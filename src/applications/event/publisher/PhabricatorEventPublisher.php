<?php

/*
 * Copyright 2012 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

final class PhabricatorEventPublisher {

  private $storyType;
  private $storyData;
  private $storyTime;
  private $storyAuthorPHID;
  private $primaryObjectPHID;
  private $subscribedPHIDs;
  private $relatedPHIDs;

  public function setRelatedPHIDs(array $phids) {
    $this->relatedPHIDs = $phids;
    return $this;
  }

  public function setSubscribedPHIDs(array $phids) {
    $this->subscribedPHIDs = $phids;
    return $this;
  }

  public function setStoryType($story_type) {
    $this->storyType = $story_type;
    return $this;
  }

  public function setStoryData(array $data) {
    $this->storyData = $data;
    return $this;
  }

  public function setStoryTime($time) {
    $this->storyTime = $time;
    return $this;
  }

  public function setStoryAuthorPHID($phid) {
    $this->storyAuthorPHID = $phid;
    return $this;
  }

  public function setPrimaryObjectPHID($phid) {
    $this->primaryObjectPHID = $phid;
    return $this;
  }

  public function publish() {
    if (!PhabricatorEnv::getEnvConfig('notification.enabled')) {
      return null;
    }

    if (!$this->relatedPHIDs) {
      throw new Exception("There are no related PHIDs for this story!");
    }

    if (!$this->primaryObjectPHID) {
      throw new Exception("Call setPrimaryObjectPHID before publishing!");
    }

    if (!$this->subscribedPHIDs) {
      throw new Exception("Call setSubscribedPHIDs before publishing!");
    }

    if (!$this->storyType) {
      throw new Exception("Call setStoryType() before publishing!");
    }

    $chrono_key = $this->generateChronologicalKey();

    $story = id(new PhabricatorEventStoryData())
      ->setStoryType($this->storyType)
      ->setStoryData($this->storyData)
      ->setAuthorPHID($this->storyAuthorPHID)
      ->setObjectPHID($this->objectPHID)
      ->setChronologicalKey($chrono_key)
      ->save();

    $this->updateStoryReference($chrono_key);
    $this->updateNotification($chrono_key);

    $this->sendAphlictNotification();
    return $story;
  }

  private function updateStoryReference($chrono_key) {
    $ref = new PhabricatorEventStoryReference();

    $sql = array();
    $conn = $ref->establishConnection('w');
    foreach (array_unique($this->relatedPHIDs) as $phid) {
      $sql[] = qsprintf(
        $conn,
        '(%s, %s)',
        $phid,
        $chrono_key);
    }

    queryfx(
      $conn,
      'INSERT INTO %T (objectPHID, chronologicalKey) VALUES %Q',
      $ref->getTableName(),
      implode(', ', $sql));
  }

  private function updateEventNotification($chrono_key) {
    $notif = new PhabricatorEventNotification();

    $sql = array();
    $conn = $notif->establishConnection('w');

    $conn = $notif->establishConnection('w');

    foreach (array_unique($this->subscribedPHIDs) as $user_phid) {
      $sql[] = qsprintf(
        $conn,
        '(%s, %s, %s, %d)',
	$this->primaryObjectPHID,
        $user_phid,
        $chrono_key,
	false);
    }

    queryfx(
      $conn,
      'INSERT INTO %T (objectPHID, userPHID, chronologicalKey, hasViewed) VALUES %Q',
      $notif->getTableName(),
      implode(', ', $sql));
  }



  public function sendAphlictNotification() {
    // send aphlict notification based on story type

    $type = $this->storyType;
    $event_data = $this->storyData;
    $actor_phid = $this->storyAuthorPHID;
    $pathname = null;
    switch ($type) {
      case PhabricatorNotificationStoryTypeConstants::STORY_UNKNOWN:
        break;
      case PhabricatorNotificationStoryTypeConstants::STORY_STATUS:
        break;
      case PhabricatorNotificationStoryTypeConstants::STORY_DIFFERENTIAL:
        $revision_id = $event_data['revision_id'];
        $action = $event_data['action'];
        $pathname  = '/D'.$revision_id;
        id(new DifferentialNotification($revision_id, $action, $actor_phid,
          $pathname))->push();
        break;
      case PhabricatorNotificationStoryTypeConstants::STORY_PHRICTION:
        $document_id = $event_data['id'];
        $action = $event_data['action'];
        $document = id(new PhrictionDocument())->load($document_id);
        $pathname = PhrictionDocument::getSlugURI($document->getSlug());
        id(new PhrictionNotification($document_id, $action, $actor_phid,
          $pathname))->push();
        break;
      case PhabricatorNotificationStoryTypeConstants::STORY_MANIPHEST:
        $task_id = $event_data['taskID'];
        $action = $event_data['type'];
        $pathname = '/T'.$task_id;
        id(new ManiphestNotification($task_id, $action, $actor_phid,
          $pathname))->push();
        break;
      case PhabricatorNotificationStoryTypeConstants::STORY_PROJECT:
        $project_id = $event_data['projectID'];
        $action = $event_data['action'];
        $pathname = '/project/view/'.$project_id.'/';
        id(new ProjectNotification($project_id, $action, $actor_phid,
          $pathname))->push();
        break;
      case PhabricatorNotificationStoryTypeConstants::STORY_AUDIT:
        $commit_id = $event_data['commitID'];
        $action = $event_data['action'];
        $pathname = '/'.$this->commit->getCommitIdentifier();
        id(new AuditNotification($commit_id, $action, $actor_phid,
          $pathname))->push();
        break;
      default:
        break;
    }
    id(new RefreshNotification($actor_phid, $pathname))->push();
  }

  /**
   * We generate a unique chronological key for each story type because we want
   * to be able to page through the stream with a cursor (i.e., select stories
   * after ID = X) so we can efficiently perform filtering after selecting data,
   * and multiple stories with the same ID make this cumbersome without putting
   * a bunch of logic in the client. We could use the primary key, but that
   * would prevent publishing stories which happened in the past. Since it's
   * potentially useful to do that (e.g., if you're importing another data
   * source) build a unique key for each story which has chronological ordering.
   *
   * @return string A unique, time-ordered key which identifies the story.
   */
  private function generateChronologicalKey() {
    // Use the epoch timestamp for the upper 32 bits of the key. Default to
    // the current time if the story doesn't have an explicit timestamp.
    $time = nonempty($this->storyTime, time());

    // Generate a random number for the lower 32 bits of the key.
    $rand = head(unpack('L', Filesystem::readRandomBytes(4)));

    // On 32-bit machines, we have to get creative.
    if (PHP_INT_SIZE < 8) {
      // We're on a 32-bit machine.
      if (function_exists('bcadd')) {
        // Try to use the 'bc' extension.
        return bcadd(bcmul($time, bcpow(2, 32)), $rand);
      } else {
        // Do the math in MySQL. TODO: If we formalize a bc dependency, get
        // rid of this.
        $conn_r = id(new PhabricatorFeedStoryData())->establishConnection('r');
        $result = queryfx_one(
          $conn_r,
          'SELECT (%d << 32) + %d as N',
          $time,
          $rand);
        return $result['N'];
      }
    } else {
      // This is a 64 bit machine, so we can just do the math.
      return ($time << 32) + $rand;
    }
  }
}


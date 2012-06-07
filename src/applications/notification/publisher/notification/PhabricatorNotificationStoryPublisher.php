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

final class PhabricatorNotificationStoryPublisher
  extends PhabricatorStoryPublisher{

  private $primaryObjectPHID;


  public function setPrimaryObjectPHID($phid) {
    $this->primaryObjectPHID = $phid;
    return $this;
  }

  public function publish() {
    if (!PhabricatorEnv::getEnvConfig('notification.enabled')) {
      return $this;
    }

    if (!$this->primaryObjectPHID) {
      throw new Exception("Call setPrimaryObjectPHID() before publishing!");
    }

    if (!$this->storyType) {
      throw new Exception("Call setStoryType() before publishing!");
    }


    $this->insertNotifications($this->chronologicalKey);
    //$this->sendAphlictNotification();
    return $this;
  }

  private function insertNotifications($chrono_key) {
    $notif = new PhabricatorFeedStoryNotification();
    $sql = array();
    $conn = $notif->establishConnection('w');

    foreach (array_unique($this->subscribedPHIDs) as $user_phid) {
      $sql[] = qsprintf(
        $conn,
        '(%s, %s, %s, %d)',
        $this->primaryObjectPHID,
        $user_phid,
        $chrono_key,
        0);
    }

    queryfx(
      $conn,
      'INSERT INTO %T
       (primaryObjectPHID, userPHID, chronologicalKey, hasViewed)
       VALUES %Q',
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
}


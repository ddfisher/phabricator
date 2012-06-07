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

abstract class PhabricatorStoryPublisher {

  protected $storyType;
  protected $storyData;
  protected $storyTime;
  protected $storyAuthorPHID;
  protected $subscribedPHIDs;
  protected $chronologicalKey;

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

  /* not as nice as being able to generate it privately here,
     but the notification and the feed item need to share the
     same chronological key so it must be passed in by the editor
  */
  public function setChronologicalKey($chrono_key) {
    $this->chronologicalKey = $chrono_key;
    return $this;
  }

  abstract public function publish();

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
  public static function generateChronologicalKey($story_time) {
    // Use the epoch timestamp for the upper 32 bits of the key. Default to
    // the current time if the story doesn't have an explicit timestamp.
    $time = nonempty($story_time, time());

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

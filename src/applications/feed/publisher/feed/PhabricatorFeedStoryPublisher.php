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

final class PhabricatorFeedStoryPublisher
  extends PhabricatorStoryPublisher {

  private $relatedPHIDs;

  public function setRelatedPHIDs(array $phids) {
    $this->relatedPHIDs = $phids;
    return $this;
  }

  public function publish() {
    if (!$this->relatedPHIDs) {
      throw new Exception("There are no PHIDs related to this story!");
    }

    if (!$this->storyType) {
      throw new Exception("Call setStoryType() before publishing!");
    }

    $story = new PhabricatorFeedStoryData();
    $story->setStoryType($this->storyType);
    $story->setStoryData($this->storyData);
    $story->setAuthorPHID($this->storyAuthorPHID);
    $story->setChronologicalKey($this->chronologicalKey);
    $story->save();

    $ref = new PhabricatorFeedStoryReference();

    $sql = array();
    $conn = $ref->establishConnection('w');
    foreach (array_unique($this->relatedPHIDs) as $phid) {
      $sql[] = qsprintf(
        $conn,
        '(%s, %s)',
        $phid,
        $this->chronologicalKey);
    }

    queryfx(
      $conn,
      'INSERT INTO %T (objectPHID, chronologicalKey) VALUES %Q',
      $ref->getTableName(),
      implode(', ', $sql));

    return $story;
  }
}

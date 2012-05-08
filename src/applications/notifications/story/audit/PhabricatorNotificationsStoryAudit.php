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

final class PhabricatorNotificationsStoryAudit
    extends PhabricatorNotificationsStory {

  public function getRequiredHandlePHIDs() {
    return array(
      $this->getStoryData()->getAuthorPHID(),
      $this->getStoryData()->getValue('commitPHID'),
    );
  }

  public function getRequiredObjectPHIDs() {
    return array();
  }

  public function renderView() {
    $data = $this->getStoryData();

    $view = new PhabricatorNotificationsStoryView();

    $view->setEpoch($data->getEpoch());
    $view->setOneLineStory(true);
    $view->setConsumed($this->getConsumed());

    $view->setTitle($this->one_line_for_data($data));

    return $view;
  }

  function one_line_for_data($data) {
    $action = $data->getValue('action');
    $verb = PhabricatorAuditActionConstants::getActionPastTenseVerb($action);

    $author_phid = $data->getAuthorPHID();
    $commit_phid = $data->getValue('commitPHID');

    $author_link = $this->linkTo($author_phid);
    $commit_link = $this->linkTo($commit_phid);

    return $author_link . " {$verb} commit " . $commit_link;
  }
}

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

class PhabricatorNotificationStoryPhriction
extends PhabricatorNotificationStory {

  public function getRequiredHandlePHIDs() {
    return array(
      $this->getStoryData()->getAuthorPHID(),
      $this->getStoryData()->getValue('phid'),
    );
  }

  public function renderView() {
    $data = $this->getStoryData();

    $view = new PhabricatorNotificationStoryView();
    $view->setEpoch($data->getEpoch());
    $view->setOneLineStory(true);
    $view->setConsumed($this->getConsumed());
    $view->setTitle($this->lineForData($data));

    return $view;

  }

  function lineForData($data) {
    $author_phid = $data->getAuthorPHID();
    $document_phid = $data->getValue('phid');

    $action = $data->getValue('action');
    $author_link = $this->linkTo($author_phid);
    $document_link = $this->linkTo($document_phid);

    $verb = PhrictionActionConstants::getActionPastTenseVerb($action);

    //TODO: Check for revert's and print edit message of document
    $one_line = "{$author_link} {$verb} document {$document_link}";
    return $one_line;
  }
}


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

final class PhabricatorNotificationsStoryDifferential
 extends PhabricatorNotificationsStory {
   
  public function getRequiredHandlePHIDs() {
    $data = $this->getStoryData();
    return array(
      $this->getStoryData()->getAuthorPHID(),
      $data->getValue('revision_phid'),
      $data->getValue('revision_author_phid'),
    );
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
    $author_phid = $data->getAuthorPHID();
    $revision_phid = $data->getValue('revision_phid');
    $action = $data->getValue('action');
    //set as summary or comment
    $feedback_content = $data->getValue('feedback_content');

    $author_link = $this->linkTo($author_phid);
    $revision_link = $this->linkTo($revision_phid);
    switch($action) {
    case 'abandon':
      return "{$author_link} abandoned {$revision_link}";
    case 'create':
      return "{$author_link} created {$revision_link}";
    case 'none':
      return "{$author_link} commented on {$revision_link} \"{$feedback_content}\"";
    case 'rethink':
      return "{$author_link} planned changes to {$revision_link}";
    case 'update': 
      return "{$author_link} updated {$revision_link}";
    default:
      return "[ ".
	"action: {$action}, ".
	"author: {$author_phid}, ".
	"revision: {$revision_phid} ".
	"feedback_content: {$feedback_content}";
	"]";
    }
  }
}

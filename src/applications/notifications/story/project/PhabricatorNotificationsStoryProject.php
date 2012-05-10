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

class PhabricatorNotificationsStoryProject extends PhabricatorNotificationsStory {
    public function getRequiredHandlePHIDs() {
      return array(
        $this->getStoryData()->getAuthorPHID(),
        $this->getStoryData()->getValue('projectPHID'),
      );
    }

    public function renderView() {
      $data = $this->getStoryData();

      $view = new PhabricatorNotificationsStoryView();
      $view->setTitle($this->lineForData($data));
      $view->setOneLineStory(true);
      $view->setConsumed($this->getConsumed());


      return $view;
    }

    function lineForData($data) {
      $action = $data->getValue('type');
      $old = $data->getValue('old');
      $new = $data->getValue('new');
      $proj_phid = $data->getValue('projectPHID');
      $author_phid = $data->getAuthorPHID();

      $author_link = $this->linkTo($author_phid);
      $proj_link = $this->linkTo($proj_phid);
      switch ($action) {
      case 'name':
        if ($old) {
          return "{$author_link} renamed project {$old} to {$proj_link}";
        } else {
          return "{$author_link} created project {$proj_link}";
        }
      default:
        return '['.
          "action: {$action}, ".
          "old: {$old}, ".
          "new: {$new}, ".
          "proj_phid: {$proj_link}, ".
          "author_phid: {$author_link}".
          "]";
      }
    }
}


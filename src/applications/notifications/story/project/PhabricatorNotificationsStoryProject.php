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
      $action = $data->getValue('action');
      $old = $data->getValue('old');
      $new = $data->getValue('new');
      $proj_phid = $data->getValue('projectPHID');
      $author_phid = $data->getAuthorPHID();

      $author_link = $this->linkTo($author_phid);
      $proj_link = $this->linkTo($proj_phid);
      $verb = PhabricatorProjectAction::getActionPastTenseVerb($action);

      $one_line = "{$author_link} {$verb} {$proj_link}";

      switch ($action) {
        case PhabricatorProjectAction::ACTION_RENAME:
          $one_line .= ' from '.
            $this->renderString($old);
          break;
        case PhabricatorProjectAction::ACTION_STATUS:
          $one_line .= ' from '.
           $this->renderString(
             PhabricatorProjectStatus::getNameForStatus($old)).
               ' to '.
           $this->renderString(
             PhabricatorProjectStatus::getNameForStatus($new)).
               '.';
           break;
        case PhabricatorProjectAction::ACTION_ADD_MEMBERS:
          $one_line .= ' '.
            $this->renderHandleList(
              array_diff(
                $new,
                $old
              )
            );
          break;
        case PhabricatorProjectAction::ACTION_REMOVE_MEMBERS:
          $one_line .= ' '.
            $this->renderHandleList(
              array_diff(
                $old,
                $new
              )
            );
          break;
       case PhabricatorProjectAction::ACTION_CHANGE_MEMBERS:
         $one_line .= ' added '.
           $this->renderHandleList(
             array_diff(
               $new,
               $old
              )
           );
         $one_line .= ' removed '.
           $this->renderHandleList(
             array_diff(
               $new,
               $old
              )
            );
          break;
      }
      return $one_line;
    }

}


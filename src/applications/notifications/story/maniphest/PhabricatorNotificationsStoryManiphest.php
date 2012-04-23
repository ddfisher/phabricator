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

final class PhabricatorNotificationsStoryManiphest
 extends PhabricatorNotificationsStory {


  public function getRequiredHandlePHIDs() {
    $data = $this->getStoryData();
    return array_filter(
        array(
        $this->getStoryData()->getAuthorPHID(),
        $data->getValue('taskPHID'),
        $data->getValue('ownerPHID'),
      ));
  }

  public function renderView() {
    $data = $this->getStoryData();
    $author_phid = $data->getAuthorPHID();
    $owner_phid = $data->getValue('ownerPHID');
    $task_phid = $data->getValue('taskPHID');
    $action = $data->getValue('type');
    $description = $data->getValue('description');
    
    $view = new PhabricatorNotificationsStoryView();

    
    
    $view->setEpoch($data->getEpoch());
    $view->setOneLineStory(true);

    $verb = ManiphestAction::getActionPastTenseVerb($action);

    /* $title =  */
    /*   $this->linkTo($author_phid). */
    /*   " {$verb} task ". */
    /*   $this->linkTo($task_phid); */
    /* $title .= '.'; */

    $view->setTitle($this->one_line_for_data($data));
    return $view;
  }

  function one_line_for_data($data) {
    $actor_phid = $data->getAuthorPHID();
    $owner_phid = $data->getValue('ownerPHID');
    $task_phid = $data->getValue('taskPHID');
    $action = $data->getValue('type');
    $description = $data->getValue('description');

    
    $actor_link = $this->linkTo($actor_phid);
    $task_link = $this->linkTo($task_phid);

    switch ($action) {
    case 'comment':
      return "{$actor_link} commented on {$task_link}";
    case 'ccs':
      return "{$actor_link} added cc's to {$task_link}";
    case 'priority':
      return "{$actor_link} changed the priority of {$task_link}";
    case 'projects':
      return "{$actor_link} added projects to {$task_link}";

    default:
      return '['.
	'actor: '.$actor_phid.", ".
	'owner: '.$owner_phid.", ".
	'task: '.$task_phid.", ".
	'action: '.$action.", ".
	'description: '.$description.
	']';
    }
  }

}

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

    $view = new PhabricatorNotificationsStoryView();

    $view->setEpoch($data->getEpoch());
    $view->setOneLineStory(true);
    $view->setConsumed($this->getConsumed());

    $view->setTitle($this->one_line_for_data($data));
    return $view;
  }

  function one_line_for_data($data) {
    $actor_phid = $data->getAuthorPHID();
    $owner_phid = $data->getValue('ownerPHID');
    $task_phid = $data->getValue('taskPHID');
    $action = $data->getValue('type');
    $description = $data->getValue('description');
    $comments = phutil_utf8_shorten($data->getValue('comments'), 140);
    //todo, cut the length of comment off
    $actor_link = $this->linkTo($actor_phid);

    $task_link = $this->linkTo($task_phid);
    $owner_link = $this->linkTo($owner_phid);

    switch ($action) {
    case 'assign':
      return "{$actor_link} assigned {$task_link} to {$owner_link}";
    case 'create':
      return "{$actor_link} created {$task_link}";
    case 'comment':
      return  "{$actor_link} commented on {$task_link} \"{$comments}\"";
    case 'ccs':
      return "{$actor_link} added cc's to {$task_link}";
    case 'close':
      return "{$actor_link} closed {$task_link}";
    case 'priority':
      return "{$actor_link} changed the priority of {$task_link}";
    case 'projects':
      return "{$actor_link} added projects to {$task_link}";
    /*TODO remove this, it's only here for things generate
      improperly in the past */
    case 'status':
      return "{$actor_link} updated task {$task_link}";
    case 'title':
      return "{$actor_link} updated title of {$task_link}";
    case 'description':
      return "{$actor_link} updated description of {$task_link} to \"{$description}\"";
    case 'reassign':
      return "{$actor_link} reassigned task {$task_link} to {$owner_link}";
    case 'attach':
      return "{$actor_link} attached something to {$task_link}";


    default:
      return '['.
        'actor: '.$actor_link.", ".
        'owner: '.$owner_link.", ".
        'task: '.$task_link.", ".
        'action: '.$action.", ".
        'description: '.$description.
        ']';
    }
  }

}

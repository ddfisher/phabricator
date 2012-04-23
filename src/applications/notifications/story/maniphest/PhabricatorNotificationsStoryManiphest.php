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

  public function renderView() {
    $data = $this->getStoryData();

    $view = new PhabricatorNotificationsStoryView();

    $view->setTitle('Maniphest Story');
    $view->setEpoch($data->getEpoch());

    $view->setOneLineStory($this->message_for_data($data));

    $view->appendChild(
      'This is an notification feed story of type '.
      '"'.phutil_escape_html($data->getStoryType()).'".');

    return $view;
  }

  function message_for_data($data) {
    $actor_phid = $data->getAuthorPHID();
    $owner_phid = $data->getValue('ownerPHID');
    $task_phid = $data->getValue('taskPHID');
    $action = $data->getValue('type');
    $description = $data->getValue('description');

    $user = id(new PhabricatorUser())->loadOneWhere(
      'phid = %s',
      $actor_phid);
    $username = $user->getUserName();

    $task = id(new ManiphestTask())->loadOneWhere(
      'phid = %s',
      $task_phid);

    switch ($action) {
    case 'comment':
      return sprintf('%s commented "%s" on Task:%s',
	$username,
	$description,
	$task->getTitle());

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

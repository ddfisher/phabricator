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

final class PhabricatorNotificationsStoryView
extends PhabricatorNotificationsView {

  private $title;
  private $phid;
  private $epoch;
  private $oneLine;
  private $viewer;
  private $consumed;

  public function setViewer(PhabricatorUser $viewer) {
    $this->viewer = $viewer;
    return $this;
  }

  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  public function setEpoch($epoch) {
    $this->epoch = $epoch;
    return $this;
  }

  public function setOneLineStory($one_line) {
    $this->oneLine = $one_line;
    return $this;
  }
  
  public function setConsumed($is_consumed) {
    $this->consumed = $is_consumed;
  }

  

  public function render() {

    $title = $this->title;
    if(!$this->consumed) {
      $title = '<b>'.$title.'</b>';
    }

    $head = phutil_render_tag(
      'div',
      array(
	'class' => 'phabricator-notifications-story-head',
      ),
      nonempty($title, 'Untitled Story'));
      

    return phutil_render_tag(
      'div',
      array(
	'class' =>
	'phabricator-notifications '.
	'phabricator-notifications-story-one-line'),
      $head);
  }

}

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

abstract class PhabricatorNotificationsStory {

  private $data;
  private $handles;
  private $framed;
  private $consumed;
  private $lastViewed;
  private $viewer;


  final public function __construct(PhabricatorNotificationsStoryData
    $data) {
    $this->data = $data;
  }

  public function getRequiredHandlePHIDs() {
    return array();
  }

  abstract public function renderView();

  final public function getStoryData() {
    return $this->data;
  }

  final public function getEpoch() {
    return $this->getStoryData()->getEpoch();
  }

  final public function getChronologicalKey() {
    return $this->getStoryData()->getChronologicalKey();
  }

  final protected function linkTo($phid) {
    $handle = $this->getHandle($phid);

    return phutil_render_tag(
      'a',
      array(
        'href'    => $handle->getURI(),
      ),
      phutil_escape_html($handle->getLinkName()));
  }


  final public function setHandles(array $handles) {
    $this->handles = $handles;
    return $this;
  }

  final protected function getHandle($phid) {
    if (isset($this->handles[$phid])) {
      if ($this->handles[$phid] instanceof PhabricatorObjectHandle) {
        return $this->handles[$phid];
      }
    }


    $handle = new PhabricatorObjectHandle();
    $handle->setPHID($phid);
    $handle->setName("Unloaded Object '{$phid}'");

    return $handle;
  }

  final public function getConsumed() {
    return $this->consumed;
  }

  final public function setConsumed($consumed) {
    $this->consumed = $consumed;
    return $this;
  }

  final public function setViewer(PhabricatorUser $user) {
    $this->viewer = $user;
  }

  final public function loadLastViewed() {
    if (!$this->viewer) {
      throw new Exception('You must call setViewer first!');
    }

    $objects = id(new PhabricatorNotificationsSubscribed())->loadAllWhere(
      "userPHID = %s AND objectPHID = %s",
      $this->viewer->getPHID(),
      $this->data->getObjectPHID()
      );


    $newest_subscription = last(msort($objects, 'getLastViewed'));
    $this->lastViewed = $newest_subscription->getLastViewed();

  }

  final public function getLastViewed() {
    if (!$this->lastViewed) {
      throw new Exception('You must call setLastViewed first!');
    }
    return $this->lastViewed;
  }

}

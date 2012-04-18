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

final class DifferentialNotification extends AphlictNotification{

  const PATHNAME = 'pathname';
  // private $revision;
  private $pagePathname;
  private $actor;
  private $message;

  function __construct($revision_id, $action, $actor_phid) {
    $this->revision = id(new DifferentialRevision())->load($revision_id);
    $this->pagePathname = '/D'.$this->revision->getID();
    $this->actor = id(new PhabricatorUser())->loadOneWhere(
      'PHID = %s',
      $actor_phid);
    $this->message = $this->generateMessage($action);
    return $this;
  }

  function generateMessage($action) {
    $username = $this->actor->getUserName();
    $verb = DifferentialAction::getActionPastTenseVerb($action);
    return sprintf("%s %s %s",
      $username,
      $verb,
      substr($this->pagePathname, 1));
  }

  public function push() {
    $this->setData(array(NotificationType::KEY => NotificationType::GENERIC,
      NotificationMessage::KEY => $this->message,
      self::PATHNAME => $this->pagePathname));
    $this->sendPostRequest();
    return $this;
  }
}

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

final class RefreshNotification extends AphlictNotification {

  private $updaterPhid;

  function __construct($actor_phid, $pathname) {
    $this->updaterPhid = $actor_phid;
    $this->pagePathname = $pathname;
    return $this;
  }

  public function push() {
    $user = id(new PhabricatorUser())->loadOneWhere(
      'phid = %s',
      $this->updaterPhid);
    $username = $user->getUserName();
    $message = sprintf("Page updated by %s", $username);
    $this->setData(array(NotificationType::KEY => NotificationType::REFRESH,
      NotificationMessage::KEY => $message,
      NotificationPathname::KEY => $this->pagePathname));
    $this->sendPostRequest();
    return $this;
  }

}


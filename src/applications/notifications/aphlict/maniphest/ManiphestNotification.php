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

final class ManiphestNotification extends AphlictNotification{

  const PATHNAME = 'pathname';
  private $message;
  private $pagePathname;

  function __construct($task_phid, $transaction, $pathname) {
    $this->pagePathname = $pathname;
    $this->message = $this->message_for_transaction($transaction);
    return $this;
  }

  function message_for_transaction($transaction) {
    $type = $transaction->getTransactionType();
    $actor_phid = $transaction->getAuthorPHID();
    $user = id(new PhabricatorUser())->loadOneWhere(
           'phid = %s',
           $actor_phid);
    $username = $user->getUserName();
    switch($type) {
    case ManiphestTransactionType::TYPE_NONE:
      return sprintf("%s commented on by %s",
        substr($this->pagePathname,1),
        $username);
    default:
      return "NO MESSAGE SET FOR TYPE:".$type;
    }
    return $actor_phid;
  }

  public function push() {
    $this->setData(array(NotificationType::KEY => NotificationType::GENERIC,
      NotificationMessage::KEY => $this->message,
      self::PATHNAME => $this->pagePathname));
    $this->sendPostRequest();
    return $this;
  }
}


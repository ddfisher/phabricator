<?php

final class ManiphestNotification extends AphlictNotification{

  const PATHNAME = 'pathname';
  private $message;
  private $pagePathname;

  function __construct($task, $transaction, $pathname) {
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

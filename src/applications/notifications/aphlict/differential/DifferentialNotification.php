<?php

final class DifferentialNotification extends AphlictNotification{

  const PATHNAME = 'pathname';
  private $revision;
  private $pagePathname;
  private $actor;
  private $message;

  function __construct($revision, $action, $actor_phid) {
    $this->revision = $revision;
    $this->pagePathname = '/D'.$revision->getID();
    $this->actor = id(new PhabricatorUser())->loadOneWhere('PHID = %s', $actor_phid);;
    $this->message = $this->generateMessage($action);
    return $this;
  }

  function generateMessage($action) { 
    $username = $this->actor->getUserName();
    $verb = DifferentialAction::getActionPastTenseVerb($action);
    return sprintf("%s %s %s", $username, $verb, substr($this->pagePathname, 1));
  }
  
  public function push() {
    $this->setData(array(NotificationType::KEY => NotificationType::GENERIC,
      NotificationMessage::KEY => $this->message,
      self::PATHNAME => $this->pagePathname));			 
    $this->sendPostRequest();
    return $this;
  }
}
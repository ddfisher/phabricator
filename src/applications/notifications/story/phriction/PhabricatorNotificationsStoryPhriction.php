<?php

class PhabricatorNotificationsStoryPhriction
extends PhabricatorNotificationsStory {

  public function getRequiredHandlePHIDs() {
    return array(
      $this->getStoryData()->getAuthorPHID(),
      $this->getStoryData()->getValue('phid'),
    );
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
    $author_phid = $data->getAuthorPHID();
    $document_phid = $data->getValue('phid');

    $action = $data->getValue('action');
    $author_link = $this->linkTo($author_phid);
    $document_link = $this->linkTo($document_phid);
    switch($action) {
    case 'edit':
      return "{$author_link} edited document {$document_link}";
    case 'create':
      return "{$author_link} created document {$document_link}";
    default:
      return '['.
	'author: '.$author_phid.', '.
	'document: '.$document_phid.', '.
	'action: '.$action.
	']';
	
      
    }
  }
}
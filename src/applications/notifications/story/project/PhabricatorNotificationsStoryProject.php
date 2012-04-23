<?php

class PhabricatorNotificationsStoryProject extends PhabricatorNotificationsStory {
    public function getRequiredHandlePHIDs() {
      return array(
	$this->getStoryData()->getAuthorPHID(),
	$this->getStoryData()->getValue('projectPHID'),
      );
    }
    
    public function renderView() {
      $data = $this->getStoryData();
      
      $view = new PhabricatorNotificationsStoryView();
      $view->setTitle($this->one_line_for_data($data));
      $view->setOneLineStory(true);
      $view->setConsumed($this->getConsumed());


      return $view;
    }

    function one_line_for_data($data) {
      $action = $data->getValue('type');
      $old = $data->getValue('old');
      $new = $data->getValue('new');
      $proj_phid = $data->getValue('projectPHID');
      $author_phid = $data->getAuthorPHID();

      $author_link = $this->linkTo($author_phid);
      $proj_link = $this->linkTo($proj_phid);
      switch($action) {
      case 'name':
	if($old) {
	  return "{$author_link} renamed project {$old} to {$proj_link}";
	} else {
	  return "{$author_link} created project {$proj_link}";
	}
      default:
	return '['.
	  "action: {$action}, ".
	  "old: {$old}, ".
	  "new: {$new}, ".
	  "proj_phid: {$proj_phid}, ".
	  "author_phid: {$author_phid}".
	  "]";
      }
    }

}


<?php

final class PhabricatorVisualizationTestPageController
extends PhabricatorVisualizationController {


  public function processRequest() {
    
    $query = new PhabricatorVisualizationTaskCreateQuery();
    
    $results = $query->execute();
    
    //    $content = stringify_results($results);

    $user = id(new PhabricatorUser())->loadOneWhere("phid = %s",
	     $results[0]['authorPHID']);
    //    phlog($results[0]['numCreated']);
    $name = $user->getUserName(); 
    $numCreated = $results[0]['numCreated'];
    

    return $this->buildStandardPageResponse(
      id(new AphrontNullView())->appendChild(
	'<div id="visualization"></div>'),
      array(
	'title' => 'Visualization Test Page',
      ));
    
  }

  private function stringify_results() {

  }
}
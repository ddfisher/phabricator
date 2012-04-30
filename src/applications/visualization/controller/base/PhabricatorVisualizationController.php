<?php

abstract class PhabricatorVisualizationController
extends PhabricatorController {

  public function buildStandardPageResponse($view, array $data) {
    
    $page = $this->buildStandardPageView();

    $page->setApplicationName('Visualization');
    $page->setBaseURI('/visualization/');
    $page->setTitle(idx($data, 'title'));
    $page->setGlyph('!');
    $page->appendChild($view);

    $response = new AphrontWebpageResponse();
    return $response->setContent($page->render());
    
  }

}
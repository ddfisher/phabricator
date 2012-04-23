<?php


final class PhabricatorNotificationsBuilder {

  private $stories;
  private $user;

  public function __construct(array $stories) {
    $this->stories = $stories;
  }

  public function setUser(PhabricatorUser $user) {
    $this->user = $user;
    return $this;
  }

  public function buildView() {
    if(!$this->user) {
      throw new Exception('Call setUser() before buildView()!');
    }

    $user = $this->user;
    $stories = $this->stories;

    $handles = array();
    $objects = array();
    
    if($stories) {
      $handle_phids = array_mergev(mpull($stories, 'getRequiredHandlePHIDs'));
      $handles = id(new PhabricatorObjectHandleData($handle_phids))
        ->loadHandles();
    }
    
    $null_view = new AphrontNullView();

    foreach ($stories as $story) {
      $story->setHandles($handles);
      $story->setViewer($user);
      $story->loadConsumed();
      $date = phabricator_date($story->getEpoch(), $user);
      $view = $story->renderView();
      $view->setViewer($user);
      $null_view->appendChild($view);
    }

    return id(new AphrontNullView())->appendChild(
      '<div class="phabricator-notifications-frame">'.
        $null_view->render().
      '</div>');

  }
}
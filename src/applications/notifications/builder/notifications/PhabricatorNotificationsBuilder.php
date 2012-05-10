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
    if (!$this->user) {
      throw new Exception('Call setUser() before buildView()!');
    }

    $user = $this->user;
    $stories = $this->stories;

    $handles = array();
    $objects = array();

    if ($stories) {
      $handle_phids = array_mergev(mpull($stories, 'getRequiredHandlePHIDs'));
      $handles = id(new PhabricatorObjectHandleData($handle_phids))
        ->loadHandles();
    }

    $null_view = new AphrontNullView();

    foreach ($stories as $story) {
      $story->setHandles($handles);
      $story->setViewer($user);
      $story->loadLastViewed();

      if ($story->getLastViewed() > $story->getChronologicalKey()) {
        $story->setConsumed(true);
      }

      // $date = phabricator_date($story->getEpoch(), $user);
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

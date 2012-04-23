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

final class PhabricatorNotificationsTestPageController
  extends PhabricatorNotificationsController {

  public function processRequest() {

    $request = $this->getRequest();
    $user = $request->getUser();

    $query = new PhabricatorNotificationsQuery();
    $query->setUserPHID($user->getPHID());

    $stories = $query->execute();
    $display_data = array();

    $builder = new PhabricatorNotificationsBuilder($stories);
    $builder->setUser($user);
    $notifications_view = $builder->buildView();

    return $this->buildStandardPageResponse(
      $notifications_view,
      array(
        'title' => 'Notifications Test Page',
      ));
  }
}

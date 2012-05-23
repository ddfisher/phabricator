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

final class PhabricatorCustomNotificationController
  extends PhabricatorNotificationController {

  public function processRequest() {
    $request = $this->getRequest();

    if ($request->isFormPost()) {
      // set up event_data
      $event_data = array(
        'text' => $request->getStr('text'),
      );
      id(new PhabricatorNotificationPublisher())
        ->setStoryType(
          PhabricatorNotificationStoryTypeConstants::STORY_UNKNOWN)
        ->setStoryData($event_data)
        ->setStoryAuthorPHID($request->getUser())
        ->setObjectPHID()
        ->publish();
      return id(new AphrontRedirectResponse())
        ->setURI('/notification/custom/');
    }

    $form = new AphrontFormView();
    $form
      ->setAction('/notification/custom/')
      ->setEncType('multipart/form-data')
      ->setUser($request->getUser())
      ->appendChild(
        id(new AphrontFormTextAreaControl())
          ->setLabel('Text')
          ->setName('text')
          ->setID('text-textarea'))
      ->appendChild(
        id(new AphrontFormSubmitControl())
          ->setValue('Publish!'));

    $panel = new AphrontPanelView();
    $panel->setWidth(AphrontPanelView::WIDTH_FULL);
    $panel->setHeader('Publish Custom Notification');
    $panel->appendChild($form);

    return $this->buildStandardPageResponse(
      array(
        $panel
      ),
      array(
        'title' => 'Custom Notification',
      ));
  }


}

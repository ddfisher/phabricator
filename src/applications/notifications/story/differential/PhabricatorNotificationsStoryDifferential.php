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

final class PhabricatorNotificationsStoryDifferential
 extends PhabricatorNotificationsStory {

  public function getRequiredHandlePHIDs() {
    $data = $this->getStoryData();
    return array(
      $this->getStoryData()->getAuthorPHID(),
      $data->getValue('revision_phid'),
      $data->getValue('revision_author_phid'),
    );
  }


  public function renderView() {
    $data = $this->getStoryData();
    $view = new PhabricatorNotificationsStoryView();

    $view->setEpoch($data->getEpoch());
    $view->setOneLineStory(true);
    $view->setConsumed($this->getConsumed());

    $view->setTitle($this->lineForData($data));
    return $view;
  }

  function lineForData($data) {
    $author_phid = $data->getAuthorPHID();
    $revision_phid = $data->getValue('revision_phid');
    $action = $data->getValue('action');
    //set as summary or comment
    $feedback_content = phutil_escape_html(
      phutil_utf8_shorten(
        $data->getValue('feedback_content'),
        140)
      );
    $author_link = $this->linkTo($author_phid);
    $revision_link = $this->linkTo($revision_phid);

    $verb = DifferentialAction::getActionPastTenseVerb($action);
    $one_line = "{$author_link} {$verb} {$revision_link}";

    if ($feedback_content) {
      $one_line .= " \"{$feedback_content}\"";
    }

    return $one_line;
  }
}

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



abstract class AphlictNotification {
  const APHLICT_POST_URL = 'http://127.0.0.1:22281/push?';
  const TIMEOUT = 5;

  private $params;
  protected $message;
  protected $actor;
  protected $pagePathname;

  abstract public function push();

  protected function sendPostRequest() {
    $url = self::APHLICT_POST_URL.$this->dataStr;
    $future = new HTTPFuture(self::APHLICT_POST_URL,
      $this->params);
    $future->setMethod('POST');
    $future->resolve();
  }

  protected function setData($data) {
    $this->params = $data;//http_build_query($data);
  }
}




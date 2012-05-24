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

abstract class PhabricatorController extends AphrontController {

  public function shouldRequireLogin() {
    return true;
  }

  public function shouldRequireAdmin() {
    return false;
  }

  public function shouldRequireEnabledUser() {
    return true;
  }

  public function shouldRequireEmailVerification() {
    $config_key = 'auth.require-email-verification';

    $need_verify = PhabricatorEnv::getEnvConfig($config_key);
    $need_login = $this->shouldRequireLogin();

    return ($need_login && $need_verify);
  }

  final public function willBeginExecution() {

    $request = $this->getRequest();

    $user = new PhabricatorUser();

    $phusr = $request->getCookie('phusr');
    $phsid = $request->getCookie('phsid');

    if ($phusr && $phsid) {
      $info = queryfx_one(
        $user->establishConnection('r'),
        'SELECT u.* FROM %T u JOIN %T s ON u.phid = s.userPHID
          AND s.type LIKE %> AND s.sessionKey = %s',
        $user->getTableName(),
        'phabricator_session',
        'web-',
        $phsid);
      if ($info) {
        $user->loadFromArray($info);
      }
    }

    $request->setUser($user);

    if ($user->getIsDisabled() && $this->shouldRequireEnabledUser()) {
      $disabled_user_controller = newv(
        'PhabricatorDisabledUserController',
        array($request));
      return $this->delegateToController($disabled_user_controller);
    }

    if (PhabricatorEnv::getEnvConfig('darkconsole.enabled')) {
      if ($user->getConsoleEnabled() ||
          PhabricatorEnv::getEnvConfig('darkconsole.always-on')) {
        $console = new DarkConsoleCore();
        $request->getApplicationConfiguration()->setConsole($console);
      }
    }

    if ($this->shouldRequireEmailVerification()) {
      $email = $user->loadPrimaryEmail();
      if (!$email) {
        throw new Exception(
          "No primary email address associated with this account!");
      }
      if (!$email->getIsVerified()) {
        $verify_controller = newv(
          'PhabricatorMustVerifyEmailController',
          array($request));
        return $this->delegateToController($verify_controller);
      }
    }

    if ($this->shouldRequireLogin() && !$user->getPHID()) {
      $login_controller = newv('PhabricatorLoginController', array($request));
      return $this->delegateToController($login_controller);
    }

    if ($this->shouldRequireAdmin() && !$user->getIsAdmin()) {
      return new Aphront403Response();
    }

  }

  public function buildStandardPageView() {
    $view = new PhabricatorStandardPageView();
    $view->setRequest($this->getRequest());

    if ($this->shouldRequireAdmin()) {
      $view->setIsAdminInterface(true);
    }

    return $view;
  }

  public function buildStandardPageResponse($view, array $data) {
    $page = $this->buildStandardPageView();
    $page->appendChild($view);
    $response = new AphrontWebpageResponse();
    $response->setContent($page->render());
    return $response;
  }

  /**
   * We generate a unique chronological key for each story type because we want
   * to be able to page through the stream with a cursor (i.e., select stories
   * after ID = X) so we can efficiently perform filtering after selecting data,
   * and multiple stories with the same ID make this cumbersome without putting
   * a bunch of logic in the client. We could use the primary key, but that
   * would prevent publishing stories which happened in the past. Since it's
   * potentially useful to do that (e.g., if you're importing another data
   * source) build a unique key for each story which has chronological ordering.
   *
   * @return string A unique, time-ordered key which identifies the story.
   */
  protected function generateChronologicalKey() {
    // Use the epoch timestamp for the upper 32 bits of the key. Default to
    // the current time if the story doesn't have an explicit timestamp.
    $time = time();

    // Generate a random number for the lower 32 bits of the key.
    $rand = head(unpack('L', Filesystem::readRandomBytes(4)));

    // On 32-bit machines, we have to get creative.
    if (PHP_INT_SIZE < 8) {
      // We're on a 32-bit machine.
      if (function_exists('bcadd')) {
        // Try to use the 'bc' extension.
        return bcadd(bcmul($time, bcpow(2, 32)), $rand);
      } else {
        // Do the math in MySQL. TODO: If we formalize a bc dependency, get
        // rid of this.
        $conn_r = id(new PhabricatorFeedStoryData())->establishConnection('r');
        $result = queryfx_one(
          $conn_r,
          'SELECT (%d << 32) + %d as N',
          $time,
          $rand);
        return $result['N'];
      }
    } else {
      // This is a 64 bit machine, so we can just do the math.
      return ($time << 32) + $rand;
    }
  }

}

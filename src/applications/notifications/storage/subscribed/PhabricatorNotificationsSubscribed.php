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

final class PhabricatorNotificationsSubscribed
  extends PhabricatorNotificationsDAO {

  protected $userPHID;
  protected $objectPHID;
  protected $lastViewed;


  public function getConfiguration() {
    return array(
      self::CONFIG_IDS          => self::IDS_MANUAL,
      self::CONFIG_TIMESTAMPS   => false,
    ) + parent::getConfiguration();
  }

  public function updateLastViewed($userPHID, $objectPHID, $lastViewed) {
    $unguarded = AphrontWriteGuard::beginScopedUnguardedWrites();
    $sub = $this->loadOneWhere("userPHID = %s AND objectPHID = %s",
	   $userPHID,
	   $objectPHID);
    
    if($sub) {
      $sub->setLastViewed($lastViewed)
	->update();
    }
    unset($unguarded);
  }
}
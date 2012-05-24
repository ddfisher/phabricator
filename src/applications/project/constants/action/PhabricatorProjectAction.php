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

final class PhabricatorProjectAction
  extends PhabricatorProjectConstants {

  const ACTION_CREATE         = 'create';
  const ACTION_MEMBERS        = 'members';
  const ACTION_STATUS         = 'status';
  const ACTION_RENAME         = 'rename';
  const ACTION_JOIN           = 'join';
  const ACTION_LEAVE          = 'leave';
  const ACTION_ADD_MEMBERS    = 'add members';
  const ACTION_REMOVE_MEMBERS = 'remove';
  const ACTION_CHANGE_MEMBERS = 'change';

  public static function getActionPastTenseVerb($action) {
    static $map = array(
      self::ACTION_CREATE        => 'created',
      self::ACTION_MEMBERS       => 'updated members of',
      self::ACTION_STATUS        => 'updated status of',
      self::ACTION_RENAME        => 'changed name of',
      self::ACTION_JOIN          => 'joined',
      self::ACTION_LEAVE         => 'left',
      self::ACTION_ADD_MEMBERS    => 'added members to',
      self::ACTION_REMOVE_MEMBERS => 'removed members from',
      self::ACTION_CHANGE_MEMBERS => 'changed members of',
    );

    return idx($map, $action, "brazenly {$action}'d");
  }




}

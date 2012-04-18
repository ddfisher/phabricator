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

final class PhabricatorNotificationsStoryTypeConstants
  extends PhabricatorNotificationsConstants {

  const STORY_UNKNOWN       = 'PhabricatorNotificationsStoryUnknown';
  // Not sure when STORY_STATUS gets used
  const STORY_STATUS        = 'PhabricatorNotificationsStoryStatus';
  const STORY_DIFFERENTIAL  = 'PhabricatorNotificationsStoryDifferential';
  const STORY_PHRICTION     = 'PhabricatorNotificationsStoryPhriction';
  const STORY_MANIPHEST     = 'PhabricatorNotificationsStoryManiphest';
  const STORY_PROJECT       = 'PhabricatorNotificationsStoryProject';
  const STORY_AUDIT         = 'PhabricatorNotificationsStoryAudit';

}

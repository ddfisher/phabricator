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

final class PhabricatorNotificationStoryTypeConstants
  extends PhabricatorNotificationConstants {

  const STORY_UNKNOWN       = 'PhabricatorNotificationStoryUnknown';
  // Not sure when STORY_STATUS gets used
  const STORY_STATUS        = 'PhabricatorNotificationStoryStatus';
  const STORY_DIFFERENTIAL  = 'PhabricatorNotificationStoryDifferential';
  const STORY_PHRICTION     = 'PhabricatorNotificationStoryPhriction';
  const STORY_MANIPHEST     = 'PhabricatorNotificationStoryManiphest';
  const STORY_PROJECT       = 'PhabricatorNotificationStoryProject';
  const STORY_AUDIT         = 'PhabricatorNotificationStoryAudit';

}

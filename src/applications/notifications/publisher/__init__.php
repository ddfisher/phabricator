<?php
/**
 * This file is automatically generated. Lint this module to rebuild it.
 * @generated
 */



phutil_require_module('phabricator', 'applications/feed/storage/story');
phutil_require_module('phabricator', 'applications/maniphest/storage/task');
phutil_require_module('phabricator', 'applications/maniphest/storage/transaction');
phutil_require_module('phabricator', 'applications/notifications/aphlict/differential');
phutil_require_module('phabricator', 'applications/notifications/aphlict/maniphest');
phutil_require_module('phabricator', 'applications/notifications/aphlict/refresh');
phutil_require_module('phabricator', 'applications/notifications/constants/story');
phutil_require_module('phabricator', 'applications/notifications/storage/storydata');
phutil_require_module('phabricator', 'applications/notifications/storage/subscribed');
phutil_require_module('phabricator', 'applications/phriction/storage/document');
phutil_require_module('phabricator', 'storage/queryfx');

phutil_require_module('phutil', 'filesystem');
phutil_require_module('phutil', 'utils');


phutil_require_source('PhabricatorNotificationsPublisher.php');

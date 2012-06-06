CREATE TABLE if not exists phabricator_feed.feed_storynotification (
  userPHID varchar(64) not null collate utf8_bin,
  primaryObjectPHID varchar(64) not null collate utf8_bin,
  notificationChronologicalKey BIGINT UNSIGNED NOT NULL,
  hasViewed boolean not null,
  UNIQUE KEY (userPHID, notificationChronologicalKey),
  KEY (userPHID, hasViewed, primaryObjectPHID)
);

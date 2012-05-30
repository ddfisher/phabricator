create database if not exists phabricator_event;


drop table if exists phabricator_event.event_storydata;

create table if not exists phabricator_event.event_storydata(
  id INT UNSIGNED not null AUTO_INCREMENT PRIMARY KEY,
  phid varchar(64) not null collate utf8_bin,
  chronologicalKey bigint unsigned NOT NULL,
  storyType varchar(64) not null collate utf8_general_ci,
  storyData longtext not null collate utf8_bin,
  authorPHID varchar(64) not null collate utf8_bin,
  dateCreated int UNSIGNED NOT NULL,
  dateModified int UNSIGNED NOT NULL,
  UNIQUE KEY (phid),
  UNIQUE KEY (chronologicalKey)
);


drop table if exists phabricator_event.event_storyreference;
CREATE TABLE if not exists phabricator_event.event_storyreference (
  objectPHID varchar(64) not null collate utf8_bin,
  chronologicalKey BIGINT UNSIGNED NOT NULL,
  UNIQUE KEY (objectPHID, chronologicalKey),
  KEY (chronologicalKey)
);

drop table if exists phabricator_event.event_notification;
CREATE TABLE if not exists phabricator_event.event_notification (
  primaryObjectPHID varchar(64) not null collate utf8_bin,
  userPHID varchar(64) not null collate utf8_bin,
  chronologicalKey BIGINT UNSIGNED NOT NULL,  
  hasViewed boolean not null,
  UNIQUE KEY (chronologicalKey),
  KEY(userPHID)
);

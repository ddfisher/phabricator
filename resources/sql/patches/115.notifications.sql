create database if not exists phabricator_notifications;

create table phabricator_notifications.notifications_storydata(
  id int unsigned not null auto_increment primary key,
  phid varchar(64) binary not null,
  unique key (phid),
  chronologicalKey bigint unsigned not null,
  unique key (chronologicalKey),
  storyType varchar(64) not null,
  storyData longblob not null,
  authorPHID varchar(64) binary not null,
  objectPHID varchar(64) binary not null,
  dateCreated int unsigned not null,
  dateModified int unsigned not null
);

create table phabricator_notifications.notifications_subscribed(
  userPHID varchar(64) binary not null,
  objectPHID varchar(64) binary not null,
  lastViewed bigint unsigned not null,
  key (userPHID)
);

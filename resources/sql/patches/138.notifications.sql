create database if not exists {$NAMESPACE}_notifications;

create table if not exists {$NAMESPACE}_notifications.notifications_storydata(
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

drop table if exists {$NAMESPACE}_notifications.notifications_subscribed;

create table {$NAMESPACE}_notifications.notifications_subscribed(
  id int unsigned not null auto_increment primary key,
  userPHID varchar(64) binary not null,
  objectPHID varchar(64) binary not null,
  lastViewed bigint unsigned not null,
  dateCreated int unsigned not null,
  dateModified int unsigned not null,
  unique key (userPHID, objectPHID),
  key(userPHID)
);

insert into {$NAMESPACE}_notifications.notifications_subscribed
  (userPHID, objectPHID, lastViewed)
  select subscriberPHID , taskPHID, 1
  from {$NAMESPACE}_maniphest.maniphest_tasksubscriber
  where not subscriberPHID=''
  	and not taskPHID='';

insert into {$NAMESPACE}_notifications.notifications_subscribed
  (userPHID, objectPHID, lastViewed)
  select distinct userPHID, objectPHID, 1
  from (select rel.objectPHID as userPHID, rev.phid as objectPHID
       	from {$NAMESPACE}_differential.differential_revision rev, 
	     {$NAMESPACE}_differential.differential_relationship  rel
	where rev.id = rel.revisionID) A;

insert ignore into {$NAMESPACE}_notifications.notifications_subscribed
  (userPHID, objectPHID, lastViewed)
  select authorPHID, phid, 1
  from {$NAMESPACE}_differential.differential_revision;

insert into {$NAMESPACE}_notifications.notifications_subscribed
  (userPHID, objectPHID, lastViewed)
  select distinct authorPHID, phid, 1
  from {$NAMESPACE}_phriction.phriction_content con,
       {$NAMESPACE}_phriction.phriction_document doc
  where doc.id = con.documentID;






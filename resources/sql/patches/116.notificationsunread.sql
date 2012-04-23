ALTER TABLE phabricator_notifications.notifications_subscribed
add consumed boolean not null;

UPDATE phabricator_notifications.notifications_subscribed
SET consumed=true;

      

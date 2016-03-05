/*
A user can create a new user profile. This will be an insert statement on the User table.
*/
INSERT INTO `TEST`.`User`
(`email`,
 `password`,
 `firstName`,
 `lastName`,
 `phone`,
 `age`)
VALUES
  ($email,
   $password,
   $firstName,
   $lastName,
   $phn,
   $age);

/*
Users will be able to search for groups of people attending events for different reasons,
such as carpooling to a concert, or meeting at the concert to dance together.
The search can be refined based of of different criteria such as their current location,
the event location, event type, etc.
This will be a select query on the Group table, Event table, User table and the Event type table.
*/
-- TODO decide how to do radius for location
SELECT
  $type,
  $userLocation,
  $eventLocation
FROM Event, EventTypeHasEvent, EventType, `Group`, User
WHERE ABS($eventLocation - $userLocation) > 10;

/*
Users will be able to add themselves to a group and attend an event. This will be an insert statement on the Goes table
*/
INSERT INTO `TEST`.`UserGoesEvent`
(`email`,
 `eventName`,
 `lat`,
 `lon`,
 `timeStart`,
 `timeEnd`)
VALUES
  ($email,
   $eventName,
   $lat,
   $lon,
   TIMESTAMP($timeStart),
   TIMESTAMP($timeEnd));

/*
Users will be able to see a history of all events that they have attended with a group.
This will be a select statement from the Goes table.
*/
SELECT
  eventName AS "Event Name",
  lat       AS "Lat",
  lon       AS "Lon",
  timeStart AS "Time Start",
  timeEnd   AS "Time End"
FROM UserGoesEvent UGE
WHERE email = $userEmail;

/*
An event provider can post events to promote them and allow users to attend.
This would be an insert statement on the Event table.
*/
INSERT INTO `TEST`.`Event`
(`eventName`,
 `lat`,
 `lon`,
 `timeStart`,
 `timeEnd`,
 `cost`,
 `description`,
 `createdBy`)
VALUES
  ($eventName,
   $lat,
   $lon,
   TIMESTAMP($timeStart),
   TIMESTAMP($timeEnd),
   $cost,
   $description,
   $createdBy);

/*
An event can also be a private event, and users would require an invitation to attend.
Invitations are sent by event providers. This would be an insert statement on the HasInvitation table.*/
INSERT INTO `TEST`.`HasInvitation`
(`invitationId`,
 `eventName`,
 `lat`,
 `lon`,
 `timeStart`,
 `timeEnd`,
 `sendToEmail`,
 `read`,
 `message`)
VALUES
  ($id,
   $eventName,
   $lat,
   $lon,
   TIMESTAMP($timeStart),
   TIMESTAMP($timeEnd),
   $sendToEmail,
   0,
   $message);

/*
Invitations are read by users, this would be a select statement on the HasInvitation table to get the invitation.
*/
SELECT
  eventName,
  lat,
  lon,
  timeStart,
  timeEnd,
  `read`,
  message
FROM HasInvitation
WHERE sendToEmail = $userEmail AND NOT `read`;

/*
An event provider can receive a report of users attending an event, the groups they are attending with,
the number of other events the users have attended, invitations that were sent out for the event, and invitations
that have not been read.
*/
CREATE OR REPLACE VIEW report.v AS
  SELECT
    `Group`.groupName,
    With.email,
    With.eventName,
    With.lat,
    With.lon,
    With.timeStart,
    With.timeEnd,
    -- SUM(IF(UserGoesEvent.email = With.email, 1, 0)) AS otherEvents,
    COUNT(UserGoesEvent.*)            AS otherEvents,
    SUM(IF(HasInvitation.read, 0, 1)) AS unreadInvites
  FROM `Group`, UserGoesEvent, Event, HasInvitation
/*
An admin can remove/edit events, users, groups, and event providers. Also they can add/remove/edit event types.
These would be delete, update or insert statements on the corresponding tables.

*/
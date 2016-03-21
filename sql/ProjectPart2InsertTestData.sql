INSERT INTO `GroupUpDebug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test1@test.com",
"password1",
"test1FirstName",
"test1LastName",
1234567);

INSERT INTO `GroupUpDebug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test2@test.com",
"password2",
"test2FirstName",
"test2LastName",
2345678);

INSERT INTO `GroupUpDebug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test3@test.com",
"password1",
"test3FirstName",
"test3LastName",
3456789);

INSERT INTO `GroupUpDebug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test4@test.com",
"password1",
"test4FirstName",
"test4LastName",
4567891);

INSERT INTO `GroupUpDebug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test5@test.com",
"password1",
"test5FirstName",
"test5LastName",
5678912);

INSERT INTO `GroupUpDebug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP1@test.com",
"password",
"test",
"EventProvider",
6789123);

INSERT INTO `GroupUpDebug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP2@test.com",
"password",
"test",
"EventProvider2",
6789124);

INSERT INTO `GroupUpDebug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP3@test.com",
"password",
"test",
"EventProvider3",
6789125);

INSERT INTO `GroupUpDebug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP4@test.com",
"password",
"test",
"EventProvider4",
6789126);

INSERT INTO `GroupUpDebug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP5@test.com",
"password",
"test",
"EventProvider5",
6789127);


INSERT INTO `GroupUpDebug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
10.89,
"test description",
"testEP1@test.com");

INSERT INTO `GroupUpDebug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 2",
49.46,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
10.90,
"test description",
"testEP2@test.com");

INSERT INTO `GroupUpDebug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 3",
49.3,
-122.9,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
10.96,
"test description",
"testEP3@test.com");


INSERT INTO `GroupUpDebug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 4",
49.1,
-128.7,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1000.08,
"test description",
"testEP4@test.com");


INSERT INTO `GroupUpDebug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 5",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP5@test.com");


INSERT INTO `GroupUpDebug`.`EventType`
(`eventTypeId`,
`category`)
VALUES
(1,
"Horror Movie");

INSERT INTO `GroupUpDebug`.`EventType`
(`eventTypeId`,
`category`)
VALUES
(2,
"Rock Concert");

INSERT INTO `GroupUpDebug`.`EventType`
(`eventTypeId`,
`category`)
VALUES
(3,
"Fight Club");

INSERT INTO `GroupUpDebug`.`EventType`
(`eventTypeId`,
`category`)
VALUES
(4,
"Study Group");

INSERT INTO `GroupUpDebug`.`EventType`
(`eventTypeId`,
`category`)
VALUES
(5,
"Wine Tasting");

INSERT INTO `GroupUpDebug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28'
);


INSERT INTO `GroupUpDebug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28'
);


INSERT INTO `GroupUpDebug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"Test Event 2",
49.46,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28'
);

INSERT INTO `GroupUpDebug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(3,
"Test Event 2",
49.46,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28'
);

INSERT INTO `GroupUpDebug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(4,
"Test Event 2",
49.46,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28'
);

INSERT INTO `GroupUpDebug`.`Group`
(`groupId`,
`groupName`,
`description`)
VALUES
(1,
"Test Group 1",
"Test Group");

INSERT INTO `GroupUpDebug`.`Group`
(`groupId`,
`groupName`,
`description`)
VALUES
(2,
"Test Group 2",
"Test Group");

INSERT INTO `GroupUpDebug`.`Group`
(`groupId`,
`groupName`,
`description`)
VALUES
(3,
"Test Group 3",
"Test Group");

INSERT INTO `GroupUpDebug`.`Group`
(`groupId`,
`groupName`,
`description`)
VALUES
(4,
"Test Group 4",
"Test Group");

INSERT INTO `GroupUpDebug`.`Group`
(`groupId`,
`groupName`,
`description`)
VALUES
(5,
"Test Group 5",
"Test Group");

INSERT INTO `GroupUpDebug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP1@test.com");

INSERT INTO `GroupUpDebug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Private Test Event 2",
49.3,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP2@test.com");


INSERT INTO `GroupUpDebug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Private Test Event 3",
3.99,
-128.10,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP3@test.com");


INSERT INTO `GroupUpDebug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Private Test Event 4",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP4@test.com");


INSERT INTO `GroupUpDebug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP5@test.com");

INSERT INTO `GroupUpDebug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser1@test.com",
"password",
"User1",
"Test",
2222222,
99);

INSERT INTO `GroupUpDebug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser2@test.com",
"password",
"User2",
"Test",
2222223,
98);

INSERT INTO `GroupUpDebug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser3@test.com",
"password",
"User3",
"Test",
2222224,
97);

INSERT INTO `GroupUpDebug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser4@test.com",
"password",
"User4",
"Test",
2222225,
99);

INSERT INTO `GroupUpDebug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser5@test.com",
"password",
"User5",
"Test",
"2222227",
14);


INSERT INTO `GroupUpDebug`.`HasInvitation`
(`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
(1,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `GroupUpDebug`.`HasInvitation`
(`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
(2,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `GroupUpDebug`.`HasInvitation`
(`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
(3,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `GroupUpDebug`.`HasInvitation`
(`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
(4,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `GroupUpDebug`.`HasInvitation`
(`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
(5,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `GroupUpDebug`.`EventProviderSendInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
1,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser1@test.com');

INSERT INTO `GroupUpDebug`.`EventProviderSendInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
2,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser2@test.com');

INSERT INTO `GroupUpDebug`.`EventProviderSendInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
3,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser3@test.com');

INSERT INTO `GroupUpDebug`.`EventProviderSendInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
4,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser4@test.com');

INSERT INTO `GroupUpDebug`.`EventProviderSendInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
5,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser5@test.com');

INSERT INTO `GroupUpDebug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser1@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser2@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser4@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser5@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`UserReadsInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser1@test.com",
1,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`UserReadsInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser2@test.com",
2,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`UserReadsInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
3,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`UserReadsInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser4@test.com",
4,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`UserReadsInvitation`
(`email`,
`invitationId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser5@test.com",
5,
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"testUser1@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"testUser2@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `GroupUpDebug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"testUser3@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"testUser4@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `GroupUpDebug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"testUser5@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


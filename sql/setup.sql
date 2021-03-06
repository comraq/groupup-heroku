/*
 * Do not have permission to drop and create db
 * on remote mysql hosted on db4free.net for heroku branch
 *
 * -- drop and create db
 * DROP DATABASE IF EXISTS group_up_debug;
 * CREATE DATABASE IF NOT EXISTS group_up_debug;
 * USE group_up_debug;
*/

-- drop existing tables
DROP TABLE IF EXISTS `EventProviderSendInvitation`;
DROP TABLE IF EXISTS `With`;
DROP TABLE IF EXISTS `Group`;
DROP TABLE IF EXISTS `HasInvitation`;
DROP TABLE IF EXISTS `PrivateEvent`;
DROP TABLE IF EXISTS `EventTypeHasEvent`;
DROP TABLE IF EXISTS `EventType`;
DROP TABLE IF EXISTS `UserGoesEvent`;
DROP TABLE IF EXISTS `User`;
DROP TABLE IF EXISTS `Admin`;
DROP TABLE IF EXISTS `Event`;
DROP TABLE IF EXISTS `EventProvider`;

-- create tables 

-- EventType(eventTypeId: int, category: char)
CREATE TABLE EventType (
    eventTypeId INT AUTO_INCREMENT,
    category VARCHAR(50) UNIQUE,
    PRIMARY KEY (eventTypeId)
);

/*
 * EventProvider(email: char, firstName: char, lastName: char, 
 * 			     companyName: char, phone: int, password: char)
 */
 CREATE TABLE EventProvider (
    email VARCHAR(50),
    password VARCHAR(60),
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    phone INT UNIQUE,
    PRIMARY KEY (email)
);

/* 
 * Event(eventName: char, lat: float, lon: float, timeStart: time, 
 * 		 timeEnd: time, cost: float, description: char, createdBy: char)
 */
CREATE TABLE `Event` (
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    cost DECIMAL(10,2),
    description TEXT,
    createdBy VARCHAR(50),
    PRIMARY KEY (eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (createdBy)
        REFERENCES EventProvider (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE
	-- ,CONSTRAINT checkTime CHECK (timeStart <= timeEnd)
);

/*
 * PrivateEvent(eventName: char, lat: float, lon: float, timeStart: time, timeEnd: time, 
 *			    cost: float, description: char)
 */
CREATE TABLE PrivateEvent (
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    PRIMARY KEY (eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (eventName , lat , lon , timeStart , timeEnd)
        REFERENCES Event (eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


/*
 * Admin(email: char, password: char, firstName: char, lastName: char, phone: int)
 */
CREATE TABLE Admin (
    email VARCHAR(50),
    password VARCHAR(60),
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    phone INT UNIQUE,
    PRIMARY KEY (email)
);

/*
 * User(email: char, password: char, firstName: char, lastName: char, phone: int, age: int)
 */
CREATE TABLE `User` (
    email VARCHAR(50),
    password VARCHAR(60),
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    phone INT UNIQUE,
    age INT,
    UNIQUE (firstName , lastName , age),
    PRIMARY KEY (email)
);


/*
 * HasInvitation(eventName: char, lat: float, lon: float, 
 *				 timeStart: time, timeEnd: time, message: char)
 */
CREATE TABLE HasInvitation (
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    message TEXT,
    PRIMARY KEY (eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (eventName , lat , lon , timeStart , timeEnd)
        REFERENCES PrivateEvent (eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE ON UPDATE CASCADE
);


/*
 * Group(groupId: int, groupName: char, description: char)
 */
 CREATE TABLE `Group` (
    groupId INT AUTO_INCREMENT,
    groupName VARCHAR(50),
    description TEXT,
    PRIMARY KEY (groupId)
);

/*
 * EventTypeHasEvent(eventTypeId: int, eventName: char, lat: float, lon: float, 
 *					 timeStart: time, timeEnd: time)
 */
CREATE TABLE EventTypeHasEvent (
    eventTypeId INT,
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    PRIMARY KEY (eventTypeId , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (eventTypeId)
        REFERENCES EventType (eventTypeId)
        ON DELETE NO ACTION
        ON UPDATE CASCADE,
    FOREIGN KEY (eventName , lat, lon , timeStart , timeEnd)
        REFERENCES `Event` (eventName , lat, lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
 * EventProviderSendInvitation(email: char, eventName: char, 
 * 							   lat: float, lon: float, timeStart: time, timeEnd: time)
 */
CREATE TABLE EventProviderSendInvitation (
    email VARCHAR(50),
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    sendToEmail VARCHAR(225),
    PRIMARY KEY (email , eventName , lat , lon , timeStart , timeEnd, sendToEmail),
    FOREIGN KEY (email)
        REFERENCES EventProvider (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (eventName , lat , lon , timeStart , timeEnd)
        REFERENCES HasInvitation (eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (sendToEmail)
        REFERENCES `User` (email)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/*
 * UserGoesEvent (email: char, eventName: char, lat: float, lon: float, 
 * 				  timeStart: time, timeEnd: time)
 */
 CREATE TABLE UserGoesEvent (
    email VARCHAR(50),
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    PRIMARY KEY (email , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (email)
        REFERENCES `User` (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (eventName , lat , lon , timeStart , timeEnd)
        REFERENCES `Event` (eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
 * With(groupId: int, email: char, eventName: char, lat: float, lon: float, 
 * 		timeStart: time, timeEnd: time)
 */
 CREATE TABLE `With` (
    groupId INT,
    email VARCHAR(50),
    eventName VARCHAR(50),
    lat DECIMAL(10,5),
    lon DECIMAL(10,5),
    timeStart DATETIME,
    timeEnd DATETIME,
    PRIMARY KEY (groupId , email , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (groupId)
        REFERENCES `Group` (groupId)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (email , eventName , lat , lon , timeStart , timeEnd)
        REFERENCES UserGoesEvent (email , eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

# password of each user is email without @test.com
INSERT INTO `group_up_debug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test1@test.com",
"$2y$10$0flxHQvyMJ9.qv/zR3IcmeCIjPHJz2ropVN6R3xkUgJgtZ9Uc3uSS",
"test1FirstName",
"test1LastName",
1234567);

INSERT INTO `group_up_debug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test2@test.com",
"$2y$10$cl5524njfsUqhT2EQ0u6a.ts2JOkH/2bS4EzYrX8SlpLcFZkTPnAG",
"test2FirstName",
"test2LastName",
2345678);

INSERT INTO `group_up_debug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test3@test.com",
"$2y$10$aP71EeogXFVYzQ9fJ3DgZeFHkxvvXrz2WqVFHzPrBdrOSnvpIp3Vm",
"test3FirstName",
"test3LastName",
3456789);

INSERT INTO `group_up_debug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test4@test.com",
"$2y$10$gAAP4cvNCKJItmpGORduhOyN5t3cETFhNx/f0Z7OouM4r5qZDXTzu",
"test4FirstName",
"test4LastName",
4567891);

INSERT INTO `group_up_debug`.`Admin`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("test5@test.com",
"$2y$10$I3KjYi6avPLRETncaV/3R.FAXvtVmuaV/79O1wb2L6GUuoe.dH2Cq",
"test5FirstName",
"test5LastName",
5678912);

INSERT INTO `group_up_debug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP1@test.com",
"$2y$10$.vUn/HCZ8OtDkKph1hnEfOEjBD50dKNt2/wUIcM0Ctk5rCmVu.J/q",
"test",
"EventProvider",
6789123);

INSERT INTO `group_up_debug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP2@test.com",
"$2y$10$rqj5F/v/C.pwqJfBF7jpfOCA9CupmI6lPV.k.E3CtijZBVn/f/rxi",
"test",
"EventProvider2",
6789124);

INSERT INTO `group_up_debug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP3@test.com",
"$2y$10$m/cw.bTNHY0kC2VKCHyywOx2fFZwrj37jCZJr4i7FybHV9tmKcpe.",
"test",
"EventProvider3",
6789125);

INSERT INTO `group_up_debug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP4@test.com",
"$2y$10$PFUMcxcuk0v8ptuKtljMP.eX3BkML6.RLpOpuyMep3vcTTSe5BR4q",
"test",
"EventProvider4",
6789126);

INSERT INTO `group_up_debug`.`EventProvider`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`)
VALUES
("testEP5@test.com",
"$2y$10$Zeco.3gEKebvfRLJU0XUIeFz1Byg8oOLY4Oh8AQX0p9gNdZ7oMz6a",
"test",
"EventProvider5",
6789127);

INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`Event`
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
50.3,
-122.9,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
10.96,
"test description",
"testEP3@test.com");

INSERT INTO `group_up_debug`.`Event`
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
49.112,
-126.123,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1000.08,
"test description",
"testEP4@test.com");

INSERT INTO `group_up_debug`.`Event`
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
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP5@test.com");

INSERT INTO `group_up_debug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"test description",
"testEP1@test.com");

INSERT INTO `group_up_debug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 5-1",
45.3956,
-128.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
127.77,
"test description",
"testEP5@test.com");

INSERT INTO `group_up_debug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 5-2",
46.3956,
-130.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
127.77,
"test description",
"testEP5@test.com");

INSERT INTO `group_up_debug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
127.77,
"test description",
"testEP5@test.com");

INSERT INTO `group_up_debug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Test Event 5-4",
41.3956,
-121.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
12007.77,
"test description",
"testEP5@test.com");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Horror Movie");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Action Movie");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("SciFi Movie");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Rock Concert");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Fight Club");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Pub Crawl");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Study Group");

INSERT INTO `group_up_debug`.`EventType`
(`category`)
VALUES
("Wine Tasting");

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
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

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
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

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(5,
"Test Event 2",
49.46,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28'
);

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
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

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
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

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(3,
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(4,
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(5,
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"Test Event 5-1",
45.3956,
-128.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(7,
"Test Event 5-1",
45.3956,
-128.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(8,
"Test Event 5-1",
45.3956,
-128.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(6,
"Test Event 5-2",
46.3956,
-130.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(7,
"Test Event 5-2",
46.3956,
-130.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(4,
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(5,
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(8,
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(4,
"Test Event 5-4",
41.3956,
-121.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`Group`
(`groupName`,
 `description`)
VALUES
("Test Group 1",
 "Test Group 1 Description");

INSERT INTO `group_up_debug`.`Group`
(`groupName`,
 `description`)
VALUES
("Test Group 2",
 "Test Group 2 Description");

INSERT INTO `group_up_debug`.`Group`
(`groupName`,
 `description`)
VALUES
("Test Group 3",
 "Test Group 3 Description");

INSERT INTO `group_up_debug`.`Group`
(`groupName`,
 `description`)
VALUES
("Test Group 4",
 "Test Group 4 Description");

INSERT INTO `group_up_debug`.`Group`
(`groupName`,
 `description`)
VALUES
("Test Group 5",
 "Test Group 5 Description");

INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("Private Test Event 2",
49.3,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("Private Test Event 3",
3.99,
-128.10,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("Private Test Event 4",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`Event`
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

INSERT INTO `group_up_debug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`Event`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`cost`,
`description`,
`createdBy`)
VALUES
("Private Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
1827.77,
"Amazing event where you can meet new people",
"testEP1@test.com");

INSERT INTO `group_up_debug`.`PrivateEvent`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("Private Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(5,
"Private Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`EventTypeHasEvent`
(`eventTypeId`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(1,
"Private Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser1@test.com",
"$2y$10$UAMvk8wDaKErVHoX3PDkfuGGhqd5.NG8CRJNVsafG3.PohqNwYhku",
"User1",
"Test",
2222222,
99);

INSERT INTO `group_up_debug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser2@test.com",
"$2y$10$mpbx97U0zY7PeG0MzGQCbuqYhXgAdpJuWiwG9yDkT4p9qhjw/3Kwq",
"User2",
"Test",
2222223,
98);

INSERT INTO `group_up_debug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser3@test.com",
"$2y$10$ffjvMaNvTDaPSnrv1bKwKOc7oEYaXkilEOBTV.xhQcAk3d1tyNPnS",
"User3",
"Test",
2222224,
97);

INSERT INTO `group_up_debug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser4@test.com",
"$2y$10$0eZO6OOakVnE0BMQR2aPGuQ8kQvtVm50kceUcDQEHWyIJkNQsZ.1a",
"User4",
"Test",
2222225,
99);

INSERT INTO `group_up_debug`.`User`
(`email`,
`password`,
`firstName`,
`lastName`,
`phone`,
`age`)
VALUES
("testUser5@test.com",
"$2y$10$cE0Q3JG8e3glIEMjE6cI/uGi7Xkvl/Kl6SpMzB1FOFo8t4QaG6opW",
"User5",
"Test",
"2222227",
14);


INSERT INTO `group_up_debug`.`HasInvitation`
(`eventName`,
 `lat`,
 `lon`,
 `timeStart`,
 `timeEnd`,
 `message`)
VALUES
("Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
 "TestMessage");

INSERT INTO `group_up_debug`.`HasInvitation`
(`eventName`,
 `lat`,
 `lon`,
 `timeStart`,
 `timeEnd`,
 `message`)
VALUES
("Private Test Event 2",
49.3,
-123.1,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `group_up_debug`.`HasInvitation`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
("Private Test Event 3",
3.99,
-128.10,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `group_up_debug`.`HasInvitation`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
("Private Test Event 4",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `group_up_debug`.`HasInvitation`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
("Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"TestMessage");

INSERT INTO `group_up_debug`.`HasInvitation`
(`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`message`)
VALUES
("Private Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
"Hello welcome to our amazing event");

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP5@test.com",
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser3@test.com');

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser1@test.com');

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser2@test.com');

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser3@test.com');

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser4@test.com');

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser5@test.com');

INSERT INTO `group_up_debug`.`EventProviderSendInvitation`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`,
`sendToEmail`)
VALUES
("testEP1@test.com",
"Private Test Event 6",
4.33,
-124.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28',
'testUser1@test.com');

INSERT INTO `group_up_debug`.`UserGoesEvent`
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

INSERT INTO `group_up_debug`.`UserGoesEvent`
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

INSERT INTO `group_up_debug`.`UserGoesEvent`
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

INSERT INTO `group_up_debug`.`UserGoesEvent`
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

INSERT INTO `group_up_debug`.`UserGoesEvent`
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

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser1@test.com",
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser2@test.com",
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Test Event 5",
44.3956,
-127.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Test Event 5-1",
45.3956,
-128.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser4@test.com",
"Test Event 5-1",
45.3956,
-128.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Test Event 5-2",
46.3956,
-130.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser1@test.com",
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser2@test.com",
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser4@test.com",
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser5@test.com",
"Test Event 5-3",
44.3956,
-120.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Test Event 5-4",
41.3956,
-121.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser5@test.com",
"Test Event 5-4",
41.3956,
-121.968,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Private Test Event 5",
3.33,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser1@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser2@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser3@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser4@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`UserGoesEvent`
(`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
("testUser5@test.com",
"Private Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`With`
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

INSERT INTO `group_up_debug`.`With`
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

INSERT INTO `group_up_debug`.`With`
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


INSERT INTO `group_up_debug`.`With`
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


INSERT INTO `group_up_debug`.`With`
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

INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"testUser1@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"testUser2@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"testUser3@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"testUser4@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(2,
"testUser5@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(3,
"testUser1@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(4,
"testUser2@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(5,
"testUser3@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(5,
"testUser4@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');


INSERT INTO `group_up_debug`.`With`
(`groupId`,
`email`,
`eventName`,
`lat`,
`lon`,
`timeStart`,
`timeEnd`)
VALUES
(3,
"testUser5@test.com",
"Test Event 1",
49.2,
-123.2,
'2016-04-30 14:53:28',
'2016-04-30 14:53:28');

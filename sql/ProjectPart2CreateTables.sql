DROP DATABASE IF EXISTS GroupUpDebug;
CREATE DATABASE IF NOT EXISTS GroupUpDebug;
USE GroupUpDebug;

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

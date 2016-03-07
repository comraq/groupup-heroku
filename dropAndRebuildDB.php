<?php

$connection = mysqli_connect("localhost", "root", "password");

if ($connection->connect_errno) {
	echo "Failed to connect to MySQL: (" . $connection->connect_errno . ") " . $connection->connect_error . " localhost root password GroupUpDebug";
}

echo $connection->host_info . "\n";
$dbName = "GroupUpDebug";

$dropDatabase = "DROP DATABASE IF EXISTS " . $dbName . ";";
$createDatabase = "CREATE DATABASE IF NOT EXISTS " . $dbName . ";";

if (mysqli_query($connection, $dropDatabase)) {
	echo "Database dropped successfully" . "\n";
} else {
	echo "Error dropping database: " . mysqli_error($connection);
}

if (mysqli_query($connection, $createDatabase)) {
	echo "Database created successfully" . "\n";
} else {
	echo "Error creating database: " . mysqli_error($connection);
}

mysqli_select_db($connection, $dbName);

$createTables = '-- EventType(eventTypeId: int, category: char)
CREATE TABLE EventType (
    eventTypeId INT,
    category VARCHAR(50),
    PRIMARY KEY (eventTypeId)
);

/*
 * EventProvider(email: char, firstName: char, lastName: char, 
 * 			     companyName: char, phone: int, password: char)
 */
 CREATE TABLE EventProvider (
    email VARCHAR(50),
    password VARCHAR(50),
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
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    cost FLOAT,
    description VARCHAR(255),
    createdBy VARCHAR(50),
    PRIMARY KEY (eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (createdBy)
        REFERENCES EventProvider (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
 * PrivateEvent(eventName: char, lat: float, lon: float, timeStart: time, timeEnd: time, 
 *			    cost: float, description: char)
 */
CREATE TABLE PrivateEvent (
    eventName VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    cost FLOAT,
    description VARCHAR(255),
    createdBy VARCHAR(50),
    PRIMARY KEY (eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (createdBy)
        REFERENCES EventProvider (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
 * Admin(email: char, password: char, firstName: char, lastName: char, phone: int)
 */
CREATE TABLE Admin (
    email VARCHAR(50),
    password VARCHAR(50),
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
    password VARCHAR(50),
    firstName VARCHAR(50),
    lastName VARCHAR(50),
    phone INT UNIQUE,
    age INT,
    UNIQUE (firstName , lastName , age),
    PRIMARY KEY (email)
);


/*
 * HasInvitation(invitationId: int, eventName: char, lat: float, lon: float, 
 *				 timeStart: time, timeEnd: time, message: char)
 */
CREATE TABLE HasInvitation (
    invitationId INT,
    eventName VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    sendToEmail VARCHAR(225),
    `read` TINYINT(1),
    message VARCHAR(255),
    PRIMARY KEY (invitationId , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (eventName , lat , lon , timeStart , timeEnd)
        REFERENCES PrivateEvent (eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (sendToEmail)
        REFERENCES `User` (email)
        ON DELETE CASCADE ON UPDATE CASCADE
);


/*
 * Group(groupId: int, groupName: char, description: char)
 */
 CREATE TABLE `Group` (
    groupId INT,
    groupName VARCHAR(50),
    description VARCHAR(255),
    PRIMARY KEY (groupId)
);

/*
 * EventTypeHasEvent(eventTypeId: int, eventName: char, lat: float, lon: float, 
 *					 timeStart: time, timeEnd: time)
 */
CREATE TABLE EventTypeHasEvent (
    eventTypeId INT,
    eventName VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    PRIMARY KEY (eventTypeId , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (eventTypeId)
        REFERENCES EventType (eventTypeId)
        ON DELETE NO ACTION
        ON UPDATE CASCADE,
    FOREIGN KEY (eventName , lat, lon , timeStart , timeEnd)
        REFERENCES `Event` (eventName , lat, lon , timeStart , timeEnd)
        ON DELETE NO ACTION
        ON UPDATE CASCADE
);

/*
 * UserReadsInvitation(email: char, invitationId: int, eventName: char, lat: float, lon: float,
 *						 timeStart: time, timeEnd: time)
 */
 CREATE TABLE UserReadsInvitation (
    email VARCHAR(50),
    invitationId INT,
    eventName VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    PRIMARY KEY (email , invitationId , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (email)
        REFERENCES `User` (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (invitationId , eventName , lat , lon , timeStart , timeEnd)
        REFERENCES HasInvitation (invitationId , eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
 * EventProviderSendInvitation(email: char, invitationId: int, eventName: char, 
 * 							   lat: float, lon: float, timeStart: time, timeEnd: time)
 */
CREATE TABLE EventProviderSendInvitation (
    email VARCHAR(50),
    invitationId INT,
    eventName VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    PRIMARY KEY (email , invitationId , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (email)
        REFERENCES EventProvider (email)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (invitationId , eventName , lat , lon , timeStart , timeEnd)
        REFERENCES HasInvitation (invitationId , eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

/*
 * UserGoesEvent (email: char, eventName: char, lat: float, lon: float, 
 * 				  timeStart: time, timeEnd: time)
 */
 CREATE TABLE UserGoesEvent (
    email VARCHAR(50),
    eventName VARCHAR(50),
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
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
    lat FLOAT,
    lon FLOAT,
    timeStart TIME,
    timeEnd TIME,
    PRIMARY KEY (groupId , email , eventName , lat , lon , timeStart , timeEnd),
    FOREIGN KEY (groupId)
        REFERENCES `Group` (groupId)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (email , eventName , lat , lon , timeStart , timeEnd)
        REFERENCES UserGoesEvent (email , eventName , lat , lon , timeStart , timeEnd)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);';

	if (mysqli_multi_query($connection, $createTables)) {
		echo "All tables created successfully";
	} else {
		echo "Error creating table: " . mysqli_error($connection);
		$error = true;
		break;
	}

mysqli_close($connection);
?>


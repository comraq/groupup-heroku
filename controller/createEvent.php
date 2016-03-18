<?php
require_once(__DIR__.'/database.php');

class createEvent extends Database{

	function __construct(){
		parent::__construct();
	}

	function createEvent(){
		date_default_timezone_set("America/Los_Angeles");
		$data = $_POST["createEvent"];
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd =  $data["timeEnd"];
		$cost = $data["eventCost"];
		$description = $data["eventDescription"];
		$eventType = $data["eventType"];
		$createdBy = "testEP1@test.com";
		$lat = $data["lat"];
		$lon = $data["lng"];
		
		parent::connect();
		
		$insertEventSql = "INSERT INTO 
		`Event`(`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`cost`,`description`,`createdBy`) 
		VALUES(?,?,?,?,?,?,?,?)";

		$stmt = $this->conn->prepare($insertEventSql);
		$stmt->bind_param('sddssdss', $eventName, $lat, $lon, $timeStart, $timeEnd, $cost, $description, $createdBy);
		$stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
		parent::disconnect();
		print TRUE;
		return TRUE;
	}
}
$createEvent = new createEvent();
$result = $createEvent->createEvent();
return $result;
?>
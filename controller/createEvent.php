<?php
require_once(__DIR__.'/database.php');

class CreateEvent extends Database{

	function __construct(){
		parent::__construct();
	}

	function createEvent($data){
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
		parent::disconnect();
		return json_encode(TRUE);
	}

	function startCreateEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->createEvent($data);
			$this->response($result, 200);
		}
	}
}


?>
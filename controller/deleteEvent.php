<?php
require_once(__DIR__.'/database.php');

class DeleteEvent extends Database{

	function __construct(){
		parent::__construct();
	}

	function deleteEvent($data){
		$this->connect();
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd = $data["timeEnd"];
		$eventType = $data["eventType"];
		$lat = $data["lat"];
		$lon = $data["lng"];
		
		$deleteEventSQL = "DELETE FROM `Event` 
		WHERE eventName=? AND lat=? AND lon=? AND timeStart=? AND timeEnd=? AND eventType=?";

		$deleteEventSQL = $this->conn->prepare($deleteEventSQL);
		$deleteEventSQL->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
		$deleteEventSQL->execute();
		if(!$deleteEventSQL->execute()){
			$result = array('data' => "Event could not be deleted", "code"=> 500);
		}else{
				$result = array('data' => TRUE, "code"=> 200);
		
		}
		
		$deleteEventSQL->close();
		$this->disconnect();
		return $result;
	}

	function startDeleteEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->deleteEvent($data);
			$this->response($result["data"], $result["code"]);
		}else{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
			exit;
		}
	}
}


?>
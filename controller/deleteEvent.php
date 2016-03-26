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
		$lat = $data["lat"];
		$lon = $data["lng"];
		
		$deleteEventSQL = "DELETE FROM `Event` 
		WHERE eventName=? AND lat=? AND lon=? AND timeStart=? AND timeEnd=?";

		$deleteEventSTMT = $this->conn->prepare($deleteEventSQL);
		$deleteEventSTMT->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
		if(!$deleteEventSTMT->execute()){
			$result = array('data' => "Event could not be deleted", "code"=> 500);
		}else{
			$result = array('data' => TRUE, "code"=> 200);
		
		}
		$deleteEventSTMT->close();
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
<?php
require_once(__DIR__.'/database.php');

class UpdateEvent extends Database{

	function __construct(){
		parent::__construct();
		mysqli_report(MYSQLI_REPORT_ERROR);
	}

	function updateEvent($data){
		$this->connect();

		$origEventName = $data["origEventName"];
		$origTimeStart = $data["origTimeStart"];
		$origTimeEnd = $data["origTimeEnd"];
		$origLat = $data["origLat"];
		$origLon = $data["origLng"];
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd = $data["timeEnd"];
		$cost = $data["eventCost"];
		$description = $data["eventDescription"];
		$lat=$data["lat"];
		$lon=$data["lng"];
		$createdBy = $data["createdBy"];
		$privateEvent = $data["privateEvent"];
		
		if ($privateEvent){
			$message = $data["message"];
			$invitees = $data["invitees"];
		}

		$updateSQL = "UPDATE `Event` SET eventName=?, lat=?, lon=?, timeStart=?, timeEnd=?, cost=?, description=? WHERE eventName = ? AND lat=? AND lon=? AND timeStart=? AND timeEnd=?";
		if(!$updateStmt = $this->conn->prepare($updateSQL)){
			$result = array('data' => "There was an error deleting the event from the databse", 'code'=> 500);
		}else{
			$updateStmt->bind_param('sddssdssddss', $eventName, $lat, $lon, $timeStart, $timeEnd, $cost, $description, $origEventName, $origLat, $origLon, $origTimeStart, $origTimeEnd);
			if(!$updateStmt->execute()){
				$result = array('data' => "There was an error deleting the event from the databse", 'code'=> 500);
			}else{
				if($updateStmt->affected_rows > 0){
					$result = array('data' => TRUE, 'code'=> 200);
				}else{
					$result = array('data' => "Update was not successful".$this->conn->error, 'code'=> 500);
				}
			}
		}
		$updateStmt->close();
		$this->disconnect();
		return $result;
	}
	function startUpdateEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->updateEvent($data);
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
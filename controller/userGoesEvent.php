<?php
require_once(__DIR__.'/database.php');
class UserGoesEvent extends Database{
	function __construct(){
		parent::__construct();
	}
	function userGoesEvent($data){
		
		$email = $data["email"];
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd = $data["timeEnd"];
		$lat = $data["lat"];
		$lon = $data["lon"];
		
		$insertUGE = "INSERT INTO `UserGoesEvent`(`email`, `eventName`, `lat`, `lon`, `timeStart`, `timeEnd`)
		VALUES (?,?,?,?,?,?)";
		
		$this->connect();
		$stmt = $this->conn->prepare($insertUGE);
		$stmt->bind_param('ssddss', $email, $eventName, $lat, $lon, $timeStart, $timeEnd);
		$stmt->execute();
		$stmt->close();
		if($this->conn->error){
			$result = array('data' => $this->conn->error);
		}else{
			$result = true;
		}
		$this->disconnect();
		return $result;
	}

function cancelUserGoesEvent($data){
		
		$email = $data["email"];
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd = $data["timeEnd"];
		$lat = $data["lat"];
		$lon = $data["lon"];
		
		$deleteUGE = "DELETE FROM UserGoesEvent WHERE email=? AND eventName=? AND lat=? AND lon=? AND timeStart=? AND timeEnd=?";
		
		$this->connect();
		$stmt = $this->conn->prepare($deleteUGE);
		echo $this->conn->error;
		$stmt->bind_param('ssddss', $email, $eventName, $lat, $lon, $timeStart, $timeEnd);
		$stmt->execute();
		$stmt->close();
		if($this->conn->error){
			$result = array('data' => $this->conn->error);
		}else{
			$result = true;
		}
		$this->disconnect();
		return $result;
	}


	function startUserGoesEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->userGoesEvent($data);
			$this->response($result, 200);
		}else{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
			exit;
		}
	}

	function startCanceltUserGoesEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->cancelUserGoesEvent($data);
			$this->response($result, 200);
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
<?php
require_once(__DIR__.'/database.php');

class EventType extends Database{

	function __construct(){
		parent::__construct();
		mysqli_report(MYSQLI_REPORT_ERROR);

	}

	function getTypes(){
		
		$this->connect();
		$searchEventsSQL = "SELECT * FROM `EventType`";

		$stmt = $this->conn->prepare($searchEventsSQL);
		$stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
		$res = $stmt->get_result();
		$data = $res->fetch_all(MYSQLI_ASSOC);
		$stmt->close();
		$this->disconnect();
		return json_encode($data);
	}

	function deleteTypes($data){
		$this->connect();
		$eventType = $data["eventTypes"];
		$deleteSQL = "DELETE FROM EventType WHERE eventTypeId=?";
		$delStmt = $this->conn->prepare($deleteSQL);
		$delStmt->bind_param('d', $eventType);
		if($delStmt->execute()){
			$result = array('data' => TRUE, 'code'=> 200);
		}else{
			$result = array('data' => "You cannot delete an Event Type that has Events associated with it", 'code'=> 500);
		}
		$delStmt->close();
		$this->disconnect();
		return $result;
	}
	
	function startGetTypes(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'GET'){
			$result = $this->getTypes();
			$this->response($result, 200);
		}else{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
		}
		exit;
	}

	function startDeleteEventTypes(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->deleteTypes($data);
			$this->response($result["data"], $result["code"]);
		}else{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
		}
		exit;
	}
}
?>
<?php
require_once(__DIR__.'/database.php');

class CreateEvent extends Database{

	function __construct(){
		parent::__construct();
		mysqli_report(MYSQLI_REPORT_ALL);
	}

	function createEvent($data){
		$this->connect();
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd = $data["timeEnd"];
		$cost = $data["eventCost"];
		$description = $data["eventDescription"];
		$eventType = $data["eventType"];
		$createdBy = $data["createdBy"];
		$lat = $data["lat"];
		$lon = $data["lng"];
		$privateEvent = $data["privateEvent"];
		
		if ($privateEvent){
			$message = $data["message"];
			$invitees = $data["invitees"];
		}

		$insertEventSql = "INSERT INTO 
		`Event`(`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`cost`,`description`,`createdBy`) 
		VALUES(?,?,?,?,?,?,?,?)";

		$insertPrivateEventSQL = "INSERT INTO 
		`PrivateEvent`(`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`) 
		VALUES(?,?,?,?,?)";

		$insertEventProviderSendInvitation = "INSERT INTO 
		`EventProviderSendInvitation`(`email`,`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`sendToEmail`) 
		VALUES(?,?,?,?,?,?,?)";

		$insertHasInvitation ="INSERT INTO `HasInvitation`(`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`message`)
		VALUES (?, ?, ?,?, ?, ?)";

		$insertEventTypeHasEvent = "INSERT INTO `EventTypeHasEvent` 
		(`eventTypeId`, `eventName`, `lat`, `lon`, `timeStart`, `timeEnd`)
		VALUES
		(?,?,?,?,?,?)";

		$table = "`Event`";
		if($privateEvent){
			$table = "PrivateEvent";
		}

		$checkIfExists = "SELECT * FROM `Event` WHERE eventName=? AND lat= CAST(? AS DECIMAL(10,5)) AND lon=CAST(? AS DECIMAL(10,5)) AND timeStart=? AND timeEnd=?";
		
		$checkIfExistsStmt = $this->conn->prepare($checkIfExists);
		$checkIfExistsStmt->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
		if(!$checkIfExistsStmt->execute()){
			$result = array('data' => "This event already exists", 'code'=> 500);
		}
		$checkIfExistsStmt->store_result();

		if ($checkIfExistsStmt->num_rows == 0){
			$this->conn->autocommit(FALSE);
			$this->conn->query("START TRANSACTION");
			
			$insertEventStmt = $this->conn->prepare($insertEventSql);
			$insertEventStmt->bind_param('sddssdss', $eventName, $lat, $lon, $timeStart, $timeEnd, $cost, $description, $createdBy);
			if(!$insertEventStmt->execute()){
				$this->conn->rollback();
				$result = array('data' => "There was an error inserting the event into the databse", 'code'=> 500);
			}
			$insertEventStmt->close();

			if($privateEvent){
				$insPEventStmt = $this->conn->prepare($insertPrivateEventSQL);
				$insPEventStmt->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
				if(!$insPEventStmt->execute()){
					$this->conn->rollback();
					$result = array('data' => "There was an error inserting the event into the databse", 'code'=> 500);
				}
				$insPEventStmt->close();

				$insHasInvStmt = $this->conn->prepare($insertHasInvitation);
				$insHasInvStmt->bind_param('sddsss', $eventName, $lat, $lon, $timeStart, $timeEnd, $message);
				if(!$insHasInvStmt->execute()){
					$this->conn->rollback();
					$result = array('data' => "There was an error inserting the invitation into the databse", 'code'=> 500);
				}
				$insHasInvStmt->close();

				if(!$invStmt = $this->conn->prepare($insertEventProviderSendInvitation)){
					$this->conn->rollback();
					return array('data' => $createdBy, 'code'=> 500);
				}
				$invStmt->bind_param('ssddsss', $createdBy, $eventName, $lat, $lon, $timeStart, $timeEnd, $sendToEmail);
				
				foreach ($invitees as $sendToEmail) {
					if(!$invStmt->execute()){
						$this->conn->rollback();
						$result = array('data' => "There was an error sending your invitaitons", 'code'=> 500);
						break;
					}
				}
				$invStmt->close();
			}

			$insertEventTypeHasEventStmt = $this->conn->prepare($insertEventTypeHasEvent);
			$insertEventTypeHasEventStmt->bind_param('dsddss', $typeId, $eventName, $lat, $lon, $timeStart, $timeEnd);
			foreach ($eventType as $typeId) {
				if(!$insertEventTypeHasEventStmt->execute()){
					$this->conn->rollback();
					$result = array('data' => $this->conn->error, 'code'=> 500);
					break;
				}

			}
			$insertEventTypeHasEventStmt->close();

			if($this->conn->error){
				$this->conn->rollback();
				$result = array('data' => $this->conn->error, 'code'=> 500);
			}else{
				$this->conn->query("COMMIT");
				$result = array('data' => TRUE, 'code'=> 200);
			}
			$this->conn->autocommit(TRUE);
		}else{
			$result = array('data' => "This event already exists", 'code'=> 500);
		}
		$this->disconnect();
		return $result;
	}

	function startCreateEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->createEvent($data);
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
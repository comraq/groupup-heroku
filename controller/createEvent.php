<?php
require_once(__DIR__.'/database.php');

class CreateEvent extends Database{

	function __construct(){
		parent::__construct();
	}

	function createEvent($data){
		$this->connect();
		$eventName = $data["eventName"];
		$timeStart = $data["timeStart"];
		$timeEnd = $data["timeEnd"];
		$cost = $data["eventCost"];
		$description = $data["eventDescription"];
		$eventType = $data["eventType"];
		//TODO Change this
		$createdBy = "testEP1@test.com";
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
		`EventProviderSendInvitation`(`email`,`invitationId`,`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`sendToEmail`) 
		VALUES(?,?,?,?,?,?,?,?)";

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

		//$checkIfExists = "SELECT * FROM " . $table . " WHERE eventName=? AND lat= CAST(? AS DECIMAL(10,5)) AND lon=CAST(? AS DECIMAL(10,5)) AND timeStart=? AND timeEnd=?";
		
		$checkIfExists = "SELECT * FROM `Event` WHERE eventName=? AND lat= CAST(? AS DECIMAL(10,5)) AND lon=CAST(? AS DECIMAL(10,5)) AND timeStart=? AND timeEnd=?";
		
		$checkIfExistsStmt = $this->conn->prepare($checkIfExists);
		$checkIfExistsStmt->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
		$checkIfExistsStmt->execute();
		$checkIfExistsStmt->store_result();


		if ($checkIfExistsStmt->num_rows == 0){
			$this->conn->autocommit(FALSE);
			$this->conn->query("START TRANSACTION");
			
			$insertEventStmt = $this->conn->prepare($insertEventSql);
			$insertEventStmt->bind_param('sddssdss', $eventName, $lat, $lon, $timeStart, $timeEnd, $cost, $description, $createdBy);
			$insertEventStmt->execute();
			$insertEventStmt->close();

			if($privateEvent){

				$insPEventStmt = $this->conn->prepare($insertPrivateEventSQL);
				$insPEventStmt->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
				$insPEventStmt->execute();
				$insPEventStmt->close();

				$insHasInvStmt = $this->conn->prepare($insertHasInvitation);
				$insHasInvStmt->bind_param('sddsss', $eventName, $lat, $lon, $timeStart, $timeEnd, $message);
				$insHasInvStmt->execute();
				$iID = $this->conn->insert_id;
				$insHasInvStmt->close();

				$invStmt = $this->conn->prepare($insertEventProviderSendInvitation);
				$invStmt->bind_param('sdsddsss', $createdBy, $iID, $eventName, $lat, $lon, $timeStart, $timeEnd, $sendToEmail);
				
				foreach ($invitees as $sendToEmail) {
					$invStmt->execute();
				}
				$invStmt->close();
			}

			$insertEventTypeHasEventStmt = $this->conn->prepare($insertEventTypeHasEvent);
			$insertEventTypeHasEventStmt->bind_param('dsddss', $typeId, $eventName, $lat, $lon, $timeStart, $timeEnd);
			foreach ($eventType as $typeId) {
				$insertEventTypeHasEventStmt->execute();
			}
			$insertEventTypeHasEventStmt->close();

			if($this->conn->error){
				$this->conn->rollback();
				$result = array('data' => $this->conn->error);
			}else{
				$this->conn->query("COMMIT");
				$result = TRUE;
			}
			$this->conn->autocommit(TRUE);
		}else{
			$result = FALSE;
		}
		$this->disconnect();
		return json_encode($result);
	}

	function startCreateEvent(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->createEvent($data);
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
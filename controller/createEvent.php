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
		$privateEvent = $data["privateEvent"];
		
		if ($privateEvent){
			$message = $data["message"];
			$invitees = $data["invitees"];
		}

		parent::connect();

		$insertEventSql = "INSERT INTO 
		`Event`(`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`cost`,`description`,`createdBy`) 
		VALUES(?,?,?,?,?,?,?,?)";

		$insertPrivateEventSQL = "INSERT INTO 
		`PrivateEvent`(`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`cost`,`description`,`createdBy`) 
		VALUES(?,?,?,?,?,?,?,?)";

		$insertEventProviderSendInvitation = "INSERT INTO 
		`EventProviderSendInvitation`(`email`,`invitationId`,`eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`sendToEmail`) 
		VALUES(?,?,?,?,?,?,?,?)";

		$insertHasInvitation ="INSERT INTO `HasInvitation`(`invitationId`, `eventName`,`lat`,`lon`,`timeStart`,`timeEnd`,`message`)
		VALUES (?, ?, ?, ?,?, ?, ?)";

		$insertEventTypeHasEvent = "INSERT INTO `EventTypeHasEvent`
		(`eventTypeId`,
		`eventName`,
		`lat`,
		`lon`,
		`timeStart`,
		`timeEnd`)
		VALUES
		(?,?,?,?,?,?)";

		if(!$privateEvent){
			$checkIfExists = "SELECT * FROM `Event` WHERE eventName=? AND lat= CAST(? AS DECIMAL(10,5)) AND lon=CAST(? AS DECIMAL(10,5)) AND timeStart=? AND timeEnd=?";
			$checkIfExistsStmt = $this->conn->prepare($checkIfExists);
			$checkIfExistsStmt->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
			$checkIfExistsStmt->execute();
			$checkIfExistsStmt->store_result();

			if ($checkIfExistsStmt->num_rows == 0){
				$stmt = $this->conn->prepare($insertEventSql);
				$stmt->bind_param('sddssdss', $eventName, $lat, $lon, $timeStart, $timeEnd, $cost, $description, $createdBy);
				$stmt->execute();
				$stmt->close();
				$result = TRUE;
			}else{
				$result = FALSE;
			}
		}else{


			$checkIfExists = "SELECT * FROM `PrivateEvent` WHERE eventName=? AND lat= CAST(? AS DECIMAL(10,5)) AND lon=CAST(? AS DECIMAL(10,5)) AND timeStart=? AND timeEnd=?";
			$checkIfExistsStmt = $this->conn->prepare($checkIfExists);
			$checkIfExistsStmt->bind_param('sddss', $eventName, $lat, $lon, $timeStart, $timeEnd);
			$checkIfExistsStmt->execute();
			$checkIfExistsStmt->store_result();
			
			if ($checkIfExistsStmt->num_rows == 0){

				$query = "SELECT MAX(invitationId) FROM HasInvitation";
				$idStmt = $this->conn->prepare($query);
				$idStmt->execute();
				$idStmt->bind_result($id);
				$idStmt->fetch();
				$idStmt->close();

				$stmt = $this->conn->prepare($insertPrivateEventSQL);
				$stmt->bind_param('sddssdss', $eventName, $lat, $lon, $timeStart, $timeEnd, $cost, $description, $createdBy);
				$stmt->execute();
				$stmt->close();

				$stmt = $this->conn->prepare($insertHasInvitation);
				$stmt->bind_param('dsddsss', $id, $eventName, $lat, $lon, $timeStart, $timeEnd, $message);
				$stmt->execute();
				$stmt->close();


				$invStmt = $this->conn->prepare($insertEventProviderSendInvitation);
				$invStmt->bind_param('sdsddsss', $createdBy, $id, $eventName, $lat, $lon, $timeStart, $timeEnd, $sendToEmail);
				$this->conn->query("START TRANSACTION");
				foreach ($invitees as $sendToEmail) {
					$invStmt->execute();
					print $this->conn->error;
				}
				$invStmt->close();

				$insertEventTypeHasEventStmt = $this->conn->prepare($insertEventTypeHasEvent);
				$insertEventTypeHasEventStmt->bind_param('dsddss', $eventType, $eventName, $lat, $lon, $timeStart, $timeEnd);
				foreach ($eventType as $typeId) {
					$insertEventTypeHasEventStmt->execute();
					print $this->conn->error;
				}
				$insertEventTypeHasEventStmt->close();
				$this->conn->query("COMMIT");
				$result = TRUE;
			}else{
				$result = FALSE;
			}
		}
		parent::disconnect();
		return json_encode($result);
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
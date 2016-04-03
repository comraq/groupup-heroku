<?php

require_once(__DIR__.'/database.php');

class Account extends Database
{
	public $LIMIT = 10;

	function __construct(){
		parent::__construct();
	}

	private function getProfile($tb, $dataArray)
	{
		$data = $dataArray;
		if (is_null($data))
		{
			$result = array(
				'data' => "Please refresh and login again"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		$table = $tb;
		$email = $data["email"];
		$this->connect();
		$escapeEmail = $this->conn->real_escape_string($email);
		$getProfileSql = "SELECT * from " . $table . " where email = ?";
		$stmt = $this->conn->prepare($getProfileSql);
		$stmt->bind_param("s", $escapeEmail);

		$stmt->execute();
		$res = $stmt->get_result();
		$result = $res->fetch_all(MYSQLI_ASSOC);
		$stmt->close();


		$result = array(
					'data' => $result
				);
		$statusCode = 200;		
		$this->disconnect();
		$this->response($result, $statusCode);
	}

	private function updateProfile($tb, $dataArray)
	{
		$data = $dataArray;
		if (is_null($data))
		{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
		
		$table = $tb;
		$email = $data["email"];
		$firstName = $data["firstName"];
		$lastName = $data["lastName"];
		$phone = $data["phone"];
		$age;
		
		if($table == 'User'){
			$age = $data["age"];
			if ($age < 0)
			{
				$result = array(
					'data' => "Age should not be negative"
					);
				$statusCode = 400;
				$this->response($result, $statusCode);
			}
		}
		$result;
		$statusCode;

		if(is_null($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			$result = array(
				'data' => "Please log in again and try"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		if ($table == 'User')
		{
			if(is_null($firstName) || is_null($lastName) || is_null($phone) || is_null($age))
			{
				$result = array(
					'data' => "All fields must be filled"
					);
				$statusCode = 400;
				$this->response($result, $statusCode);
				return;
			}
		}else{
			
			if(is_null($firstName) || is_null($lastName) || is_null($phone))
			{
				$result = array(
					'data' => "All fields must be filled"
					);
				$statusCode = 400;
				$this->response($result, $statusCode);
				return;
			}
		}


		$this->connect();
		$escapeEmail = $this->conn->real_escape_string($email);
		$escapeFName = $this->conn->real_escape_string($firstName);
		$escapeLName = $this->conn->real_escape_string($lastName);
		$escapePhone = $this->conn->real_escape_string($phone);
		$escapeAge;
		
		if (!is_null($age)){
			$escapeAge = $this->conn->real_escape_string($age);
			$updateProfileSql = "UPDATE " . $table . " set firstname=?, lastname=?, phone=?, age=? where email = ?";
			$stmt = $this->conn->prepare($updateProfileSql);
			$stmt->bind_param('ssiis', $escapeFName, $escapeLName, $escapePhone, $escapeAge, $escapeEmail);
		}else{
			$updateProfileSql = "UPDATE " . $table . " set firstname=?, lastname=?, phone=? where email = ?";
			$stmt = $this->conn->prepare($updateProfileSql);
			$stmt->bind_param('ssis', $escapeFName, $escapeLName, $escapePhone, $escapeEmail);
		}

		$stmt->execute();
		$stmt->close();
		$result = array(
					'data' => True
				);
		$statusCode = 200;
		$this->disconnect();
		$this->response($result, $statusCode);

	}

	private function updatePassword($tb, $dataArray)
	{
		$data = $dataArray;
		if (is_null($data))
		{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
		
		$table = $tb;
		$email = $data["email"];
		$oldPassword = $data["oldPassword"];
		$newPassword = $data["newPassword"];
		$rePassword = $data["rePassword"];
		$checkPassword;
		$result;
		$statusCode;

		if(is_null($oldPassword) || is_null($newPassword) || is_null($rePassword)) 
		{
			$result = array(
				'data' => "All fields must be filled"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		$this->connect();
		$escapeOldPass = $this->conn->real_escape_string($oldPassword);
		$escapeNewPass = $this->conn->real_escape_string($newPassword);
		$escapeRePass = $this->conn->real_escape_string($rePassword);
		$escapeEmail = $this->conn->real_escape_string($email);

		$hashPass = password_hash($escapeNewPass, PASSWORD_BCRYPT);
		if (!password_verify($escapeRePass, $hashPass))
		{ 
			$this->disconnect(); 
			$result = array(
				'data' => "Password & Re-password are different"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		$checkPasswordSql = "select password from " . $table . " where email = ?";
		$stmt = $this->conn->prepare($checkPasswordSql);
		$stmt->bind_param('s', $escapeEmail);
		$stmt->execute();
		$res = $stmt->get_result();
		$checkPassword = $res->fetch_all(MYSQLI_ASSOC);
	    $stmt->close();

	    // check if password is correct
	    if (!password_verify($escapeOldPass, $checkPassword[0]["password"]))
		{ 
			$this->disconnect(); 
			$result = array(
				'data' => "Password is incorrect"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
	
		$updatePassSql = "update " . $table . " set password=? where email = ?";
		$stmt = $this->conn->prepare($updatePassSql);
		$stmt->bind_param('ss', $hashPass, $escapeEmail);
		$stmt->execute();
		$stmt->close();
		$result = array(
					'data' => True
				);
		$statusCode = 200;
		
		$this->disconnect();
		$this->response($result, $statusCode);
	}

	private function getEvent($dataArray)
	{
		$data = $dataArray;
		if (is_null($data))
		{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
		$email = $data["email"];
		$page = $data["page"];
		$offset = $page * $this->LIMIT;
		$result;
		$statusCode;
		$this->connect();
		$escapeEmail = $this->conn->real_escape_string($email);
		$getEventSql = "SELECT AE.*,
		GROUP_CONCAT(G.groupName SEPARATOR ', ') AS groupNames,
        GROUP_CONCAT(G.description SEPARATOR ', ') AS groupDescriptions,
        GROUP_CONCAT(AE.groupId SEPARATOR ', ') AS groupIds
        from (
	SELECT 
		UGE.email AS email,
		UGE.eventName AS eventName,
		UGE.lat AS lat,
		UGE.lon AS lon,
		UGE.timeStart AS timeStart,
		UGE.timeEnd AS timeEnd,
		E.cost AS cost,
		E.description AS description,
        GROUP_CONCAT(DISTINCT ET.category
        SEPARATOR ', ') AS category,
		W.groupId as groupId

		FROM UserGoesEvent as UGE
		NATURAL LEFT JOIN `Event` E
		NATURAL LEFT JOIN EventTypeHasEvent ETHE
        NATURAL LEFT JOIN EventType ET
        LEFT OUTER JOIN (select * from `With`) as W on 
			UGE.email = W.email AND 
        	UGE.eventName = W.eventName AND
        	UGE.lat = W.lat AND
        	UGE.lon = W.lon AND
        	UGE.timeStart = W.timeStart AND
        	UGE.timeEnd = W.timeEnd

		WHERE
			UGE.email = ?

		Group By UGE.email, UGE.eventName, UGE.lat, UGE.lon, UGE.timeStart, UGE.timeEnd, W.groupId
		Order By UGE.timeStart DESC
        ) as AE
		LEFT OUTER JOIN `Group` G on
        AE.groupId = G.groupId 
        Group By AE.email, AE.eventName, AE.lat, AE.lon, AE.timeStart, AE.timeEnd
        LIMIT ? OFFSET ?";

		$stmt = $this->conn->prepare($getEventSql);
		$stmt->bind_param('sii', $escapeEmail, $this->LIMIT, $offset);
		$stmt->execute();
		$res = $stmt->get_result();
		$result = $res->fetch_all(MYSQLI_ASSOC);

	    $stmt->close();
	    $this->disconnect();

		$statusCode = 200;

		$this->response($result, $statusCode);

	}

	private function getInvitation($tb, $dataArray)
	{
		$data = $dataArray;
		if (is_null($data))
		{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
		$table = $tb;
		$email = $data["email"];
		$page = $data["page"];
		$offset = $page * $this->LIMIT;
		$result;
		$statusCode;
		
		$this->connect();
		$escapeEmail = $this->conn->real_escape_string($email);
		$getInvitationSql = "SELECT
		EPSI.email AS email,
		EPSI.eventName AS eventName,
		EPSI.lat AS lat,
		EPSI.lon AS lon,
		EPSI.timeStart AS timeStart,
		EPSI.timeEnd AS timeEnd,
		HI.message AS message,
		E.cost AS cost,
		E.description AS description,
		(CASE
        WHEN UGE.email = ? THEN 1
        ELSE 0
        END) AS going,
        GROUP_CONCAT(ET.category
        SEPARATOR ', ') AS category

		FROM EventProviderSendInvitation as EPSI,
			 HasInvitation as HI,
			 PrivateEvent as PE,
			 Event as E
		NATURAL LEFT JOIN UserGoesEvent UGE
		NATURAL LEFT JOIN EventTypeHasEvent ETHE
        NATURAL LEFT JOIN EventType ET

		WHERE
			EPSI.eventName = HI.eventName AND
			EPSI.lat = HI.lat AND
			EPSI.lon = HI.lon AND
			EPSI.timeStart = HI.timeStart AND
			EPSI.timeEnd = HI.timeEnd AND

			HI.eventName = PE.eventName AND
			HI.lat = PE.lat AND
			HI.lon = PE.lon AND
			HI.timeStart = PE.timeStart AND
			HI.timeEnd = PE.timeEnd AND

			PE.eventName = E.eventName AND
        	PE.lat = E.lat AND
        	PE.lon = E.lon AND
        	PE.timeStart = E.timeStart AND
        	PE.timeEnd = E.timeEnd AND
        	sendToEmail = ?
        Group By PE.eventName, PE.lat, PE.lon, PE.timeStart, PE.timeEnd
        Order By EPSI.timeStart DESC

        LIMIT ? OFFSET ?";

		$stmt = $this->conn->prepare($getInvitationSql);
		$stmt->bind_param('ssii', $escapeEmail, $escapeEmail, $this->LIMIT, $offset);
		$stmt->execute();
		$res = $stmt->get_result();
		$result = $res->fetch_all(MYSQLI_ASSOC);

	    $stmt->close();
	    $this->disconnect();

		$statusCode = 200;

		$this->response($result, $statusCode);
	}



	public function user()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST')
		{
			$table = "User";
			$json = file_get_contents("php://input");
			$dataObj = json_decode($json,TRUE);
			if (isset($dataObj["event"])){
				$data = $dataObj["event"];
				$this->getEvent($data);
			}

			if (isset($dataObj["invitation"])){
				$data = $dataObj["invitation"];
				$table = "EventProviderSendInvitation";
				$this->getInvitation($table, $data);
			}

			if (isset($dataObj["profile"])){
				$data = $dataObj["profile"];
				$this->updateProfile($table, $data);
			}

			if (isset($dataObj["password"])){
				$data = $dataObj["password"];
				$this->updatePassword($table, $data);
			}

			if (isset($dataObj["getProfile"])){
				$data = $dataObj["getProfile"];
				$this->getProfile($table, $data);
			}
		}else{
			$this->response("Method Not Allowed", 405);
		}
	}

	public function eventProvider()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST')
		{
			$table = "EventProvider";
			$json = file_get_contents("php://input");
			$dataObj = json_decode($json,TRUE);

			if (isset($dataObj["profile"])){
				
				$data = $dataObj["profile"];
				$this->updateProfile($table, $data);
			}

			if (isset($dataObj["getProfile"])){
				$data = $dataObj["getProfile"];
				$this->getProfile($table, $data);
			}

			if (isset($dataObj["password"])){
				$data = $dataObj["password"];
				$this->updatePassword($table, $data);
			}
		}else{
			$this->response("Method Not Allowed", 405);
		}
	}
}

?>

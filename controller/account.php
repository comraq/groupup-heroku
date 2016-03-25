<?php

require_once(__DIR__.'/database.php');

class Account extends Database
{
	public $LIMIT = 10;

	function __construct(){
		parent::__construct();
	}

	private function updateProfile($db, $dataArray)
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
		
		$table = $db;
		$email = $data["email"];
		$firstName = $data["firstName"];
		$lastName = $data["lastName"];
		$phone = $data["phone"];
		$age = $data["age"];
		$result;
		$statusCode;

		if ($age < 0){
			$result = array(
				'data' => "Age should not be negative"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
		}

		if(is_null($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			$result = array(
				'data' => "Please log in again and try"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		if (is_null($firstName) || is_null($lastName) || is_null($phone) || is_null($age))
		{
			$result = array(
				'data' => "All fields must be filled"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		$this->connect();
		$escapeEmail = $this->conn->real_escape_string($email);
		$escapeFName = $this->conn->real_escape_string($firstName);
		$escapeLName = $this->conn->real_escape_string($lastName);
		$escapePhone = $this->conn->real_escape_string($phone);
		$escapeAge;
		
		if (!is_null($age)){
			$escapeAge = $this->conn->real_escape_string($age);
			$updateProfileSql = "update " . $table . "set firstname=?, lastname=?, phone=?, age=? where email = ?";
			$stmt = $this->conn->prepare($updateProfileSql);
			$stmt->bind_param('ssdds', $escapeFName, $escapeLName, $escapePhone, $escapeAge, $escapeEmail);
		}else{
			$updateProfileSql = "update " . $table . "set firstname=?, lastname=?, phone=? where email = ?";
			$stmt = $this->conn->prepare($updateProfileSql);
			$stmt->bind_param('ssds', $escapeFName, $escapeLName, $escapePhone, $escapeEmail);
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

	private function updatePassword($db, $dataArray)
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
		
		$table = $db;
		$email = $data["email"];
		$oldPassword = $data["oldPassword"];
		$newPassword = $data["newPassword"];
		$rePassword = $data["rePassword"];
		$checkPassword;
		$result;
		$statusCode;

		if(is_null($oldPassword) || is_null($password) || is_null($rePassword)) 
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
		$stmt = $this->conn->prepare($checkEmailSql);
		$stmt->bind_param('s', $escapeEmail);
		$stmt->execute();
		$res = $stmt->get_result();
		$checkPassword = $res->fetch_all(MYSQLI_ASSOC);
	    $stmt->close();
	    
	    // check if password is correct
	    if (!password_verify($escapeOldPass, $checkPassword["password"]))
		{ 
			$this->disconnect(); 
			$result = array(
				'data' => "Password is incorrect"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
	
		$updatePassSql = "update " . $table . "set password=? where email = ?";
		$stmt = $this->conn->prepare($updatePassSql);
		$stmt->bind_param('s', $hashPass);
		$stmt->execute();
		$stmt->close();
		$result = array(
					'data' => True
				);
		$statusCode = 200;
		
		$this->disconnect();
		$this->response($result, $statusCode);
	}

	private function getInvitation($db, $dataArray){
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
		$table = $db;
		$email = $data["email"];
		$page = $data["page"];
		$result;
		$statusCode;
		
		$this->connect();
		$escapeEmail = $this->conn->real_escape_string($email);
		// $getInvitationSql = "select email, invitationId, eventName, lat, lon, timeStart, timeEnd from ".$table." where sendToEmail = ? LIMIT ".$this->LIMIT; //." OFFSET ".$page;
		$getInvitationSql= "SELECT 
        R.eventName AS eventName,
        R.lat AS lat,
        R.lon AS lon,
        R.timeStart AS timeStart,
        R.timeEnd AS timeEnd,
        R.cost AS cost,
        R.description AS description,
        R.createdBy AS createdBy,
        R.category AS category,
        SUM(CASE
        WHEN uge.email = ? THEN 1
        ELSE 0
        END) AS going
        FROM
        (SELECT 
        e.eventName AS eventName,
        e.lat AS lat,
        e.lon AS lon,
        e.timeStart AS timeStart,
        e.timeEnd AS timeEnd,
        e.cost AS cost,
        e.description AS description,
        e.createdBy AS createdBy,
        GROUP_CONCAT(et.category
        SEPARATOR ', ') AS category
        FROM
        `Event` e
        NATURAL LEFT JOIN EventTypeHasEvent eht
        NATURAL LEFT JOIN EventType et
        WHERE
        eventName LIKE ?
        AND NOT EXISTS( SELECT 
                *
        FROM
        PrivateEvent pe
        WHERE
        pe.eventName = e.eventName
        AND pe.lat = e.lat
        AND pe.lon = e.lon
        AND pe.timeStart = e.timeStart
        AND pe.timeEnd = e.timeEnd)
        GROUP BY eventName , lat , lon , timeStart , timeEnd UNION (SELECT 
        e.eventName AS eventName,
        e.lat AS lat,
        e.lon AS lon,
        e.timeStart AS timeStart,
        e.timeEnd AS timeEnd,
        e.cost AS cost,
        e.description AS description,
        e.createdBy AS createdBy,
        GROUP_CONCAT(et.category
        SEPARATOR ', ') AS category
        FROM
        EventTypeHasEvent eht
        NATURAL LEFT JOIN `Event` e
        NATURAL LEFT JOIN EventType et
        WHERE
        createdBy LIKE ?
        AND NOT EXISTS( SELECT 
                *
        FROM
        PrivateEvent pe
        WHERE
        pe.eventName = e.eventName
        AND pe.lat = e.lat
        AND pe.lon = e.lon
        AND pe.timeStart = e.timeStart
        AND pe.timeEnd = e.timeEnd)
        GROUP BY eventName , lat , lon , timeStart , timeEnd) UNION (SELECT 
        e.eventName AS eventName,
        e.lat AS lat,
        e.lon AS lon,
        e.timeStart AS timeStart,
        e.timeEnd AS timeEnd,
        e.cost AS cost,
        e.description AS description,
        e.createdBy AS createdBy,
        GROUP_CONCAT(et.category
        SEPARATOR ', ') AS category
        FROM
        EventTypeHasEvent eht
        NATURAL LEFT JOIN `Event` e
        NATURAL LEFT JOIN EventType et
        WHERE
        description LIKE ?
        AND NOT EXISTS( SELECT 
                *
        FROM
        PrivateEvent pe
        WHERE
        pe.eventName = e.eventName
        AND pe.lat = e.lat
        AND pe.lon = e.lon
        AND pe.timeStart = e.timeStart
        AND pe.timeEnd = e.timeEnd)
        GROUP BY eventName , lat , lon , timeStart , timeEnd)) R
        NATURAL LEFT JOIN
        UserGoesEvent uge
        GROUP BY R.eventName , R.lat , R.lon , R.timeStart , R.timeEnd , R.cost , R.description , R.createdBy , R.category";

		$stmt = $this->conn->prepare($getInvitationSql);
		$stmt->bind_param('s', $escapeEmail);
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

			if (isset($dataObj["password"])){
				$data = $dataObj["password"];
				$this->updatePassword($table, $data);
			}
		}else{
			$this->response("Method Not Allowed", 405);
		}
	}

	public function admin()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST')
		{
			$table = "Admin";
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->createUser($table, $data);
			$this->response($result, 200);
		}
	}
}

?>
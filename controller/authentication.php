<?php

require_once(__DIR__.'/database.php');

class Authentication extends Database
{

	function __construct(){
		parent::__construct();
	}

	private function createUser($db, $dataArray)
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
		
		$database = $db;
		$email = $data["email"];
		$password = $data["password"];
		$rePassword = $data["rePassword"];
		$firstName = $data["firstName"];
		$lastName = $data["lastName"];
		$phone = $data["phone"];
		$age = $data["age"];
		$result;
		$statusCode;

		if (array_key_exists("age", $data)){
			$age = $data["age"];
		}

		if(is_null($email) || is_null($password) || is_null($rePassword)) 
		{
			$result = array(
				'data' => "Email|Password|Confirm Password missing"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$result = array(
				'data' => "Incorrect Email format"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}

		$this->connect();
		$escapePass = $this->conn->real_escape_string($password);
		$escapeRePass = $this->conn->real_escape_string($rePassword);
		$escapeEmail = $this->conn->real_escape_string($email);
		$escapeFName = $this->conn->real_escape_string($firstName);
		$escapeLName = $this->conn->real_escape_string($lastName);
		$escapePhone = $this->conn->real_escape_string($phone);

		$hashPass = password_hash($escapePass, PASSWORD_BCRYPT);
		if (!password_verify($escapeRePass, $hashPass))
		{ 
			$this->disconnect(); 
			$result = array(
				'data' => "Password|Re-password are different"
				);
			$statusCode = 400;
			$this->response($result, $statusCode);
			return;
		}
		if (!is_null($age)){
			$escapeAge = $this->conn->real_escape_string($age);
		}

		// check User
		$checkEmailSql = "select * from " . $database . " where email = ?";
		$checkPhoneSql = "select * from " . $database . " where phone = ?";
		
		if ($escapeAge){
			$checkNameAgeSql = "select * from " . $database . " where firstName = ? AND lastName = ? AND age = ?";
			$stmtCheckNameAge = $this->conn->prepare($checkNameAgeSql);
			$stmtCheckNameAge->bind_param('ssi', $escapeFName, $escapeLName, $escapeAge);
			$stmtCheckNameAge->execute();
			$stmtCheckNameAge->store_result();
			if($stmtCheckNameAge->num_rows != 0){
				$result = array(
					'data' => "First & Last Name, Age combination already registered"
					);
				$statusCode = 400;
				$stmtCheckNameAge->close();
				$this->disconnect();
				$this->response($result, $statusCode);
			}
		}

		$stmtCheckEmail = $this->conn->prepare($checkEmailSql);
		$stmtCheckEmail->bind_param('s', $escapeEmail);
		$stmtCheckEmail->execute();
		$stmtCheckEmail->store_result();
		
		if($stmtCheckEmail->num_rows != 0){
			$result = array(
				'data' => "Email already registered"
				);
			$statusCode = 400;
			$stmtCheckEmail->close();
			$this->disconnect();
			$this->response($result, $statusCode);
		}

		$stmtCheckPhone = $this->conn->prepare($checkPhoneSql);
		$stmtCheckPhone->bind_param('i', $escapePhone);
		$stmtCheckPhone->execute();
		$stmtCheckPhone->store_result();
		
		if($stmtCheckPhone->num_rows != 0){
			$result = array(
				'data' => "Phone Number already registered"
				);
			$statusCode = 400;
			$stmtCheckPhone->close();
			$this->disconnect();
			$this->response($result, $statusCode);
		}

		
		$createSql = "insert into " . $database;
		if ($database == "User")
		{
			$columns = "(email, password, firstname, lastname, phone, age) VALUES (?,?,?,?,?,?)";
			$sql = $createSql.$columns;
			$stmt = $this->conn->prepare($sql);
			$stmt->bind_param('ssssdd', $escapeEmail, $hashPass, $escapeFName, $escapeLName, $escapePhone, $escapeAge);
			$stmt->execute();
			$stmt->close();

			$result = array(
				'data' => True
			);
			$statusCode = 200;
		}

		if ($database == "EventProvider")
		{
			$columns = "(email, password, firstname, lastname, phone) VALUES (?,?,?,?,?)";
			$sql = $createSql.$columns;
			$stmt = $this->conn->prepare($sql);
			$stmt->bind_param('ssssd', $escapeEmail, $hashPass, $escapeFName, $escapeLName, $escapePhone);
			$stmt->execute();
			$stmt->close();

			$result = array(
				'data' => True
			);
			$statusCode = 200;
		}

		if ($database == "Admin")
		{
			$columns = "(email, password, firstname, lastname, phone) VALUES (?,?,?,?,?)";
			$sql = $createSql.$columns;
			$stmt = $this->conn->prepare($sql);
			$stmt->bind_param('ssssd', $escapeEmail, $hashPass, $escapeFName, $escapeLName, $escapePhone);
			$stmt->execute();
			$stmt->close();
			
			$result = array(
				'data' => True
			);
			$statusCode = 200;
		}
		
		$this->disconnect();
		$this->response($result, $statusCode);
	}

	public function user() {
		
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST')
		{
			$database = "User";
			$json = file_get_contents("php://input");
			$data = json_decode($json,TRUE);
			$result = $this->createUser($database, $data);
			$this->response($result, 200);
		}else{
			$this->response("Method Not Allowed", 405);
		}
	}

	public function eventProvider()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST')
		{
			$database = "EventProvider";
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->createUser($database, $data);
			$this->response($result, 200);
		}
	}

	public function admin() {

		$method = $_SERVER['REQUEST_METHOD'];
		if ($method == 'POST')
		{
			$database = "Admin";
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->createUser($database, $data);
			$this->response($result, 200);
		}
	}
	
}

// $authentication = new Authentication();
// $result = $authentication->register();

// echo $result;

?>
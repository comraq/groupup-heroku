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
			$statusCode = 404;
			$this->response($result, $statusCode);
			exit;
		}
		
		$database = $db;
		$email = $data["email"];
		$password = $data["password"];
		$rePassword = $data["rePassword"];
		$firstName = $data["firstName"];
		$lastName = $data["lastName"];
		$phone = $data["phone"];
		$age;
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
			$statusCode = 404;
			$this->response($result, $statusCode);
			exit;
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$result = array(
				'data' => "Incorrect Email format"
				);
			$statusCode = 404;
			$this->response($result, $statusCode);
			exit;
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
			$statusCode = 404;
			$this->response($result, $statusCode);
			exit;
		}
		if (!is_null($age)){
			$escapeAge = $this->conn->real_escape_string($age);
		}

		// check User
		$checkEmailSql = "select * from " . $database . " where email = ?";

		$stmt = $this->conn->prepare($checkEmailSql);
		$stmt->bind_param('s', $escapeEmail);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows == 0)
		{	
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
		}else{
			$result = array(
				'data' => "Email already exists"
				);
			$statusCode = 404;
		}
		
		$this->disconnect();
		$this->response($data, $statusCode);
	}

	public function user()
	{
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

	public function admin()
	{
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
<?php

require_once(__DIR__.'/database.php');

class Authentication extends Database{

	function __construct(){
		parent::__construct();
	}

	private function createUser($db, $dataArray)
	{
		$database = $db;
		$data = $dataArray;
		$email = $data["email"];
		$password = $data["password"];
		$rePassword = $data["rePassword"];
		$firstName = $data["firstName"];
		$lastName = $data["lastName"];
		$phone = $data["phone"];
		$age;
		$result;
		if (array_key_exists("age", $data)){
			$age = $data["age"];
		}

		if(is_null($email) || is_null($password) || is_null($rePassword)) return "Email|Password|Confirm Password missing";
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return "Incorrect Email format";

		parent::connect();
		$escapePass = $this->conn->real_escape_string($password);
		$escapeRePass = $this->conn->real_escape_string($rePassword);
		$escapeEmail = $this->conn->real_escape_string($email);
		$escapeFName = $this->conn->real_escape_string($firstName);
		$escapeLName = $this->conn->real_escape_string($lastName);
		$escapePhone = $this->conn->real_escape_string($phone);

		$hashPass = password_hash($escapePass, PASSWORD_BCRYPT);
		if (!password_verify($escapeRePass, $hashPass))
		{ 
			parent::disconnect(); 
			return "wrong password";
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
				$result =  TRUE;
			}

			if ($database == "EventProvider")
			{
				$columns = "(email, password, firstname, lastname, phone) VALUES (?,?,?,?,?)";
				$sql = $createSql.$columns;
				$stmt = $this->conn->prepare($sql);
				$stmt->bind_param('ssssd', $escapeEmail, $hashPass, $escapeFName, $escapeLName, $escapePhone);
				$stmt->execute();
				$stmt->close();
				$result =  TRUE;
			}

			if ($database == "Admin")
			{
				$columns = "(email, password, firstname, lastname, phone) VALUES (?,?,?,?,?)";
				$sql = $createSql.$columns;
				$stmt = $this->conn->prepare($sql);
				$stmt->bind_param('ssssd', $escapeEmail, $hashPass, $escapeFName, $escapeLName, $escapePhone);
				$stmt->execute();
				$stmt->close();
				$result =  TRUE;
			}
		}else{
			$result = "Email already exists";
		}
		
		parent::disconnect();
		return $result;
	}

	public function register()
	{
		if (isset($_POST["registerUser"])){
			$test = "TRUE";
			$form = $_POST["registerUser"];
			$database = "User";
			$result = $this->createUser($database, $form);
			return $result;

		}
		if (isset($_POST["registerEProvider"])){
			$form = $_POST["registerUser"];
			$database = "EventProvider";
			$result = $this->createUser($database, $form);
			return $result;
		}
		if (isset($_POST["registerAdmin"])){
			$form = $_POST["registerUser"];
			$database = "Admin";
			$result = $this->createUser($database, $form);
			return $result;
		}

		return "No Post Set";
	}
	
}

$authentication = new Authentication();
$result = $authentication->register();

echo $result;

?>
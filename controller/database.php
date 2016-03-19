<?php

require_once(__DIR__.'/rest.inc.php');

class Database extends Rest
{

	var $servername = "localhost";
	var $username = "groupup";
	var $password = "groupup";
	var $database = "GroupUpDebug";
	public $conn;
		
	function __construct(){
		parent::__construct();
	}

	public function connect(){
		$this->conn = new mysqli($this->servername, $this->username, $this->password);

		if ($this->conn->connect_error){
			die("Connection failed: " . $this->conn->connect_error."\n");
		}
		mysqli_select_db($this->conn, $this->database) or die("Could not connect to " . $this->database);
	}

	public function disconnect(){
		$this->conn->close();
	}

}


?>
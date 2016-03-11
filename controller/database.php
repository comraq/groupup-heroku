<?php

class Database{

	var $servername = "localhost";
	var $username = "root";
	var $password = "EdwardChoi90!";
	var $database = "TEST";
	public $conn;

	function __construct(){

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
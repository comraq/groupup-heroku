<?php
require_once(__DIR__.'/database.php');

class EventType extends Database{

	function __construct(){
		parent::__construct();
	}

	function getTypes(){
		
		$this->connect();
		$searchEventsSQL = "SELECT * FROM `EventType`";

		$stmt = $this->conn->prepare($searchEventsSQL);
		$stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
		$res = $stmt->get_result();
		$data = $res->fetch_all(MYSQLI_ASSOC);
		$stmt->close();
		$this->disconnect();
		return json_encode($data);
	}

	function startGetTypes(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'GET'){
			$result = $this->getTypes();
			$this->response($result, 200);
		}else{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
		}
		exit;
	}
}
?>
<?php
require_once(__DIR__.'/database.php');

class Search extends Database{

	function __construct(){
		parent::__construct();
	}

	function searchEvents($data){		
		//$data = $_POST["searchEvents"];
		if (is_null($data))
		{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
			exit;
		}
		$searchTarget = "%".$data["searchTarget"]."%";
		
		parent::connect();

		$searchEventsSQL = "SELECT * FROM `Event` WHERE eventName LIKE ?
		UNION 
		SELECT * FROM `Event` WHERE createdBy LIKE ?
		UNION 
		SELECT * FROM `Event` WHERE description LIKE ?";

		$stmt = $this->conn->prepare($searchEventsSQL);
		$stmt->bind_param('sss', $searchTarget, $searchTarget, $searchTarget);
		$stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
		$res = $stmt->get_result();
		$result = $res->fetch_all(MYSQLI_ASSOC);
		$stmt->close();
		parent::disconnect();
		return json_encode($result);
	}

	function startSearchEvents(){
		$reqMethod = $_SERVER['REQUEST_METHOD'];
		if ($reqMethod == 'POST'){
			$json = file_get_contents("php://input");
			$data = json_decode($json, TRUE);
			$result = $this->searchEvents($data);
			$this->response($result, 200);
		}
	}
}
?>
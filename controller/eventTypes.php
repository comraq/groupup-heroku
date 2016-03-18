<?php
require_once(__DIR__.'/database.php');

class eventType extends Database{

	function __construct(){
		parent::__construct();
	}

	function getTypes(){
		
		parent::connect();
		
		$searchEventsSQL = "SELECT * FROM `EventType`";

		$stmt = $this->conn->prepare($searchEventsSQL);
		$stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
		$res = $stmt->get_result();
		$data = $res->fetch_all( MYSQLI_ASSOC );
		print json_encode( $data );
		$stmt->close();
		parent::disconnect();
		return TRUE;
	}
}
$eventType = new eventType();
$result = $eventType->getTypes();
return $result;
?>
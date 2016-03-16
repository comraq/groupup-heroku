<?php
require_once(__DIR__.'/database.php');

class Search extends Database{

	function __construct(){
		parent::__construct();
	}

	function searchEvents(){
		$data = $_POST["searchEvents"];
		$searchTarget = "%".$data["searchTarget"]."%";
		
		if (array_key_exists("lat", $data)){
			$lat = $data["lat"];
		}if (array_key_exists("lon", $data)){
			$lon = $data["lon"];
		}
		
		parent::connect();
		
		$searchEventsSQL = "SELECT DISTINCT * FROM `Event` WHERE eventName LIKE ?
		UNION 
		SELECT DISTINCT * FROM `Event` WHERE createdBy LIKE ?
		UNION 
		SELECT DISTINCT * FROM `Event` WHERE description LIKE ?";

		$stmt = $this->conn->prepare($searchEventsSQL);
		$stmt->bind_param('sss', $searchTarget, $searchTarget, $searchTarget);
		$stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
		$res = $stmt->get_result();
		$data = $res->fetch_all( MYSQLI_ASSOC );
		print json_encode( $data );
		parent::disconnect();
		return TRUE;
	}
}
$search = new Search();
$result = $search->searchEvents();
return $result;
?>
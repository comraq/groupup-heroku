<?php
require_once(__DIR__.'/database.php');

class Search extends Database{

	function __construct(){
		parent::__construct();
	}

	function searchGroups(){
		$data = $_POST["searchGroups"];
		$searchTarget = "%".$data["searchTarget"]."%";
		
		if (array_key_exists("lat", $data)){
			$lat = $data["lat"];
		}if (array_key_exists("lon", $data)){
			$lon = $data["lon"];
		}
		
		parent::connect();
		
		$searchGroupsSQL = "select * from `Group` where groupName like ?";

		$stmt = $this->conn->prepare($searchGroupsSQL);
		$stmt->bind_param('s', $searchTarget);
		$stmt->execute();
		$stmt->store_result();

		parent::disconnect();
		return $stmt->num_rows;
	}
}
$search = new Search();
$result = $search->searchGroups();

echo $result;

?>
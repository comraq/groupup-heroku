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
		
		$searchEventsSQL = "select * from `Event` where eventName like ?";

		$stmt = $this->conn->prepare($searchEventsSQL);
		$stmt->bind_param('s', $searchTarget);
		$stmt->execute();
		$result = $stmt->get_result();
        while ($row = $result->fetch_array(MYSQLI_NUM))
        {
            foreach ($row as $r)
            {
                $results[] = $r;
                print "$r ";
            }
            print "\n";
        }
		parent::disconnect();
		return $results;
		

	
		//return $stmt->num_rows;
	}
}
$search = new Search();
$result = $search->searchEvents();
return $result;
?>
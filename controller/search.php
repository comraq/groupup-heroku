<?php

require_once(__DIR__.'/database.php');

class Search extends Database
{

	function __construct(){
		parent::__construct();
	}

	function searchEvents($data){		
		if (is_null($data))
		{
			$result = array(
				'data' => "Emtpy Data"
				);
			$statusCode = 405;
			$this->response($result, $statusCode);
			exit;
		}
        $this->connect();
        $searchString = $data["searchTarget"];   
        $escSearch  = $this->conn->real_escape_string($searchString);
        $searchTarget = "%".$escSearch."%";

        $searchEventsSQL = "SELECT 
    R.eventName AS eventName,
    R.lat AS lat,
    R.lon AS lon,
    R.timeStart AS timeStart,
    R.timeEnd AS timeEnd,
    R.cost AS cost,
    R.description AS description,
    R.createdBy AS createdBy,
    R.category AS category,
    SUM(CASE
        WHEN uge.email = 'testUser1@test.com' THEN 1
        ELSE 0
    END) AS going
FROM
    (SELECT 
        e.eventName AS eventName,
            e.lat AS lat,
            e.lon AS lon,
            e.timeStart AS timeStart,
            e.timeEnd AS timeEnd,
            e.cost AS cost,
            e.description AS description,
            e.createdBy AS createdBy,
            GROUP_CONCAT(et.category
                SEPARATOR ', ') AS category
    FROM
        `Event` e
    NATURAL LEFT JOIN EventTypeHasEvent eht
    NATURAL LEFT JOIN EventType et
    WHERE
        (eventName LIKE ?
            OR createdBy LIKE ?
            OR description LIKE ?)
            AND NOT EXISTS( SELECT 
                *
            FROM
                PrivateEvent pe
            WHERE
                pe.eventName = e.eventName
                    AND pe.lat = e.lat
                    AND pe.lon = e.lon
                    AND pe.timeStart = e.timeStart
                    AND pe.timeEnd = e.timeEnd)
    GROUP BY eventName , lat , lon , timeStart , timeEnd) R
        NATURAL LEFT JOIN
    UserGoesEvent uge
GROUP BY R.eventName , R.lat , R.lon , R.timeStart , R.timeEnd , R.cost , R.description , R.createdBy , R.category;
";
        $stmt = $this->conn->prepare($searchEventsSQL);
        $stmt->bind_param('sss', $searchTarget, $searchTarget, $searchTarget);
        $stmt->execute();
		//referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
        $res = $stmt->get_result();
        $result = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $this->disconnect();
        return json_encode($result);
    }

    function startSearchEvents(){
      $reqMethod = $_SERVER['REQUEST_METHOD'];
      if ($reqMethod == 'POST'){
         $json = file_get_contents("php://input");
         $data = json_decode($json, TRUE);
         $result = $this->searchEvents($data);
         $this->response($result, 200);
     }else{
        $result = array(
            'data' => "Emtpy Data"
            );
        $statusCode = 405;
        $this->response($result, $statusCode);
        exit;
    }
}
}
?>
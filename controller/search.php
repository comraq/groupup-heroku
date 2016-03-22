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

		$searchEventsSQL = "SELECT 
    e.eventName as eventName,
    e.lat as lat,
    e.lon as lon,
    e.timeStart as timeStart,
    e.timeEnd as timeEnd,
    e.cost as cost,
    e.description as description,
    e.createdBy as createdBy,
    GROUP_CONCAT(et.category
        SEPARATOR ', ') as category
FROM
    `Event` e
        NATURAL LEFT JOIN
    EventTypeHasEvent eht
        NATURAL LEFT JOIN
    EventType et
WHERE
    eventName LIKE ? AND NOT EXISTS (SELECT * FROM PrivateEvent pe WHERE pe.eventName = e.eventName AND  pe.lat = e.lat AND pe.lon =e.lon AND pe.timeStart = e.timeStart AND pe.timeEnd =e.timeEnd)
GROUP BY eventName , lat, lon, timeStart, timeEnd 
UNION (SELECT 
    e.eventName as eventName,
    e.lat as lat,
    e.lon as lon,
    e.timeStart as timeStart,
    e.timeEnd as timeEnd,
    e.cost as cost,
    e.description as description,
    e.createdBy as createdBy,
    GROUP_CONCAT(et.category
        SEPARATOR ', ') as category
FROM
    EventTypeHasEvent eht
        NATURAL LEFT JOIN
    `Event` e
        NATURAL LEFT JOIN
    EventType et
WHERE
    createdBy LIKE ? AND NOT EXISTS (SELECT * FROM PrivateEvent pe WHERE pe.eventName = e.eventName AND  pe.lat = e.lat AND pe.lon =e.lon AND pe.timeStart = e.timeStart AND pe.timeEnd =e.timeEnd)
GROUP BY eventName , lat, lon, timeStart, timeEnd) UNION (SELECT 
    e.eventName as eventName,
    e.lat as lat,
    e.lon as lon,
    e.timeStart as timeStart,
    e.timeEnd as timeEnd,
    e.cost as cost,
    e.description as description,
    e.createdBy as createdBy,
    GROUP_CONCAT(et.category
        SEPARATOR ', ') as category
FROM
    EventTypeHasEvent eht
        NATURAL LEFT JOIN
    `Event` e
        NATURAL LEFT JOIN
    EventType et
WHERE
    description LIKE ? AND NOT EXISTS (SELECT * FROM PrivateEvent pe WHERE pe.eventName = e.eventName AND  pe.lat = e.lat AND pe.lon =e.lon AND pe.timeStart = e.timeStart AND pe.timeEnd =e.timeEnd)
GROUP BY eventName , lat, lon, timeStart, timeEnd)";

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
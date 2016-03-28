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

        $searchName =$data["searchName"];
        $searchNameOperator = $data["searchNameOperator"];
        $searchTimeStart =$data["searchTimeStart"];
        $searchTimeStartLogic =$data["searchTimeStartLogic"];
        $searchTimeStartOperator =$data["searchTimeStartOperator"];
        $searchTimeEnd =$data["searchTimeEnd"];
        $searchTimeEndLogic =$data["searchTimeEndLogic"];
        $searchTimeEndOperator =$data["searchTimeEndOperator"];
        $searchCost =$data["searchCost"];
        $searchCostLogic =$data["searchCostLogic"];
        $searchCostOperator =$data["searchCostOperator"];
        $searchDesctipion =$data["searchDesctipion"];
        $searchDesctipionLogic =$data["searchDesctipionLogic"];
        $searchDesctipionOperator =$data["searchDesctipionOperator"];
        $searchEventType =$data["searchEventType"];
        $searchEventTypeLogic =$data["searchEventTypeLogic"];
        $searchEventTypeOperator =$data["searchEventTypeOperator"];
        $searchCreatedBy =$data["searchCreatedBy"];
        $searchCreatedByLogic =$data["searchCreatedByLogic"];
        $searchCreatedByOperator =$data["searchCreatedByOperator"];


        if ($searchNameOperator == 'LIKE' || $searchNameOperator == 'IS NOT LIKE') {
            $searchName = '%'.$this->conn->real_escape_string($searchName).'%';
        }
        if ($searchTimeStartOperator == 'LIKE' || $searchTimeStartOperator == 'IS NOT LIKE') {
            $searchTimeStart = '%'.$this->conn->real_escape_string($searchTimeStart).'%';
        }
        if ($searchTimeStartOperator == 'LIKE' || $searchTimeStartOperator == 'IS NOT LIKE') {
            $searchTimeStart = '%'.$this->conn->real_escape_string($searchTimeStart).'%';
        }
        if ($searchTimeEndOperator == 'LIKE' || $searchTimeEndOperator == 'IS NOT LIKE') {
            $searchTimeEnd = '%'.$this->conn->real_escape_string($searchTimeEnd).'%';
        }
        if ($searchCostOperator == 'LIKE' || $searchCostOperator == 'IS NOT LIKE') {
            $searchCost = '%'.$this->conn->real_escape_string($searchCost).'%';
        }
        if ($searchDesctipionOperator == 'LIKE' || $searchDesctipionOperator == 'IS NOT LIKE') {
            $searchDesctipion = '%'.$this->conn->real_escape_string($searchDesctipion).'%';
        }
        if ($searchCreatedByOperator == 'LIKE' || $searchCreatedByOperator == 'IS NOT LIKE') {
            $searchCreatedBy = '%'.$this->conn->real_escape_string($searchCreatedBy).'%';
        }


        $nameQuery = "eventName ".$searchNameOperator;
        if ($searchName) {   
            $nameQuery.=" '".$searchName."' ";
        }

        $timeStartQuery = '';
        if ($searchTimeStartLogic && $searchTimeStartOperator) {
            $timeStartQuery.=$searchTimeStartLogic." timeStart ".$searchTimeStartOperator;
            if ($searchTimeStart) {
                $timeStartQuery.=" '".$searchTimeStart."' ";
            }
        }

        $timeEndQuery = '';
        if ($searchTimeEndLogic && $searchTimeEndOperator) {
            $timeEndQuery.=$searchTimeEndLogic." timeEnd ".$searchTimeEndOperator;
            if ($searchTimeStart) {
                $timeEndQuery.=" '".$searchTimeEnd."' ";
            }
        }

        $costQuery = '';
        if ($searchCostLogic && $searchCostOperator) {
            $costQuery.=$searchCostLogic." cost ".$searchCostOperator;
            if ($searchCost) {
                $costQuery.=" ".$searchCost." ";
            }
        }

        $descriptionQuery = '';
        if ($searchDesctipionLogic && $searchDesctipionOperator) {
            $descriptionQuery.=$searchDesctipionLogic." desctiption ".$searchDesctipionOperator;
            if ($searchDesctipion) {
                $descriptionQuery.=" '".$searchDesctipion."' ";
            }
        };

        $createdByQuery = '';
        if ($searchCreatedByLogic && $searchCreatedByOperator) {
            $createdByQuery.=$searchCreatedByLogic." createdBy ".$searchCreatedByOperator;
            if ($searchCreatedBy) {
                $createdByQuery.=" '".$searchCreatedBy."' ";
            }
        }

        $whenQuery = $nameQuery.$timeStartQuery.$timeEndQuery.$costQuery.$descriptionQuery.$createdByQuery;

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
        (".$whenQuery .")
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
        GROUP BY R.eventName , R.lat , R.lon , R.timeStart , R.timeEnd , R.cost , R.description , R.createdBy , R.category
        ";
        $stmt = $this->conn->prepare($searchEventsSQL);
        if(!$stmt->execute()){
            $result = array('data' => "There was an error deleting the event from the databse", 'code'=> 500);
            $stmt->close();
        }else{
           //referenced http://stackoverflow.com/questions/11892699/how-do-i-properly-use-php-to-encode-mysql-object-into-json
            $res = $stmt->get_result();
            $rows = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            $this->disconnect();
            $result = array('data' => json_encode($rows, JSON_NUMERIC_CHECK), 'code' => 200);
        }
        return $result;
    }

    function startSearchEvents(){
      $reqMethod = $_SERVER['REQUEST_METHOD'];
      if ($reqMethod == 'POST'){
         $json = file_get_contents("php://input");
         $data = json_decode($json, TRUE);
         $result = $this->searchEvents($data);
        $this->response($result["data"], $result["code"]);
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
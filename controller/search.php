<?php

require_once(__DIR__.'/database.php');

class Search extends Database
{

	function __construct(){
		parent::__construct();
        mysqli_report(MYSQLI_REPORT_ERROR);
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
        $email = $data["email"];
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
        $searchDesc =$data["searchDesc"];
        $searchDescLogic =$data["searchDescLogic"];
        $searchDescOperator =$data["searchDescOperator"];
        $searchEventType =$data["searchEventType"];
        $searchEventTypeLogic =$data["searchEventTypeLogic"];
        $searchEventTypeOperator =$data["searchEventTypeOperator"];
        $searchCreatedBy =$data["searchCreatedBy"];
        $searchCreatedByLogic =$data["searchCreatedByLogic"];
        $searchCreatedByOperator =$data["searchCreatedByOperator"];


        $nameQuery = '';

        if($searchNameOperator){
            if ($searchNameOperator == 'LIKE' || $searchNameOperator == 'NOT LIKE') {
                $searchName = '%'.$searchName.'%';
            }
            $nameQuery.="AND eventName ".$searchNameOperator." ";
            if ($searchName) {   
                $nameQuery.="'".$searchName."' ";
            }
        };


        $timeStartQuery = '';
        if ($searchTimeStartLogic && $searchTimeStartOperator) {

            if ($searchTimeStartOperator == 'LIKE' || $searchTimeStartOperator == 'NOT LIKE') {
                $searchTimeStart = '%'.$searchTimeStart.'%';
            }
            $timeStartQuery.=$searchTimeStartLogic." timeStart ".$searchTimeStartOperator." ";
            if ($searchTimeStart) {
                $timeStartQuery.="'".$searchTimeStart."' ";
            }
        }

        $timeEndQuery = '';
        if ($searchTimeEndLogic && $searchTimeEndOperator) {
         if ($searchTimeEndOperator == 'LIKE' || $searchTimeEndOperator == 'NOT LIKE') {
            $searchTimeEnd = '%'.$searchTimeEnd.'%';
        }
        $timeEndQuery.=$searchTimeEndLogic." timeEnd ".$searchTimeEndOperator." ";
        if ($searchTimeEnd) {
            $timeEndQuery.="'".$searchTimeEnd."' ";
        }
    }

    $costQuery = '';
    if ($searchCostLogic && strval($searchCostOperator)) {
        $costQuery.=$searchCostLogic." cost ".$searchCostOperator." ";
        if ($searchCostOperator == 'LIKE' || $searchCostOperator == 'NOT LIKE') {
            $searchCost = '%'.strval($searchCost).'%';
        }

        if (strval($searchCost)) {
            $costQuery.=$searchCost." ";
        }
    }

    $descriptionQuery = '';

    if ($searchDescLogic && $searchDescOperator) {

        if ($searchDescOperator == 'LIKE' || $searchDescOperator == 'NOT LIKE') {
            $searchDesc = '%'.$searchDesc.'%';
        }

        $descriptionQuery.=$searchDescLogic." description ".$searchDescOperator." ";
        if ($searchDesc) {
            $descriptionQuery.="'".$searchDesc."' ";
        }
    };

    $createdByQuery = '';
    if ($searchCreatedByLogic && $searchCreatedByOperator) {

        if ($searchCreatedByOperator == 'LIKE' || $searchCreatedByOperator == 'NOT LIKE') {
            $searchCreatedBy = '%'.$searchCreatedBy.'%';
        }
        $createdByQuery.=$searchCreatedByLogic." createdBy ".$searchCreatedByOperator." ";
        if ($searchCreatedBy) {
            $createdByQuery.="'".$searchCreatedBy."' ";
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
    WHEN uge.email = '".$email."' THEN 1
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
    WHERE NOT EXISTS( SELECT 
                *
    FROM
    PrivateEvent pe
    WHERE
    pe.eventName = e.eventName
    AND pe.lat = e.lat
    AND pe.lon = e.lon
    AND pe.timeStart = e.timeStart
    AND pe.timeEnd = e.timeEnd)
    ".$whenQuery."
    GROUP BY eventName , lat , lon , timeStart , timeEnd) R
    NATURAL LEFT JOIN
    UserGoesEvent uge
    GROUP BY R.eventName , R.lat , R.lon , R.timeStart , R.timeEnd , R.cost , R.description , R.createdBy , R.category
    ";

    if(!$stmt = $this->conn->prepare($searchEventsSQL)){
        $result = array('data' => "There was an internal error processing your query", 'code'=> 500);
    } else if(!$stmt->execute()){
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

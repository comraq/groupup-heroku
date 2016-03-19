<?php
  require_once(__DIR__.'/database.php');

  class GroupController extends Database {
    function __construct() {
      parent::__construct();
    }

    function queryGroups() {
      parent::connect();

      $allGroupsSql = 'select * from `Group`';
      $stmt = $this->conn->prepare($allGroupsSql);
      $stmt->execute();
      $res = $stmt->get_result();
      $data = $res->fetch_all(MYSQLI_ASSOC);

      parent::disconnect();
      return json_encode( $data );
    }
  }

/*
  $groupController = new GroupController();
  $reqMethod = $_SERVER['REQUEST_METHOD'];
  if ($reqMethod == 'GET')
    $response = $groupController->queryGroups();
    
  if (isset($response))
    echo $response;
*/
?>

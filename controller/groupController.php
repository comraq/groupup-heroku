<?php
  require_once(__DIR__.'/database.php');

  class GroupController extends Database {
    function __construct() {
      parent::__construct();
    }

    function queryGroups() {
      parent::connect();

      $allGroupsSql =
        'select G.groupName, G.description, G.groupId,
                W.email, W.eventName, W.timeStart
         from `With` W, `Group` G
         where W.groupId = G.groupId
         order by G.groupId';
      $stmt = $this->conn->prepare($allGroupsSql);
      $stmt->execute();
      $res = $stmt->get_result();
      $data = GroupController::aggregateByGroup(
        $res->fetch_all(MYSQLI_ASSOC));

      parent::disconnect();
      $this->response(json_encode($data), 200);
    }

    private static function aggregateByGroup($data) {
      $result = array();
      $filter = array('email', 'eventName', 'timeStart');
      foreach ($data as $entry) {
        if (!isset($curGroup)
            || $curGroup['groupId'] != $entry['groupId']) {
          if (isset($curGroup))
            array_push($result, $curGroup);
 
          $curGroup = $entry;
          $curGroup['events'] = array(
            GroupController::aggregateEmailAttr($entry, $filter, true));
          
        } else if ($curGroup['groupId'] == $entry['groupId']) {
          array_push($curGroup['events'], 
            GroupController::aggregateEmailAttr($entry, $filter,false));
        }
      }
      if (isset($curGroup))
        array_push($result, $curGroup);

      return $result;
    }

    private static function aggregateEmailAttr($instance,
                                               $filter, $remove) {
      $event = (object) [];
      foreach ($filter as $attr) {
        $event->$attr = $instance[$attr];
        if ($remove)
          unset($instance[$attr]);
      }
      return $event;     
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

<?php
  require_once(__DIR__.'/database.php');

  class GroupController extends Database {
    function __construct() {
      parent::__construct();
    }

    function queryGroups() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'GET') {
        $res = array(
                 'data' => "Invalid Request Type!"
               );
        $this.response($res, 400);
      }

      $this->connect();
      $allGroupsSql =
        'select G.groupName, G.description, G.groupId,
                W.eventName, W.timeStart, W.email
         from `With` W inner join `Group` G
         on W.groupId = G.groupId
         order by G.groupId';
      $stmt = $this->conn->prepare($allGroupsSql);
      $stmt->execute();
      $res = $stmt->get_result();
      $data = GroupController::aggregateByGroup(
        $res->fetch_all(MYSQLI_ASSOC));

      $this->disconnect();
      $this->response(json_encode($data), 200);
    }

    private static function aggregateByGroup($data) {
      $result = array();
      $filter = array('eventName', 'timeStart', 'email');
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

    function queryEvents() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'GET') {
        $res = array(
                 'data' => "Invalid Request Type!"
               );
        $this.response($res, 400);
      }

      $this->connect();
      $allEventsSql =
        'select E.eventName, E.timeStart, E.timeEnd, E.cost,
                E.lat, E.lon, ifnull(T.category, "None") as category,
                if(count(*) - 1 > 0, concat("+ ", count(*) - 1, " more"),
                NULL) as remaining
         from Event E
           left join EventTypeHasEvent ET
             on E.eventName = ET.eventName and
                E.timeStart = ET.timeStart and
                E.timeEnd = ET.timeEnd and
                E.lat = ET.lat and
                E.lon = ET.lon
           left join EventType T
             on ET.eventTypeId = T.eventTypeId
         group by E.eventName, E.timeStart, E.timeEnd, E.lat, E.lon';
      $stmt = $this->conn->prepare($allEventsSql);
      $stmt->execute();
      $res = $stmt->get_result();
      $data = $res->fetch_all(MYSQLI_ASSOC);

      $this->disconnect();
      $this->response(json_encode($data), 200);
    }

    private static function getUserEmail() {
      return 'testUser4@test.com';
    }
 
/*
    function insertGroup() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'POST') {
        $res = array(
                 'data' => "Invalid Request Type!"
               );
        $this.response($res, 400);
      }

      $data = json_decode(file_get_contents('php://input'), true);
      if (is_null($data)) {
        $res = array(
                 'data' => "Invalid Data!"
               );
        $this.response($res, 400);
      }

      $insertGroupSql = "insert into `Group`
                         (`groupId`, `groupName`, `description`)
                         values (?, ?, ?)";
      $this->connect();
      if (!$data['userGoesEvent']) {
        $escapeEmail = $this->conn->real_escape_string(
                         GroupController::getUserEmail()
                       );
        $insertGroupsSql =
          'select G.groupName, G.description, G.groupId,
                  W.eventName, W.timeStart, W.timeEnd
           from `With` W inner join `Group` G
           on W.groupId = G.groupId
           order by G.groupId';
        $stmt = $this->conn->prepare($insertGroupSql);
        $stmt->execute();
        $res = $stmt->get_result();
        $data = $res->fetch_all(MYSQLI_ASSOC));
      }

      $this->disconnect();
      $this->response(json_encode($data), 200);
    }
*/
  }
?>

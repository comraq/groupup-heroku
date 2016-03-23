<?php
  require_once(__DIR__.'/database.php');

  class GroupController extends Database {
    function __construct() {
      parent::__construct();
    }

    private static function getUserEmail() {
      return 'testUser4@test.com';
    }

    function getGroups() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'GET') {
        $res = array(
                 'data' => 'Invalid Request Type!'
               );
        $this->response($res, 400);
      }

      $this->connect();
      $allGroupsSql =
        'select G.groupName, G.description, G.groupId,
                W.eventName, W.timeStart, W.email
         from `With` W inner join `Group` G
         on W.groupId = G.groupId
         order by G.groupId';
      $stmt = $this->conn->prepare($allGroupsSql);
      if (!$stmt->execute()) {
        $stmt->close();
        $this->disconnect();
        $res = array(
                 'data' => 'Error Retrieving Groups Information!'
               );
        $this->response($res, 500);
      };
      $res = $stmt->get_result();
      $data = GroupController::aggregateByGroup(
        $res->fetch_all(MYSQLI_ASSOC));

      $stmt->close();
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
            GroupController::aggregateEmailAttr($entry, $filter, false));
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

    function getEvents() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'GET') {
        $res = array(
                 'data' => 'Invalid Request Type!'
               );
        $this->response($res, 400);
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
      if (!$stmt->execute()) {
        $stmt->close();
        $this->disconnect();
        $res = array(
                 'data' => 'Error Retrieving Events Information!'
               );
        $this->response($res, 500);
      };
      $res = $stmt->get_result();
      $data = $res->fetch_all(MYSQLI_ASSOC);

      $stmt->close();
      $this->disconnect();
      $this->response(json_encode($data), 200);
    }

    private static function addUserToEvents($conn, $events) {
      $insertUserGoesEventSql = "insert into `UserGoesEvent`
                                 (`email`, `eventName`, `lat`, `lon`,
                                  `timeStart`, `timeEnd`)
                                 values (?, ?, ?, ?, ?, ?)
                                 on duplicate key update
                                 email = values(email),
                                 eventName = values(eventName),
                                 lat = values(lat),
                                 lon = values(lon),
                                 timeStart = values(timeStart),
                                 timeEnd = values(timeEnd)";
      $escEmail = $conn->real_escape_string(
                 GroupController::getUserEmail());
      $stmt = $conn->prepare($insertUserGoesEventSql);
      $stmt->bind_param('ssddss', $escEmail, $escEventName,
                                  $escLat, $escLon,
                                  $escTimeStart, $escTimeEnd);

      foreach ($events as $event) {
        $escEventName = $conn->real_escape_string(
                          $event['eventName']);
        $escLat = $conn->real_escape_string($event['lat']);
        $escLon = $conn->real_escape_string($event['lon']);
        $escTimeStart = $conn->real_escape_string(
                          $event['timeStart']);
        $escTimeEnd = $conn->real_escape_string(
                        $event['timeEnd']);
        if (!$stmt->execute()) {
          $stmt->close();
          $result = array(
                      'statusCode' => 500,
                      'data' => 'Error Signing Up User for Event!'
                    );
          return $result;
        };
      }
      $stmt->close();
      return null;
    }
 
    function createGroup() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'POST') {
        $res = array(
                 'data' => 'Invalid Request Type!'
               );
        $this->response($res, 400);
      }

      $data = json_decode(file_get_contents('php://input'), true);
      if (is_null($data)) {
        $res = array(
                 'data' => "Invalid Request Body/Data!"
               );
        $this->response($res, 400);
      } else if (sizeof($data['withEvents']) < 1) {
        $res = array(
                 'data' => 'Must be Attending Event(s)
                            Before Creating/Joining a Group!'
               );
        $this->response($res, 400);
      }

      $this->connect();
      if ($data['addUserToEvents']) {
        $err = GroupController::addUserToEvents($this->conn,
                                                $data['withEvents']);
        if (!is_null($err)) {
          $this->disconnect();
          $this->response($err['data'], $err['statusCode']);
        }
      }

      $insertGroupSql = "insert into `Group`
                         (`groupName`, `description`)
                         values (?, ?)";
      $escGroupName = $this->conn->real_escape_string($data['name']);
      $escDescription = $this->conn->real_escape_string(
                          $data['description']);
      $stmt = $this->conn->prepare($insertGroupSql);
      $stmt->bind_param('ss', $escGroupName, $escDescription);
      if (!$stmt->execute()) {
        $stmt->close();
        $this->disconnect();
        $res = array(
                 'data' => 'Error Creating Group!'
               );
        $this->response($res, 500);
      };

      $groupId = $this->conn->insert_id;
      $err = GroupController::userGoesEventWithGroup(
               $this->conn, $groupId, $data['withEvents']);
      if (!is_null($err)) {
        // Failed to Add User To Group
        // Deleting Previously Created Group

        $deleteGroupSql = "delete from `Group` where groupId = ?";
        $stmt = $this->conn->prepare($deleteGroupSql);
        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $stmt->close();
        $this->disconnect();
        $this->response($err['data'], $err['statusCode']);
      }

      // New Group Successfully Inserted into Database!
      $stmt->close();
      $this->disconnect();
      $res = array(
               'data' => 'Successfully Created and Joined New Group!'
             );
      $this->response($res, 200);
    }

    private static function userGoesEventWithGroup($conn,
                                                   $groupId,
                                                   $events) {
      $insertWithSql = "insert into `With`
                        (`email`, `eventName`, `lat`, `lon`,
                         `timeStart`, `timeEnd`, `groupId`)
                        values (?, ?, ?, ?, ?, ?, ?)";
      $escEmail = $conn->real_escape_string(
                    GroupController::getUserEmail());
      $stmt = $conn->prepare($insertWithSql);
      $stmt->bind_param('ssddssi', $escEmail, $escEventName,
                                   $escLat, $escLon,
                                   $escTimeStart, $escTimeEnd, $groupId);

      foreach ($events as $event) {
        $escEventName = $conn->real_escape_string(
                          $event['eventName']);
        $escLat = $conn->real_escape_string($event['lat']);
        $escLon = $conn->real_escape_string($event['lon']);
        $escTimeStart = $conn->real_escape_string(
                          $event['timeStart']);
        $escTimeEnd = $conn->real_escape_string(
                        $event['timeEnd']);
        if (!$stmt->execute()) {
          $stmt->close();
          $result = array(
                      'statusCode' => 500,
                      'data' => 'Error Adding User to Group!'
                    );
          return $result;
        };
      }
      $stmt->close();
      return null;
    }
  }
?>

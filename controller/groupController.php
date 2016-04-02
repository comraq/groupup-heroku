<?php
  require_once(__DIR__.'/database.php');

  class GroupController extends Database {
    function __construct() {
      parent::__construct();
    }

    private static function getUserEmail() {
      return $_GET['email'];
    }

    function getGroups() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'GET') {
        $res = array(
                 'data' => 'Bad Request Method!'
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
      }
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
 
          $entry['events'] = array(
            GroupController::aggregateEventsAttr($entry, $filter, true));
          $curGroup = $entry;

        } else if ($curGroup['groupId'] == $entry['groupId']) {
          array_push($curGroup['events'], 
            GroupController::aggregateEventsAttr($entry, $filter, false));
        }
      }
      if (isset($curGroup))
        array_push($result, $curGroup);

      return $result;
    }

    private static function aggregateEventsAttr(&$instance,
                                               $filter, $remove) {
      $event = array();

      foreach ($filter as $attr) {
        $event[$attr] = $instance[$attr];

        if ($remove)
          unset($instance[$attr]);
      }
      return $event;     
    }

    function getEvents() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'GET') {
        $res = array(
                 'data' => 'Bad Request Method!'
               );
        $this->response($res, 400);
      }

      $this->connect();
      $escEmail = $this->conn->real_escape_string(
                  GroupController::getUserEmail());
      $allEventsSql =
        'select E.eventName, E.timeStart, E.timeEnd, E.cost,
                E.lat, E.lon, ifnull(T.category, "None") as category,
                if(count(*) - 1 > 0, concat("+ ", count(*) - 1, " more"),
                NULL) as remaining,
                if(U.eventName is null, false, true) as attending,
                G.groupIds
         from Event E
           left join EventTypeHasEvent ET
             on E.eventName = ET.eventName and
                E.timeStart = ET.timeStart and
                E.timeEnd = ET.timeEnd and
                E.lat = ET.lat and
                E.lon = ET.lon
           left join EventType T
             on ET.eventTypeId = T.eventTypeId
           left join (
             select eventName, lat, lon, timeStart, timeEnd,
                    true as attending
             from UserGoesEvent
             where email = ?
           ) as U
             on E.eventName = U.eventName and
                E.timeStart = U.timeStart and
                E.timeEnd = U.timeEnd and
                E.lat = U.lat and
                E.lon = U.lon
           left join (
             select U.eventName, U.lat, U.lon, U.timeStart, U.timeEnd,
                    group_concat(W.groupId, "") as groupIds
             from UserGoesEvent U inner join `With` W
             on U.eventName = W.eventName and
                U.timeStart = W.timeStart and
                U.timeEnd = W.timeEnd and
                U.lat = W.lat and
                U.lon = W.lon and
                U.email = W.email and
                U.email = ?
             group by U.eventName, U.timeStart, U.timeEnd, U.lat, U.lon
           ) as G
             on E.eventName = G.eventName and
                E.timeStart = G.timeStart and
                E.timeEnd = G.timeEnd and
                E.lat = G.lat and
                E.lon = G.lon
           left join PrivateEvent PE
             on E.eventName = PE.eventName and
                E.lat = PE.lat and
                E.lon = PE.lon and
                E.timeStart = PE.timeStart and
                E.timeEnd = PE.timeEnd
             where PE.eventName is null
         group by E.eventName, E.timeStart, E.timeEnd, E.lat, E.lon';
      $stmt = $this->conn->prepare($allEventsSql);
      $stmt->bind_param('ss', $escEmail, $escEmail);
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

    function joinLeaveGroups() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'POST') {
        $res = array(
                 'data' => 'Bad Request Method!'
               );
        $this->response($res, 400);
      }

      $data = json_decode(file_get_contents('php://input'), true);
      if (is_null($data)) {
        $res = array(
                 'data' => "POST Request, Invalid Request Body!"
               );
        $this->response($res, 400);
      } else if (sizeof($data['withEvents']) < 1) {
        $res = array(
                 'data' => 'No Events to Take Action on for Group!'
               );
        $this->response($res, 400);
      }

      $this->connect();
      $this->conn->autocommit(false);
      $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

      $err = GroupController::addUserToEvents($this->conn,
                                              $data['withEvents']);
      if (!is_null($err)) {
        $this->conn->rollback();
        $this->disconnect();
        $this->response($err['data'], $err['statusCode']);
      }

      $escGroupId = $this->conn->real_escape_string($data['groupId']);
      $escEmail = $this->conn->real_escape_string(
                 GroupController::getUserEmail());

      $insertWithSql = "insert into `With`
                         (`groupId`, `email`, `eventName`, `lat`, `lon`,
                          `timeStart`, `timeEnd`)
                         values (?, ?, ?, ?, ?, ?, ?)";

      // Prepare the insertWithSql statment
      $stmt = $this->conn->prepare($insertWithSql);
      $stmt->bind_param('issddss', $escGroupId, $escEmail, $escEventName,
                                  $escLat, $escLon,
                                  $escTimeStart, $escTimeEnd);

      // Loop through POST Body to insert UserGoesEvent into `With`
      // While also keeping track of the entries to remove from `With`
      $delIndices = array();
      for ($i = 0, $size = sizeof($data['withEvents']);
             $i < $size; ++$i) {
        $event = $data['withEvents'][$i];
        if ($event['alreadyGoing']) {
          array_push($delIndices, $i);
        } else {
          $escEventName = $this->conn->real_escape_string(
                            $event['eventName']);
          $escLat = $this->conn->real_escape_string($event['lat']);
          $escLon = $this->conn->real_escape_string($event['lon']);
          $escTimeStart = $this->conn->real_escape_string(
                            $event['timeStart']);
          $escTimeEnd = $this->conn->real_escape_string(
                          $event['timeEnd']);
          if (!$stmt->execute()) {
            $stmt->close();
            $this->conn->rollback();
            $this->disconnect();
            $res = array(
                     'data' => 'Error Adding User to Group!'
                   );
            $this->response($res, 500);
          }
        }
      }

      $deleteWithSql = "delete from `With`
                        where
                          groupId = ? and
                          email = ? and
                          eventName = ? and
                          lat = ? and
                          lon = ? and
                          timeStart = ? and
                          timeEnd = ?";

      // Prepare the deleteWithSql statment
      $stmt = $this->conn->prepare($deleteWithSql);
      $stmt->bind_param('dssddss', $escGroupId, $escEmail, $escEventName,
                                  $escLat, $escLon,
                                  $escTimeStart, $escTimeEnd);

      foreach ($delIndices as $delIndex) {
        $event = $data['withEvents'][$delIndex];
        $escEventName = $this->conn->real_escape_string(
                          $event['eventName']);
        $escLat = $this->conn->real_escape_string($event['lat']);
        $escLon = $this->conn->real_escape_string($event['lon']);
        $escTimeStart = $this->conn->real_escape_string(
                          $event['timeStart']);
        $escTimeEnd = $this->conn->real_escape_string(
                        $event['timeEnd']);
        if (!$stmt->execute()) {
          $stmt->close();
          $this->conn->rollback();
          $this->disconnect();
          $res = array(
                  'data' => 'Error Removing User from Group!'
                );
          $this->response($res, 500);
        };
      }

      $deleteGroupIfNotInWithSql = "delete from `Group`
                                      where groupId
                                        not in (
                                          select W.groupId
                                          from `With` W
                                        )";
      $stmt = $this->conn->prepare($deleteGroupIfNotInWithSql);
      if (!$stmt->execute()) {
        $stmt->close();
        $this->conn->rollback();
        $this->disconnect();
        $res = array(
                'data' => 'Error Deleting Empty Group!'
              );
        $this->response($res, 500);
      };

      // Successfully added/deleted UserGoesEvents to Group!
      $stmt->close();
      $this->conn->commit();

      $this->disconnect();
      $res = array(
               'data' => 'Successfully Updated Group Preferences!'
             );
      $this->response($res, 200);
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
        if (!$event['attending']) {
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
      }
      $stmt->close();
      return null;
    }

    function modifyGroup() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'POST') {
        $res = array(
                 'data' => 'Bad Request Method!'
               );
        $this->response($res, 400);
      }
      $data = json_decode(file_get_contents('php://input'), true);
      if (is_null($data)) {
        $res = array(
                 'data' => "POST Request, Invalid Request Body!"
               );
        $this->response($res, 400);
      } else if (is_null($data['groupId']) || $data['groupId'] < 0) {
        $res = array(
                 'data' => 'Invalid groupId!'
               );
        $this->response($res, 400);
      }

      $this->connect();
      $escGroupId = $this->conn->real_escape_string($data['groupId']);

      if ($data['delete']) {
        $msg = 'Delete';
        $deleteGroupSql = "delete from `Group`
                           where groupId = ?";
        $stmt = $this->conn->prepare($deleteGroupSql);
        $stmt->bind_param('i', $escGroupId);
      } else {
        $msg = 'Update';
        $updateGroupSql = "update `Group`
                           set groupName = ?, description = ?
                           where groupId = ?";
        $escGroupName = $this->conn->real_escape_string($data['groupName']);
        $escDescription = $this->conn->real_escape_string(
                            $data['description']);
        $stmt = $this->conn->prepare($updateGroupSql);
        $stmt->bind_param('ssi', $escGroupName, $escDescription,
                                 $escGroupId);
      }

      if (!$stmt->execute()) {
        $stmt->close();
        $this->disconnect();
        $res = array(
                 'data' => 'Error ' . substr($msg, 0, -1) . 'ing Group!'
               );
        $this->response($res, 500);
      };

      // Modify Group Successful!
      $stmt->close();
      $this->disconnect();
      $res = array(
               'data' => 'Successfully ' . $msg . 'd Group!'
             );
      $this->response($res, 200);
    }
 
    function createGroup() {
      $method = $_SERVER['REQUEST_METHOD'];
      if ($method != 'POST') {
        $res = array(
                 'data' => 'Bad Request Method!'
               );
        $this->response($res, 400);
      }

      $data = json_decode(file_get_contents('php://input'), true);
      if (is_null($data)) {
        $res = array(
                 'data' => "POST Request, Invalid Request Body!"
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
      $this->conn->autocommit(false);
      $this->conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

      $err = GroupController::addUserToEvents($this->conn,
                                              $data['withEvents']);
      if (!is_null($err)) {
        $this->conn->rollback();
        $this->disconnect();
        $this->response($err['data'], $err['statusCode']);
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
        $this->conn->rollback();
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
        // Rollback, includes deleting previously created Group
        $this->conn->rollback();
        $this->disconnect();
        $this->response($err['data'], $err['statusCode']);
      }

      // New Group Successfully Inserted into Database!
      $stmt->close();
      $this->conn->commit();

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
      $escGroupId = $conn->real_escape_string($groupId);
      $escEmail = $conn->real_escape_string(
                    GroupController::getUserEmail());
      $stmt = $conn->prepare($insertWithSql);
      $stmt->bind_param('ssddssi', $escEmail, $escEventName,
                                   $escLat, $escLon,
                                   $escTimeStart, $escTimeEnd, $escGroupId);

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

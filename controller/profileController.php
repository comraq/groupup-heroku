<?php
  require_once(__DIR__.'/database.php');

  class ProfileController extends Database {
    function __construct() {
      parent::__construct();
    }

    private static function getEProviderEmail() {
      return 'testEP5@test.com';
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
                  ProfileController::getEProviderEmail());
      $allEventsSql = "select E.eventName, E.lat, E.lon, E.description,
                              E.timeStart, E.timeEnd, E.cost,
                              count(UE.email) as numUsers
                       from Event E
                         left join UserGoesEvent UE
                           on E.eventName = UE.eventName and
                              E.lat = UE.lat and
                              E.lon = UE.lon and
                              E.timeStart = UE.timeStart and
                              E.timeEnd = UE.timeEnd
                         left join PrivateEvent PE
                           on E.eventName = PE.eventName and
                              E.lat = PE.lat and
                              E.lon = PE.lon and
                              E.timeStart = PE.timeStart and
                              E.timeEnd = PE.timeEnd
                       where PE.eventName is null and
                             E.createdBy = ?
                       group by E.eventName, E.lat, E.lon,
                                E.timeStart, E.timeEnd";

      $stmt = $this->conn->prepare($allEventsSql);
      $stmt->bind_param('s', $escEmail);
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
  }
?>

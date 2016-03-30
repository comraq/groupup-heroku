<?php
  require_once(__DIR__.'/database.php');

  class ProfileController extends Database {
    function __construct() {
      parent::__construct();
    }

    private static function getEProviderEmail() {
      return 'testEP5@test.com';
    }

    function getUsersAndEvents() {
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
      $data = array();
      $data['events'] = $res->fetch_all(MYSQLI_ASSOC);

      /*
       * The following query retrieves all users which attend events created
       * by current logged in event provider while:
       * - Providing a count of the number of events attended by each user
       *   on a per event basis (ie: group by event)
       * - Provide an additional boolean column indicating whether the user 
       *   attends all events created by event provider (division query)
       */
      $rankUsersSql = "select UE.email, count(E.eventName) as numEvents,
                              ifnull(D.attendAll, 0) as attendAll
                       from UserGoesEvent UE
                       inner join (
                         select eventName, lat, lon, timeStart, timeEnd
                         from Event
                         where createdBy = ?
                       ) as E
                         on UE.eventName = E.eventName and
                            UE.lat = E.lat and
                            UE.lon = E.lon and
                            UE.timeStart = E.timeStart and
                            UE.timeEnd = E.timeEnd
                       left join (
                         select distinct UE.email, 1 as attendAll
                         from UserGoesEvent UE
                         where not exists (
                           select *
                           from Event E
                           where E.createdBy = ? and
                                 not exists (
                                   select *
                                   from UserGoesEvent U
                                   where U.eventName = E.eventName and
                                         U.lat = E.lat and
                                         U.lon = E.lon and
                                         U.timeStart = E.timeStart and
                                         U.timeEnd = E.timeEnd and
                                         U.email = UE.email
                           )
                         )
                       ) as D
                         on UE.email = D.email
                       group by UE.email
                       order by numEvents desc";

      $stmt = $this->conn->prepare($rankUsersSql);
      $stmt->bind_param('ss', $escEmail, $escEmail);
      if (!$stmt->execute()) {
        $stmt->close();
        $this->disconnect();
        $res = array(
                 'data' => 'Error Retrieving User Information!'
               );
        $this->response($res, 500);
      };

      $res = $stmt->get_result();
      $data['users'] = $res->fetch_all(MYSQLI_ASSOC);

      $stmt->close();
      $this->disconnect();
      $this->response(json_encode($data), 200);
    }
  }
?>

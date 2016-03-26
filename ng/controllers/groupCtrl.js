var app = angular.module('groupUpApp')
                 .controller('GroupCtrl', function($scope,
                                                   $location,
                                                   $http,
                                                   $window,
                                                   $timeout,
                                                   NgMap, 
                                                   $routeParams,
                                                   $uibModal,
                                                   alertFactory) {
  var verbose = false;

  this.url = "controller/groupController";
  this.scope = $scope;
  this.location = $location;
  this.joinTab = true;
  this.scope.
      joinGroupMapModalButton = "Confirm Action for Current Group";

  /*
  /* Restricts the max number of UserGoesEvent instances per group
   * displayed in the Join Groups Tab 
   */
  this.scope.userGoesEventsPerGroupLimit = 3;

  function refreshMap(map, position) {
    if (verbose)
      console.log("refreshMap, mapId: " + map.id);

    var bounds = new google.maps.LatLngBounds();
    if (position) {
      var up = new google.maps.LatLng(position.lat, position.lon);
      bounds.extend(up);
    }
    google.maps.event.trigger(map, "resize");
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
  }

  this.joinGroupsShowMap = function joinGroupsShowMap(mapId, groupId) {
    this.joinGroupId = groupId;
    var modalInstance = $uibModal.open({
      animation: true,
      templateUrl: "ng/views/groupMap.html",
      scope: this.scope,
      size: "lg"
    });

    // this.joinGroupsMap always loses the original joinGroupsMap obj
    // Must use NgMap.getMap with explicit mapId to fetch the correct map
    NgMap.getMap({ id: mapId }).then(function(map) {
      this.joinGroupsMap = map;
      prepareMap.call(this);
    }.bind(this));
  }

  function prepareMap() {
    var map;
    if (this.joinTab)
      map = this.joinGroupsMap;
    else {
      map = this.createGroupsMap;
      this.newGroup = {
        withEvents: []
      };
    }
 
    if (verbose)
      console.log("prepareMap, joinTab = " + this.joinTab + ", mapId: "
                  + this.joinGroupsMap.id);

    $window.navigator.geolocation.getCurrentPosition(function(position) {
      var userPosition = {
                           lat: position.coords.latitude,
                           lon: position.coords.longitude
                         };
      refreshMap(map, userPosition);
      $timeout(function() {
        refreshMap(map, userPosition);
      }, 1000);
    });

    this.actionsCount = 0;
    if (!this.scope.events)
      this.getEvents();
    else {
      for (var i = 0; i < this.scope.events.length; ++i) {
        var e = this.scope.events[i];
        e.selected = false;
        e.alreadyGoing = false;
        if (e.groupIds && e.groupIds.includes(this.joinGroupId)) {
          e.selected = true;
          e.alreadyGoing = true;
        }
      };
    }
  }
  
  this.initCreateTab = function initCreateTab(mapId) {
    if (!this.joinTab)
      return;

    // For reasons unknown,
    // this.joinGroupsMap repoints to this.createGroupsMap at this point
    this.joinTab = false;
    this.joinGroupId = undefined;
    
    if (!this.createGroupsMap)
      this.createGroupsMap = NgMap.initMap(mapId);
 
    if (Object.keys($routeParams).length == 0)
      this.scope.createButton = "Join Event and Go With Created Group";
    else
      this.scope.createButton = "Go With Created Group";

    prepareMap.call(this);
  };

  this.eventsChanged = function eventsChanged(e) {
    var eventPosition = {
                          lat: e.lat,
                          lon: e.lon
                        };
    if (!e.attending) {
      if (e.selected)
        e.attendPreference = "true (was false)";
      else
        e.attendPreference = false;
    }

    if (e.selected != e.alreadyGoing)
      ++this.actionsCount;
    else
      --this.actionsCount;

    if (this.joinTab)
      refreshMap(this.joinGroupsMap, eventPosition)
    else
      refreshMap(this.createGroupsMap, eventPosition);
  }

  this.createGroup = function createGroup() {
    this.dataLoading = true;
    if (verbose) {
      console.log("Creating Group with Post Request Body: ");
      console.log(this.newGroup);
    }

    $http({
      method: "POST",
      data: this.newGroup,
      url: this.url + "/createGroup",
    }).then(function successCallback(res){
      alertFactory.add("success", res.data.data);
      this.dataLoading = false;
      $routeParams = {};

      // Clear the groups model so Join Groups tab can query for new data
      delete this.scope.groups;

    }.bind(this), function errorCallback(err){
      alertFactory.add("danger", err.data.data);
      console.log(err);
      this.dataLoading = false;

    }.bind(this));
  };

  this.getGroups = function getGroups() {
    $http({
      method: "GET",
      url: this.url + "/getGroups",
    }).then(function successCallback(res) {
      if (verbose)
        console.log("getGroups res: " + JSON.stringify(res));

      this.scope.groups = JSON.parse(res.data).map(function(e, i, arr) {
        return e;
      });

      if (verbose)
        console.log(this.scope.groups);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  };

  this.joinTabActive = function joinTabActive() {
    if (!this.scope.groups)
      this.getGroups();

    this.joinTab = true;
  }

  this.getEvents = function getEvents() {
    $http({
      method: "GET",
      url: this.url + "/getEvents"
    }).then(function successCallback(res) {
      if (verbose)
        console.log("getEvents res: " + JSON.stringify(res));
 
      this.scope.events = JSON.parse(res.data).map(function (e, i, arr) {
        if ($routeParams.eventName == e.eventName
            && $routeParams.lat == e.lat
            && $routeParams.lon == e.lon
            && $routeParams.timeStart == e.timeStart
            && $routeParams.timeEnd == e.timeEnd) {
          // Route Params Matches an Event!
          // User already added to UserGoesEvent Prior to Route
          e.selected = true;
        } else
          e.selected = false;

        e.alreadyGoing = false;
        e.lat = parseFloat(e.lat);
        e.lon = parseFloat(e.lon);
        e.attendPreference = (e.attending)? true : false;
        e.groupIds = (!e.groupIds)? [] :
                       e.groupIds.split(",").map(function(id, i, arr) {
                         var groupId = parseInt(id)
                         if (this.joinGroupId == groupId) {
                           e.selected = true;
                           e.alreadyGoing = true;
                         }

                         return groupId;
                       }.bind(this));
        return e;
      }.bind(this));

      if (verbose);
        console.log(this.scope.events);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  };

  this.joinLeaveGroups = function joinLeaveGroups() {
    this.dataLoading = true;

    var reqBody = {
      groupId: this.joinGroupId,
      withEvents: []
    };
    this.scope.events.forEach(function(e, i, arr) {
      if (e.selected != e.alreadyGoing)
        reqBody.withEvents.push(e);   
    });
    if (verbose) {
      console.log("Joining/Leaving Groups with POST Request Body: ")
      console.log(reqBody);
    }

    $http({
      method: "POST",
      data: reqBody,
      url: this.url + "/joinLeaveGroups",
    }).then(function successCallback(res){
      alertFactory.add("success", res.data.data);
      this.dataLoading = false;
      $routeParams = {};

    }.bind(this), function errorCallback(err){
      alertFactory.add("danger", err.data.data);
      console.log(err);
      this.dataLoading = false;

    }.bind(this));

    this.getGroups();
    this.getEvents();
  }

  if (Object.keys($routeParams).length == 0)
    this.getGroups();
  else {
    if (verbose) {
      console.log("Got Route Params!")
      console.log($routeParams);
    }
    $timeout(function() {
      angular.element('#create-group-tab a').trigger('click');
    });
  }
});

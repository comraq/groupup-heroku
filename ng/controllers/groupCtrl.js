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
  this.scope.joinGroupMapModalButton = "Join Group For Attending Events";

  /*
  /* Restricts the max number of UserGoesEvent instances per group
   * displayed in the Join Group Tab 
   */
  this.scope.userGoesEventsPerGroupLimit = 3;

  function refreshMap(map, position) {
    console.log("refreshing map");
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
    console.log("joinGroupsShowMap, groupId: " + groupId);
    this.joinGroupId = groupId;
    var modalInstance = $uibModal.open({
      animation: true,
      templateUrl: "ng/views/groupMap.html",
      scope: this.scope,
      size: "lg"
    });

    if (!this.joinGroupsMap) {
      NgMap.getMap({ id: mapId }).then(function(map) {
        console.log("inside getMap");
        this.joinGroupsMap = map;
        prepareMap.call(this);
      }.bind(this));
      console.log("before joinGroupsShowMap return");
      return;
    }
    prepareMap.call(this);
  }

  function prepareMap() {
    this.newGroup = {
      addUserToEvents: true, 
      withEvents: []
    };

    var map, timeoutDuration;
    if (this.joinTab) {
      map = this.joinGroupsMap;
      timeoutDuration = 5000;
    } else {
      map = this.createGroupsMap;
      timeoutDuration = 1000;
    }

    $window.navigator.geolocation.getCurrentPosition(function(position) {
      var userPosition = {
                           lat: position.coords.latitude,
                           lon: position.coords.longitude
                         };
      refreshMap(map, userPosition);
      $timeout(function() {
        refreshMap(map, userPosition);
      }, timeDuration);
    });
    this.getEvents();
  }
  
  this.initCreateTab = function initCreateTab(mapId) {
    if (!this.joinTab)
      return;

    this.joinTab = false;
    
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
    if (this.joinTab)
      refreshMap(this.joinGroupsMap, eventPosition)
    else
      refreshMap(this.createGroupsMap, eventPosition);
  }

  this.createGroup = function createGroup() {
    this.dataLoading = true;
    var data = this.newGroup
    $http({
      method: "POST",
      data: data,
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
      if (verbose) {
        console.log("getGroups res: " + JSON.stringify(res));
        console.log(JSON.parse(res.data));
      }
      this.scope.groups = JSON.parse(res.data);
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
      if (verbose) {
        console.log("getEvents res: " + JSON.stringify(res));
        console.log(JSON.parse(res.data));
      }
      this.scope.events = JSON.parse(res.data).map(function (e, i, arr) {
        if ($routeParams.eventName == e.eventName
            && $routeParams.lat == e.lat
            && $routeParams.lon == e.lon
            && $routeParams.timeStart == e.timeStart
            && $routeParams.timeEnd == e.timeEnd) {
          // Route Params Matches an Event!
          // User already added to UserGoesEvent Prior to Route
          e.selected = true;
          this.newGroup.addUserToEvents = false;
        } else
          e.selected = false;

        e.lat = parseFloat(e.lat);
        e.lon = parseFloat(e.lon);
        return e;
      }.bind(this));
      console.log(this.scope.events);
    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });

  };

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

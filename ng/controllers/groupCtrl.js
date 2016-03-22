var app = angular.module('groupUpApp')
                 .controller('GroupCtrl', function($scope,
                                                   $location,
                                                   $http,
                                                   $window,
                                                   $timeout,
                                                   NgMap) {
  var verbose = false;

  this.url = "controller/groupController";
  this.scope = $scope;
  this.location = $location;
  this.joinTab = true;

  function refreshMap(map, position) {
    var bounds = new google.maps.LatLngBounds();
    if (position) {
      var up = new google.maps.LatLng(position.lat, position.lon);
      bounds.extend(up);
    }
    google.maps.event.trigger(map, "resize");
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
  }
  
  this.initCreateTab = function initCreateTab(mapId) {
    if (!this.joinTab)
      return;

    this.joinTab = false;
    if (!this.map)
      this.map = NgMap.initMap(mapId);
 
    this.getEvents();
    this.newGroup = {
      withEvents: []
    };

    var ctrl = this;
    $window.navigator.geolocation.getCurrentPosition(function(position) {
      var userPosition = {
                           lat: position.coords.latitude,
                           lon: position.coords.longitude
                         };
      refreshMap(ctrl.map, userPosition);
      $timeout(function() {
        refreshMap(ctrl.map, userPosition);
      }, 1000);
    });
  };

  this.eventsChanged = function eventsChanged(e) {
    var eventPosition = {
                         lat: e.lat,
                         lon: e.lon
                       };
    refreshMap(this.map, eventPosition);
  }

  this.createGroup = function createGroup() {
    this.dataloading = true;
    var data = {
      createGroup: {
        name: this.name,
        description: this.description
      }
    };
    $http({
      method: "POST",
      data: $.param(data),
      url: this.url,
      headers: {"Content-Type": "application/x-www-form-urlencoded"}
    }).then(function successCallback(res){
      alert(res.data);
      if (response.data == true)
        console.log("Group Creation Successful!");
      else
        console.log("Could Not Create Group!");

      this.dataLoading = false;
    }.bind(this), function errorCallback(res){
      this.dataLoading = false;
      alert(res.data);
    });
  };

  this.getGroups = function getGroups() {
    $http({
      method: "GET",
      url: this.url + "/queryGroups",
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getGroups res: " + JSON.stringify(res));
        console.log(JSON.parse(res.data));
      }
      this.scope.groups = JSON.parse(res.data);
    }.bind(this), function errorCallback(err) {
      console.log(err);
    });
  };

  this.joinTabActive = function() { this.joinTab = true; }

  this.getEvents = function getEvents() {
    $http({
      method: "GET",
      url: this.url + "/queryEvents"
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getEvents res: " + JSON.stringify(res));
        console.log(JSON.parse(res.data));
      }
      this.scope.events = JSON.parse(res.data).map(function (e, i, arr) {
        e.selected = false;
        e.lat = parseFloat(e.lat);
        e.lon = parseFloat(e.lon);
        return e;
      });;
    }.bind(this), function errorCallback(err) {
      console.log(err);
    });

  };

  this.getGroups();

  this.testSubmit = function testSubmit() {
    console.log("submitting form!");
    console.log(this);
  };
});

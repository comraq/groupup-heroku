var app = angular.module('groupUpApp')
                 .controller('GroupCtrl', function($scope,
                                                   $location,
                                                   $http,
                                                   $window,
                                                   NgMap) {
  var verbose = false;

  this.url = "controller/groupController";
  this.scope = $scope;
  this.location = $location;

  this.name;
  this.description;


  this.scope.positions = [];
  var userPosition;
  function getLocation(map) {
    $window.navigator.geolocation.getCurrentPosition(function(position) {
      userPosition = {
                       lat: position.coords.latitude,
                       lng: position.coords.longitude
                     };
      drawUserPostion(new google.maps.LatLngBounds(),
                      map, userPosition);
    });
  }

  function drawUserPostion(bounds, map, userPosition) {
    if (userPosition) {
      $scope.positions = [];
      $scope.positions.push(userPosition);
      var up = new google.maps.LatLng(userPosition.lat, userPosition.lng);
      bounds.extend(up);
    }
    google.maps.event.trigger(map, "resize");
    map.setCenter(bounds.getCenter());
    map.fitBounds(bounds);
  }
  
  this.initCreateTab = function initCreateTab(mapId) {
    if (!this.map)
      this.map = NgMap.initMap(mapId);
 
    this.getEvents();
    getLocation(this.map);
  };

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

  this.getEvents = function getEvents() {
    $http({
      method: "GET",
      url: this.url + "/queryEvents"
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getEvents res: " + JSON.stringify(res));
        console.log(JSON.parse(res.data));
      }
      this.scope.events = JSON.parse(res.data);
    }.bind(this), function errorCallback(err) {
      console.log(err);
    });

  };

  this.getGroups();
});

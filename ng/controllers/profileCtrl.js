var app = angular.module('groupUpApp')
                 .controller('ProfileCtrl', function($scope,
                                                     $location,
                                                     $http,
                                                     $window,
                                                     $timeout,
                                                     NgMap, 
                                                     $routeParams,
                                                     modalService,
                                                     alertFactory) {
  var verbose = false;

  this.url = "controller/profileController";
  this.scope = $scope;
  this.location = $location;

  this.getUsersAndEvents = function getUsersAndEvents() {
    $http({
      method: "GET",
      url: this.url + "/getUsersAndEvents",
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getUsersAndEvents res: ");
        console.log(res);
      }

      var data = JSON.parse(res.data);
      this.scope.users = data.users;
      this.scope.events = data.events;
      if (verbose)
        console.log(data);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  };


  function getEventsByType() {
    $http({
      method: "GET",
      url: this.url + "/getEventsByType"
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getEventsByType res: ");
        console.log(res);
      }

      var data = JSON.parse(res.data);
      this.scope.types = data.avgByType;
      this.maxAvgTypeEvents = data.maxAvg;
      this.minAvgTypeEvents = data.minAvg;
      if (true)
        console.log(data);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  }

  this.showMinMax = function showMinMax() {
    modalService.openModal(this, "ng/views/eventByTypeHighlights.html");
    this.highlightsToggleChanged(true);
  };

  this.viewChanged = function viewChanged() {
    if (this.scope.typeView && !this.scope.types)
      getEventsByType.call(this);
  };

  this.highlightsToggleChanged = function highlightsToggleChanged(minView) {
    if (minView) {
      this.scope.highlightsAvg = this.minAvgTypeEvents;
      this.scope.highlightsModalName = "Minimum";
    } else {
      this.scope.highlightsAvg = this.maxAvgTypeEvents;
      this.scope.highlightsModalName = "Maximum";
    }
    console.log(this.scope);
  }

  this.getUsersAndEvents();
});

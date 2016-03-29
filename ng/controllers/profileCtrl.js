var app = angular.module('groupUpApp')
                 .controller('ProfileCtrl', function($scope,
                                                     $location,
                                                     $http,
                                                     $window,
                                                     $timeout,
                                                     NgMap, 
                                                     $routeParams,
                                                     $uibModal,
                                                     alertFactory) {
  var verbose = true;

  this.url = "controller/profileController";
  this.scope = $scope;
  this.location = $location;

  this.getEvents = function getEvents() {
    $http({
      method: "GET",
      url: this.url + "/getEvents",
    }).then(function successCallback(res) {
      if (verbose)
        console.log("getEvents res: " + JSON.stringify(res));

      this.scope.events = JSON.parse(res.data);
      if (verbose)
        console.log(this.scope.events);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  };

  this.getEvents();
});

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
      url: this.url + "/getUsersAndEvents",
    }).then(function successCallback(res) {
      if (verbose)
        console.log("getUsersAndEvents res: " + JSON.stringify(res));

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

  this.getEvents();
});

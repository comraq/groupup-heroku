var app = angular.module('groupUpApp')
                 .controller('GroupCtrl',
                 function($scope, $location, $http){
  //this.url = 'controller/groupController.php';
  this.url = 'controller/groupController';
  this.scope = $scope;
  this.location = $location;

  this.name;
  this.description;

  this.createGroup = function createGroup() {
    this.dataloading = true;
    var data = {
      createGroup: {
        name: this.name,
        description: this.description
      }
    };
    $http({
      method: 'POST',
      data: $.param(data),
      url: this.url,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
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
      headers: {"Content-Type": "application/x-www-form-urlencoded" }
    }).then(function successCallback(res) {
      console.log("got res: " + JSON.stringify(res));
      this.scope.groups = res.data;
    }.bind(this), function errorCallback(err) {
      console.log(err);
    });
  };

  this.getGroups();
});

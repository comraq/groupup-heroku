var app = angular.module('groupUpApp').controller('SignOutCtrl', function($scope, $location, $http){
	this.url;
	this.scope = $scope;

	this.signout = function(){
		this.url = "/controller/login/doLogout"
		

		$http({
			method: 'GET',
			url: this.url,
		}).then(function successCallback(response){
			$location.path('/');
			
		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			console.log(response.data);

		}.bind(this));
	};

});


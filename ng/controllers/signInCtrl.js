var app = angular.module('groupUpApp').controller('SignInCtrl', function($scope, $location, $http){
	this.url;
	this.scope = $scope;

	this.email;
	this.password;

	this.signin = function(){
		this.url = "/controller/database/account"
		
		var data = { email: this.email, password: this.password, }
		
		// alert(this.email);
		// alert(this.password);
		

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			$location.path('/');
			
		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			console.log(response.data);

		}.bind(this));
	};

});


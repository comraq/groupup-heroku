angular.module('groupUpApp').controller('SignInCtrl',
	function($location, SessionService, alertFactory){
		this.signIn = function () {
			SessionService.login(this.email, this.password, this.accountType)
			.then(
				function successCallback(response){
					alertFactory.add('success', 'Login Successful');
					$location.path('/Event');
				}.bind(this),
				function errorCallback(response){
					var message = response.data;
					alertFactory.add('danger', message);
				}.bind(this));
		};

	});


angular.module('groupUpApp').controller('SignOutCtrl',
	function($location, SessionService, alertFactory){
		this.signOut = function () {
			SessionService.logout()
			.then(
				function successCallback(response){
					alertFactory.add('success', 'Logout Successful');
					$location.path('/');
				}.bind(this),
				function errorCallback(response){
					var message = response.data;
					alertFactory.add('danger', message);
				}.bind(this));
		};
	});


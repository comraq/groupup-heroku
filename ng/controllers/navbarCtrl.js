angular.module('groupUpApp').controller('navbarCtrl',
	function($location, SessionService, alertFactory){
		this.isLoggedIn = function () {
			return SessionService.isLoggedIn();
		};
		this.accountType = function () {
			return SessionService.sessionInfo["accountType"];
		}
	});


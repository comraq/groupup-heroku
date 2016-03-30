angular.module('groupUpApp').service("SessionService", function($http) { 
	return {
		sessionInfo: null,
		isLoggedIn: function () {
			return this.sessionInfo != null;
		},
		getSessionInfo: function() {
			return $http.get('controller/login/getSessionInfo')
				.then(
					function successCallback(response){
						console.log(response.data);
						this.sessionInfo = response.data;
					}.bind(this));
		},
		login: function(email, password) {
			return $http({
					method: 'POST',
					url: 'controller/login/login',
					data: {
						email: email,
						password: password
					}
				})
				.then(
					function successCallback(response){
						this.sessionInfo = response.data;
					}.bind(this));
		},
		logout: function() {
			return $http.get('/controller/login/doLogout')
				.then(
					function successCallback(response){
						this.sessionInfo = null;
					}.bind(this));
		}

	};
});

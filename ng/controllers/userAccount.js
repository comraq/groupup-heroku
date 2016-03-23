var app = angular.module('groupUpApp').controller('UserAccountCtrl', function($scope, $http, $location, alertFactory){
	this.url;
	this.scope = $scope;
	this.dataLoading;

	this.email;
	this.oldPassword;
	this.newPassword;
	this.rePassword;
	
	this.firstName;
	this.lastName;
	this.phone;
	this.age;

	this.updateProfile = function(){
		
	};

	this.updatePassword = function(){
		
	}

	// this.registerAdmin = function(){
	// 	this.url = "/controller/authentication/admin"
	// 	this.dataLoading = true;
	// 	if(!validatePassword(this.aPassword, this.aRePassword)){
	// 		this.dataLoading = false;
	// 		return;
	// 	}

	// 	var data = {
	// 				email: this.aEmail,
	// 				password: this.aPassword,
	// 				rePassword: this.aRePassword,
	// 				firstName: this.aFirstName,
	// 				lastName: this.aLastName,
	// 				phone: this.aPhone
	// 			}

	// 	$http({
	// 		method: 'POST',
	// 		data: data,
	// 		url: this.url,
	// 	}).then(function successCallback(response){
	// 		alertFactory.add('success', 'Registration Successful');
	// 		this.dataLoading = false;
	// 		$location.path('/');

	// 	}.bind(this), function errorCallback(response){
	// 		var message = response.data.data;
	// 		alertFactory.add('danger', message);
	// 		this.dataLoading = false;

	// 	}.bind(this));
	// }

	// function validatePassword(password, rePassword){
	// 	var result = (password == rePassword);
	// 	if(!result){
	// 		var message = "Password & Re-password do not match";
	// 		alertFactory.add('danger', message);
	// 	}
	// 	return result;
	// }

})
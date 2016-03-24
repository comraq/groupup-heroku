var app = angular.module('groupUpApp').controller('UserAccountCtrl', function($scope, $http, $location, alertFactory, email){
	this.url;
	this.scope = $scope;
	this.dataLoading;

	this.email = email;

	// for updating password
	this.oldPassword;
	this.newPassword;
	this.rePassword;
	
	// for updating profile
	this.firstName;
	this.lastName;
	this.phone;
	this.age;
	this.invitations;



	this.getInvitation = function(){
		this.url = "/controller/account/user";

		var data = {
					invitation: {
						email: this.email,
					}
				};

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			console.log(response.data);
			this.invitations = response.data;

		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			alertFactory.add('danger', message);
			this.dataLoading = false;

		}.bind(this));
	};
	this.getInvitation();




	this.updateProfile = function(){
		this.url = "/controller/account/user";
		this.dataLoading = true;
		if(!validatePassword(this.aPassword, this.aRePassword)){
			this.dataLoading = false;
			return;
		}

		var data = {
					profile: {
						email: this.email,
						firstName: this.firstName,
						lastName: this.lastName,
						phone: this.phone,
						age: this.age
					}
				};

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			alertFactory.add('success', 'Update Successful');
			this.dataLoading = false;
			$location.path('/Account');

		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			alertFactory.add('danger', message);
			this.dataLoading = false;

		}.bind(this));
	};

	this.updatePassword = function(){
		this.url = "/controller/account/user";
		this.dataLoading = true;
		if(!validatePassword(this.newPassword, this.rePassword)){
			this.dataLoading = false;
			return;
		}

		var data = {
					password: {
						email: this.aEmail,
						oldPassword: this.oldPassword,
						newPassword: this.newPassword,
						rePassword: this.rePassword
					}
				};

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			alertFactory.add('success', 'Update Successful');
			this.dataLoading = false;
			$location.path('/Account');

		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			alertFactory.add('danger', message);
			this.dataLoading = false;

		}.bind(this));
	}

	function validatePassword(password, rePassword){
		var result = (password == rePassword);
		if(!result){
			var message = "Password & Re-password do not match";
			alertFactory.add('danger', message);
		}
		return result;
	}

})
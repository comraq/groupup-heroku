var app = angular.module('groupUpApp').controller('RegisterCtrl', function($scope, $http, $location, alertFactory){
	this.url;
	this.scope = $scope;
	this.dataLoading;

	this.email;
	this.password;
	this.rePassword;
	this.firstName;
	this.lastName;
	this.phone;
	this.age;

	this.eEmail;
	this.ePassword;
	this.eRePassword;
	this.eFirstName;
	this.eLastName;
	this.ePhone;

	this.aEmail;
	this.aPassword;
	this.aRePassword;
	this.aFirstName;
	this.aLastName;
	this.aPhone;

	this.registerUser = function(){
		this.url = "/controller/authentication/user"
		this.dataLoading = true;
		if (!validatePassword(this.password, this.rePassword)){
			this.dataLoading = false;
			return;
		}
		
		var data = {
					email: this.email,
					password: this.password,
					rePassword: this.rePassword,
					firstName: this.firstName,
					lastName: this.lastName,
					phone: this.phone,
					age: this.age
				}

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			alertFactory.add('success', 'Registration Successful');
			this.dataLoading = false;
			$location.path('/');
			
		}.bind(this), function errorCallback(response){
			var message = response.data;
			alertFactory.add('danger', message);
			this.dataLoading = false;

		}.bind(this));
	};

	this.registerEProvider = function(){
		this.url = "/controller/authentication/eventProvider"
		this.dataLoading = true;
		if(!validatePassword(this.ePassword, this.eRePassword)){
			this.dataLoading = false;
			return;
		}
		
		var data = {
					email: this.eEmail,
					password: this.ePassword,
					rePassword: this.eRePassword,
					firstName: this.eFirstName,
					lastName: this.eLastName,
					phone: this.ePhone
				}

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			alertFactory.add('success', 'Registration Successful');
			this.dataLoading = false;
			$location.path('/');

		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			alertFactory.add('danger', message);
			this.dataLoading = false;

		}.bind(this));
	}

	this.registerAdmin = function(){
		this.url = "/controller/authentication/admin"
		this.dataLoading = true;
		if(!validatePassword(this.aPassword, this.aRePassword)){
			this.dataLoading = false;
			return;
		}

		var data = {
					email: this.aEmail,
					password: this.aPassword,
					rePassword: this.aRePassword,
					firstName: this.aFirstName,
					lastName: this.aLastName,
					phone: this.aPhone
				}

		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			alertFactory.add('success', 'Registration Successful');
			this.dataLoading = false;
			$location.path('/');

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

});
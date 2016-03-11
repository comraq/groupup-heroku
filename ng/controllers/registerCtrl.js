var app = angular.module('groupUpApp').controller('RegisterCtrl', function($scope, $http){
	this.url = "../../controller/authentication.php"
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
		this.dataLoading = true;
		if (!validatePassword(this.password, this.rePassword)){
			console.log("passowrd do not match");
			this.dataLoading = false;
			return;
		};
		var data = {
			registerUser: 
				{
					email: this.email,
					password: this.password,
					rePassword: this.rePassword,
					firstName: this.firstName,
					lastName: this.lastName,
					phone: this.phone,
					age: this.age
				}
		}

		$http({
			method: 'POST',
			data: $.param(data),
			url: this.url,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			alert(response.data);
			if (response.data == true){
				console.log("registration successful");
			}else{
				console.log("registration unsuccessful");
			}
			this.dataLoading = false;
			
		}.bind(this), function errorCallback(response){
			this.dataLoading = false;
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			alert(response.data);
		});
	};

	this.registerEProvider = function(){
		this.dataLoading = true;
		if (!validatePassword(this.ePassword, this.eRePassword)){
			console.log("passowrd do not match");
			this.dataLoading = false;
			return;
		};
		var data = {
			registerEProvider: 
				{
					email: this.eEmail,
					password: this.ePassword,
					rePassword: this.eRePassword,
					firstName: this.eFirstName,
					lastName: this.eLastName,
					phone: this.ePhone
				}
		}

		$http({
			method: 'POST',
			data: data,
			url: this.url,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			alert(response.data);
			if (response.data == true){
				console.log("registration successful");
			}else{
				console.log("registration unsuccessful");
			}
			this.dataLoading = false;
		}.bind(this), function errorCallback(response){
			this.dataLoading = false;
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			    alert(response.data);
		});
	}

	this.registerAdmin = function(){
		this.dataLoading = true;
		if (!validatePassword(this.aPassword, this.aRePassword)){
			console.log("passowrd do not match");
			this.dataLoading = false;
			return;
		};
		var data = {
			registerAdmin: 
				{
					email: this.aEmail,
					password: this.aPassword,
					rePassword: this.aRePassword,
					firstName: this.aFirstName,
					lastName: this.aLastName,
					phone: this.aPhone
				}
		}

		$http({
			method: 'POST',
			data: data,
			url: this.url,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			alert(response.data);
			if (response.data == true){
				console.log("registration successful");
			}else{
				console.log("registration unsuccessful");
			}
			this.dataLoading = false;
		}.bind(this), function errorCallback(response){
			this.dataLoading = false;
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			    alert(response.data);
		});
	}

	function validatePassword(password, rePassword){
		return password == rePassword;
	}

});
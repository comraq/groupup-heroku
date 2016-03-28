var app = angular.module('groupUpApp').controller('UserAccountCtrl', function($scope, $http, $location, alertFactory, email){
	this.addUserUrl = "/controller/userGoesEvent/startUserGoesEvent";
    this.cancelURL = "/controller/userGoesEvent/startCancelUserGoesEvent";
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
	this.invitations = [];
	this.busy = false;
	this.page = 0;


	function loadInvitations(invJson){
		var invs = [];
		for(var i = 0; i < invJson.length; i++){
		    var invitation = {
				email: invJson[i]["email"],
				eventName: invJson[i]["eventName"],
				lat: invJson[i]["lat"],
				lon: invJson[i]["lon"],
				timeStart: invJson[i]["timeStart"],
				timeEnd: invJson[i]["timeEnd"],
				cost: invJson[i]["cost"],
				description: invJson[i]["description"],
				going: invJson[i]["going"],
				message: invJson[i]["message"],
				eventType: invJson[i]["category"]
			};
		    invs.push(invitation);
		}
		return invs;
	}

	this.getInvitation = function(){
		this.url = "/controller/account/user";

		var data = {
					invitation: {
						email: this.email,
						page: this.page
					}
				};
		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			this.endOfResult = (!response.data || response.data.length == 0);
			this.invitations.push.apply(this.invitations, loadInvitations(response.data));
			this.busy = false;
			this.page++;

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


	
	this.signUpForEvent = function signUpForEvent(invitation) {
    invitation.going = 1;
    var data = {
        email: this.email,
        eventName : invitation.eventName,
        lat: invitation.lat,
        lon: invitation.lon,
        timeStart: invitation.timeStart,
        timeEnd: invitation.timeEnd
    }

    $http({
        method: 'POST',
        data: data,
        url: this.addUserUrl
	    }).then(function successCallback(response) {

			console.log(response.data);
	        if (JSON.parse(response.data)) {
	            alertFactory.add('success', "Added user to event");
	        } else {
	            console.log("Unable to add user to event");
	        }
	    }.bind(this), function errorCallback(response){
				var message = response.data;
				alertFactory.add('danger', message);
				this.dataLoading = false;

		}.bind(this));
	}
	
	this.cancelSignup = function cancelSignup(invitation){
    invitation.going=0;
    var data = {
        email: this.email,
        eventName : invitation.eventName,
        lat: invitation.lat,
        lon: invitation.lon,
        timeStart: invitation.timeStart,
        timeEnd: invitation.timeEnd
    }

    $http({
        method: 'POST',
        data: data,
        url: this.cancelURL
    	}).then(function successCallback(response) {
	    	console.log(response.data);
	        if (JSON.parse(response.data)) {
	        	alertFactory.add('success', "Removed user from event");
	        } else {
	            console.log("Unable to removed user to event");
	        }
    	}.bind(this), function errorCallback(response){
			var message = response.data;
			alertFactory.add('danger', message);
			this.dataLoading = false;

		}.bind(this));
	}

})
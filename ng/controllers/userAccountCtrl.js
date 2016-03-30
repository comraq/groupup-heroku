var app = angular.module('groupUpApp').controller('UserAccountCtrl', function($scope, $http, $location, alertFactory, SessionService){
	SessionService.getSessionInfo();
	this.addUserUrl = "/controller/userGoesEvent/startUserGoesEvent";
    this.cancelUrl = "/controller/userGoesEvent/startCancelUserGoesEvent";
    this.getProfileUrl = "/controller/account/user";
	this.url;
	this.scope = $scope;
	this.dataLoading;
	this.loadingAttend;
	this.email = SessionService.sessionInfo["email"];

	// for updating password
	this.oldPassword;
	this.newPassword;
	this.rePassword;
	
	// for updating profile
	this.firstName;
	this.lastName;
	this.phone;
	this.age;
	
	// invitation
	this.invEndOfResult = false;
	this.invitations = [];
	this.invPage = 0;

	// events to go to
	this.eveEndOfResult = false;
	this.evePage = 0;
	this.events = [];

	this.checkAge = function checkAge(){
		if (this.age >= 0){
			return false;
		}
		return true;
	}
	
	this.getProfile = function getProfile(){
		var data = {
			getProfile: {
						email: this.email
					}
		};

		$http({
	        method: 'POST',
	        data: data,
	        url: this.getProfileUrl
	    	}).then(function successCallback(response) {
	    		if (response.data){
	    			var data = response.data.data[0];
			    	this.firstName = data["firstName"];
					this.lastName = data["lastName"];
					this.phone = data["phone"];
					this.age = data["age"];
					
				}
	    	}.bind(this), function errorCallback(response){
	    		this.loadingAttend = false; 
				var message = response.data;
				alertFactory.add('danger', message);

			}.bind(this));
	}
	this.getProfile();

	function loadEvents(eveJson){

		var events = [];
		for(var i = 0; i < eveJson.length; i++){
		    var event = {
				email: eveJson[i]["email"],
				eventName: eveJson[i]["eventName"],
				lat: eveJson[i]["lat"],
				lon: eveJson[i]["lon"],
				timeStart: eveJson[i]["timeStart"],
				timeEnd: eveJson[i]["timeEnd"],
				cost: (eveJson[i]["cost"])? eveJson[i]["cost"] : "Free",
				message: eveJson[i]["message"],
				eventType: (eveJson[i]["category"])? eveJson[i]["category"] : "None",
				groupName: (eveJson[i]["groupNames"])? eveJson[i]["groupNames"] : "No Group",
			};
			
			events.push(event);
		}
		return events;
	}

	this.getEvents = function(){
		this.url = "/controller/account/user";
		var data = {
					event: {
						email: this.email,
						page: this.evePage
					}
				};
		this.evePage++;
		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			
			this.eveEndOfResult = (!response.data || response.data.length == 0);
			this.events.push.apply(this.events, loadEvents(response.data));
			
		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			alertFactory.add('danger', message);

		}.bind(this));
	};
	this.getEvents();


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

	this.getInvitations = function(){
		this.url = "/controller/account/user";
		var data = {
					invitation: {
						email: this.email,
						page: this.invPage
					}
				};
		this.invPage++;
		$http({
			method: 'POST',
			data: data,
			url: this.url,
		}).then(function successCallback(response){
			this.invEndOfResult = (!response.data || response.data.length == 0);
			this.invitations.push.apply(this.invitations, loadInvitations(response.data));
			
		}.bind(this), function errorCallback(response){
			var message = response.data.data;
			alertFactory.add('danger', message);

		}.bind(this));
	};
	this.getInvitations();

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
						email: this.email,
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
		this.loadingAttend = true; 
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
		        if (JSON.parse(response.data)) {
		        	invitation.going = 1;
		            alertFactory.add('success', "Added user to event");
		            this.evePage = 0;
					this.events = [];
					this.getEvents();
		        } else {
		            console.log("Unable to add user to event");
		        }
		        this.loadingAttend = false; 
		    }.bind(this), function errorCallback(response){
		    	this.loadingAttend = false; 
				var message = response.data;
				alertFactory.add('danger', message);
				this.dataLoading = false;

			}.bind(this));
	}
	
	this.cancelSignup = function cancelSignup(invitation){
    	this.loadingAttend = true; 
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
	        url: this.cancelUrl
	    	}).then(function successCallback(response) {
		    	this.loadingAttend = false; 
		        if (JSON.parse(response.data)) {
		        	invitation.going = 0;
		        	alertFactory.add('success', "Removed user from event");
		        	this.evePage = 0;
					this.events = [];
					this.getEvents();
		        } else {
		            console.log("Unable to removed user to event");
		        }
	    	}.bind(this), function errorCallback(response){
	    		this.loadingAttend = false; 
				var message = response.data;
				alertFactory.add('danger', message);

			}.bind(this));
		}

});
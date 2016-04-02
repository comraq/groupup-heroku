var app = angular.module('groupUpApp')
                 .controller('UserAccountCtrl', function($scope,
                                                         $http,
                                                         $location,
                                                         $timeout,
                                                         alertFactory,
                                                         modalService,
                                                         sessionInfo) {
  this.addUserUrl = "/controller/userGoesEvent/startUserGoesEvent";
  this.cancelUrl = "/controller/userGoesEvent/startCancelUserGoesEvent";
  this.profileUrl;
  this.url;
  this.scope = $scope;
  this.dataLoading;
  this.loadingAttend;
  this.email = sessionInfo["email"];
  this.accType = sessionInfo["accountType"];

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

	if(this.accType == 0){
		this.profileUrl = "/controller/account/user";
		this.updatePasswordUrl = "/controller/account/user";
	}else if(this.accType == 1){
		this.profileUrl = "/controller/account/eventProvider";
		this.updatePasswordUrl = "/controller/account/EventProvider";
	}else if(this.accType == 2){
		this.profileUrl = "/controller/account/admin";
		this.updatePasswordUrl = "/controller/account/admin";
	}


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
	        url: this.profileUrl
	    	}).then(function successCallback(response) {
	    		if (response.data){
	    			var data = response.data.data[0];
			    	this.firstName = data["firstName"];
					this.lastName = data["lastName"];
					this.phone = data["phone"];
					if(data["age"]){
						this.age = data["age"];
					}
				}
	    	}.bind(this), function errorCallback(response){
	    		this.loadingAttend = false; 
				var message = response.data;
				alertFactory.add('danger', message);

			}.bind(this));
	console.log(this.profileUrl);
	}


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

	this.updateProfile = function(){
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
			url: this.profileUrl,
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
			url: this.updatePasswordUrl,
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

  // Event Provider Profile Controller
  var verbose = false;

  this.providerUrl = "controller/profileController";

  this.getUsersAndEvents = function getUsersAndEvents() {
    $http({
      method: "GET",
      url: this.providerUrl + "/getUsersAndEvents?email="
           + this.email
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getUsersAndEvents res: ");
        console.log(res);
      }

      var data = JSON.parse(res.data);
      this.scope.users = data.users;
      this.scope.events = data.events;
      if (verbose)
        console.log(data);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  };

  function getEventsByType() {
    $http({
      method: "GET",
      url: this.providerUrl + "/getEventsByType?email="
           + this.email
    }).then(function successCallback(res) {
      if (verbose) {
        console.log("getEventsByType res: ");
        console.log(res);
      }

      var data = JSON.parse(res.data);
      this.scope.types = data.avgByType;
      this.maxAvgTypeEvents = data.maxAvg;
      this.minAvgTypeEvents = data.minAvg;
      if (verbose)
        console.log(data);

    }.bind(this), function errorCallback(err) {
      alertFactory.add("danger", err.data.data);
      console.log(err);
    });
  }

  this.showMinMax = function showMinMax() {
    modalService.openModal(this,
                           "ng/views/eventByTypeHighlights.html",
                           verbose);
    this.highlightsToggleChanged(true);
  };

  this.viewChanged = function viewChanged() {
    if (this.scope.typeView && !this.scope.types)
      getEventsByType.call(this);
  };

  this.highlightsToggleChanged = function highlightsToggleChanged(minView) {
    if (minView) {
      this.scope.highlightsAvg = this.minAvgTypeEvents;
      this.scope.highlightsModalName = "Minimum";
    } else {
      this.scope.highlightsAvg = this.maxAvgTypeEvents;
      this.scope.highlightsModalName = "Maximum";
    }
  }

  this.getUsersAndEvents();

  // Initialization Methods for Account Partial View
  if (this.accType == 0) {
    // If User is Currently Logged in
    this.getEvents();
    this.getInvitations();
  } else if (this.accType == 1) {
    $timeout(function() {
      angular.element('#provider-profile-tab a').trigger('click');
    });
  } else if (this.accType == 2) {
    $timeout(function() {
      angular.element('#account-profile-tab a').trigger('click');
    });
  }

  // Applicable to All Account Types
  this.getProfile();
});

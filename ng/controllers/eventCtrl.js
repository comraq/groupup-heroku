var app = angular.module('groupUpApp').controller('EventCtrl', function($scope, $window, $location, $http, NgMap){
	$scope.positions = [];
	$scope.results;
	this.searchUrl = "/controller/search/searchEvents";
	this.typeUrl = "/controller/eventTypes/getTypes";
	this.createEventUrl = "/controller/createEvent/createEvent";
	
	this.searchTarget;
	this.bounds;

	this.eventName;
	this.eventDescription;
	this.eventCost;
	this.timeStart;
	this.timeEnd;
	this.eventType;

	var userPosition;
	var newEventLat; 
	var newEventLng;
	var createTab = false;

	this.searchEvents = function searchEvents(){
		var data = {
			searchEvents: 
			{
				searchTarget: this.searchTarget,
				lat: $scope.lat,
				lon: $scope.lon
			}
		}

		$http({
			method: 'POST',
			data: $.param(data),
			url: this.searchUrl,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			$scope.events = response.data;
			//https://ngmap.github.io/#/!map_fit_bounds.html
			if($scope.events.length > 0){
				$scope.results = true;
				var bounds = new google.maps.LatLngBounds();

				$scope.positions = [];
				$scope.events.forEach(function(event){
					var position = {lat: event.lat, lng:event.lon};
					var ps = new google.maps.LatLng(event.lat, event.lon);
					$scope.positions.push(position);
					bounds.extend(ps);
				});
				
				NgMap.getMap().then(function(map) {
					drawUserPostion(bounds, map, userPosition);
				});
			}

		}.bind(this), function errorCallback(response){
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			    alert(response.data);
			});
	};
	
	var getLocation = function getLocation() {
		$window.navigator.geolocation.getCurrentPosition(function(position){
			lat = position.coords.latitude;
			lon = position.coords.longitude;
			userPosition = {lat: lat, lng:lon};
			
			NgMap.getMap().then(function(map) {
				var bounds = new google.maps.LatLngBounds();
				drawUserPostion(bounds, map, userPosition);
			});
		});
	}

	var drawUserPostion = function drawUserPostion(bounds, map, userPosition){
		if(userPosition){
			$scope.positions.push(userPosition);
			var up = new google.maps.LatLng(userPosition.lat, userPosition.lng);
			bounds.extend(up);
			newEventLat = userPosition.lat;
			newEventLng = userPosition.lng;
		}
		map.setCenter(bounds.getCenter());
		map.fitBounds(bounds);
	}

	this.getEventTypes = function getEventTypes(){
		$http({
			method: 'GET',
			url: this.typeUrl,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			$scope.eventTypes = response.data;
		});
	}

	this.clearSearchData = function clearSearchData(){
		createTab = true;
		$scope.results = null;
		$scope.positions = [];
		NgMap.getMap().then(function(map) {
			var bounds = new google.maps.LatLngBounds();
			drawUserPostion(bounds, map, userPosition);
		});
	}

	this.clearCreateData = function clearCreateData(){
		createTab = false;
		NgMap.getMap().then(function(map) {
			$scope.positions = [];
			var bounds = new google.maps.LatLngBounds();
			drawUserPostion(bounds, map, userPosition);
		});
	}

	this.mapClick = function mapClick(event){
		if(createTab){
			$scope.positions = [];
			newEventLat = event.latLng.lat();
			newEventLng = event.latLng.lng();
			$scope.positions.push({lat: newEventLat, lng: newEventLng})
		}
	}

	this.redirect = function redirect(eventName, lat, lon, createdBy, timeStart, timeEnd){
		console.log(eventName);
		console.log(createdBy);
		console.log(timeStart);
		console.log(timeEnd);
		console.log(lat);
		console.log(lon);
		$location.path('/GoesWith/' + eventName + '/' + lat + '/' + lon + '/' + timeStart +'/' + timeEnd + '/' + createdBy);
	}

	this.createEvent = function createEvent(){
		
		var data = {
			createEvent: 
			{
				eventName: this.eventName,
				eventDescription: this.eventDescription,
				eventCost: this.eventCost,
				timeStart: this.timeStart,
				timeEnd: this.timeEnd,
				eventType: this.eventType,
				lat: newEventLat,
				lng: newEventLng
			}
		}

		$http({
			method: 'POST',
			data: $.param(data),
			url: this.createEventUrl,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			if (response.data == true){
				console.log("event creation successful");
			}else{
				console.log(response.data);
				console.log("event creation unsuccessful");
			}
		});

	}

	if(!userPosition){
		getLocation();
	}
	this.getEventTypes();

});

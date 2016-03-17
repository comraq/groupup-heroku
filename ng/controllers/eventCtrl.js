var app = angular.module('groupUpApp').controller('EventCtrl', function($scope, $window, $location, $http, NgMap){
	this.searchUrl = "../../controller/search.php";
	this.typeUrl = "../../controller/eventTypes/php";
	this.searchTarget;
	this.bounds;
	$scope.positions = [];
	$scope.results;
	var userPosition;

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
			console.log(response.data);
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
		}
		map.setCenter(bounds.getCenter());
		map.fitBounds(bounds);
	}

	var getEventTypes = function getEventTypes(){
		console.log(this.typeUrl)
		$http({
			method: 'GET',
			url: "../../controller/eventTypes.php",
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			console.log("success");
			$scope.eventTypes = response.data;
			console.log(response.data);	
		});
	}
	getLocation();
	getEventTypes();

});

var app = angular.module('groupUpApp').controller('EventCtrl', function($scope, $window, $location, $http, NgMap){
	this.url = "../../controller/search.php"
	this.searchTarget;
	this.userPosition = {lat: 0, lng:0};
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
			url: this.url,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function successCallback(response){
			$scope.events = response.data;
			
			//https://ngmap.github.io/#/!map_fit_bounds.html
			var bounds = new google.maps.LatLngBounds();
			$scope.positions = [];
			$scope.positions.push(this.userPosition);
			var up = new google.maps.LatLng(this.userPosition.lat, this.userPosition.lng);
			bounds.extend(up);

			$scope.events.forEach(function(event){
				var position = {lat: event.lat, lng:event.lon};
				$scope.positions.push(position);
				
				var ps = new google.maps.LatLng(event.lat, event.lon);
				bounds.extend(ps);
			});

			NgMap.getMap().then(function(map) {
				map.setCenter(this.userPosition);
				map.fitBounds(bounds);
			});

		}.bind(this), function errorCallback(response){
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			    alert(response.data);
			});
	};
	
	var userPosition;
	this.getLocation = function getLocation() {
		$window.navigator.geolocation.getCurrentPosition(function(position){
			lat = position.coords.latitude;
			$scope.lat = lat;
			lon = position.coords.longitude;
			$scope.lon = lon;

			this.userPosition = {lat: lat, lng:lon};
		
			NgMap.getMap().then(function(map) {
				map.setCenter(this.userPosition);
			});

		});
	}

	this.getLocation();

});

var app = angular.module('groupUpApp').controller('EventCtrl', function($scope, $window, $location, $http){
	this.url = "../../controller/search.php"
	this.searchTarget;


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
			console.log(response);
			$scope.events = response;
			console.log($scope.events)
		}.bind(this), function errorCallback(response){
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			    alert(response.data);
			});
	};
	

	this.getLocation = function getLocation() {
		$window.navigator.geolocation.getCurrentPosition(function(position){
			$scope.lat = position.coords.latitude;
			$scope.lon = position.coords.longitude;
			console.log("lat: " + $scope.lat + " lon: " + $scope.lon );
		});
	}

	this.getLocation();
});

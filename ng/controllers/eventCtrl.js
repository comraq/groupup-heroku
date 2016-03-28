var app = angular.module('groupUpApp').controller('EventCtrl', function($scope, $window, $location, $http, NgMap, alertFactory) {
    $scope.positions = [];
    $scope.results;

    this.searchUrl = "/controller/search/startSearchEvents";
    this.typeUrl = "/controller/eventType/startGetTypes";
    this.createEventUrl = "/controller/createEvent/startCreateEvent";
    this.updateEventUrl = "/controller/updateEvent/startUpdateEvent";
    this.deleteEventUrl = "/controller/deleteEvent/startDeleteEvent";
    this.addUserUrl = "/controller/userGoesEvent/startUserGoesEvent";
    this.cancelURL = "/controller/userGoesEvent/startCancelUserGoesEvent";

    this.detailEvent;
    //search stuff
    this.searchName;
    this.searchNameOperator;

    this.searchTimeStart;
    this.searchTimeStartLogic;
    this.searchTimeStartOperator;

    this.searchTimeEnd;
    this.searchTimeEndLogic;
    this.searchTimeEndOperator;

    this.searchCost;
    this.searchCostLogic;
    this.searchCostOperator;

    this.searchDesctipion;
    this.searchDesctipionLogic;
    this.searchDesctipionOperator;

    this.searchEventType;
    this.searchEventTypeLogic;
    this.searchEventTypeOperator;

    this.searchCreatedBy;
    this.searchCreatedByLogic;
    this.searchCreatedByOperator;

    this.bounds;
    this.eventName;
    this.eventDescription;
    this.eventCost;
    this.timeStart;
    this.timeEnd;
    this.invitees;
    this.message;
    this.newEventType = [];
    this.private = false;
    this.editing = false;

    this.eventTypeDetail;

    var originalEvent;
    var userPosition;
    var newEventLat;
    var newEventLng;
    var createTab = false;

    this.searchEvents = function searchEvents() {
        var data = {
            searchNameOperator: this.searchNameOperator,
            searchName: this.searchName,
            searchTimeStart: this.searchTimeStart,
            searchTimeStartLogic: this.searchTimeStartLogic,
            searchTimeStartOperator: this.searchTimeStartOperator,
            searchTimeEnd: this.searchTimeEnd,
            searchTimeEndLogic: this.searchTimeEndLogic,
            searchTimeEndOperator: this.searchTimeEndOperator,
            searchCost: this.searchCost,
            searchCostLogic: this.searchCostLogic,
            searchCostOperator: this.searchCostOperator,
            searchDesctipion: this.searchDesctipion,
            searchDesctipionLogic: this.searchDesctipionLogic,
            searchDesctipionOperator: this.searchDesctipionOperator,
            searchCreatedBy: this.searchCreatedBy,
            searchCreatedByLogic: this.searchCreatedByLogic,
            searchCreatedByOperator: this.searchCreatedByOperator
        }
        $http({
            method: 'POST',
            data: data,
            url: this.searchUrl,
        }).then(function successCallback(response) {
            $scope.events = JSON.parse(response.data);

            //https://ngmap.github.io/#/!map_fit_bounds.html
            if ($scope.events.length > 0) {
                $scope.results = true;
                var bounds = new google.maps.LatLngBounds();

                $scope.positions = [];
                $scope.events.forEach(function(event) {
                    var position = {
                        lat: event.lat,
                        lng: event.lon
                    };
                    var ps = new google.maps.LatLng(event.lat, event.lon);
                    $scope.positions.push(position);
                    bounds.extend(ps);
                });

                NgMap.getMap({ id: 'event-map' }).then(function(map) {
                    drawUserPostion(bounds, map, userPosition);
                });
            } else {
                $scope.results = false;
                $scope.clearSearchData();
            }
        }, function errorCallback(response) {
            alertFactory.add('danger', response.data);
        });
    };

    this.getEventTypes = function getEventTypes() {
        $http({
            method: 'GET',
            url: this.typeUrl,
        }).then(function successCallback(response) {
            $scope.eventTypes = JSON.parse(response.data);
        }.bind(this), function errorCallback(response) {
            alertFactory.add('danger', response.data);
        });
    }



    this.signUpForEvent = function signUpForEvent(event) {
        event.going = 1;
        var data = {
            email: "testUser1@test.com",
            eventName: event.eventName,
            lat: event.lat,
            lon: event.lon,
            timeStart: event.timeStart,
            timeEnd: event.timeEnd
        }

        $http({
            method: 'POST',
            data: data,
            url: this.addUserUrl
        }).then(function successCallback(response) {
            if (JSON.parse(response.data)) {
                alertFactory.add('success', 'Added user to event');
            } else {
                alertFactory.add('danger', response.data);
            }
        });
        $location.path('/Group/' + event.eventName + '/' + event.lat + '/' + event.lon + '/' + event.timeStart + '/' + event.timeEnd + '/' + event.createdBy + '/');
    }

    this.cancelSignup = function cancelSignup(event) {
        event.going = 0;
        var data = {
            email: "testUser1@test.com",
            eventName: event.eventName,
            lat: event.lat,
            lon: event.lon,
            timeStart: event.timeStart,
            timeEnd: event.timeEnd
        }

        $http({
            method: 'POST',
            data: data,
            url: this.cancelURL
        }).then(function successCallback(response) {
            if (JSON.parse(response.data)) {
                alertFactory.add('success', 'Removed user from event');
            } else {
                alertFactory.add('danger', response.data);
            }
        });
    };

    this.createEvent = function createEvent() {
        var eventTypes = [];
        this.newEventType.forEach(function(event) {
            eventTypes.push(event.eventTypeId);
        });

        if (this.timeStart >= this.timeEnd) {
            alertFactory.add('danger', 'Time Start must be before time end');
            return;
        }

        var data = {
            eventName: this.eventName,
            eventDescription: this.eventDescription,
            eventCost: this.eventCost,
            timeStart: formatDate(this.timeStart),
            timeEnd: formatDate(this.timeEnd),
            eventType: eventTypes,
            lat: newEventLat,
            lng: newEventLng,
            invitees: this.invitees,
            message: this.message,
            privateEvent: this.private
        }

        $http({
            method: 'POST',
            data: data,
            url: this.createEventUrl
        }).then(function successCallback(response) {
            if (JSON.parse(response.data)) {
                alertFactory.add('success', 'Event creation successful');
            } else {
                alertFactory.add('danger', 'The server returned malformed data');
            }
        }, function errorCallback(response) {
            alertFactory.add('danger', response.data);
        });
    };

    this.deleteEvent = function deleteEvent(event) {
        var data = {
            eventName: event.eventName,
            timeStart: event.timeStart,
            timeEnd: event.timeEnd,
            lat: event.lat,
            lng: event.lon,
        }
        event.delete = true;
        $http({
            method: 'POST',
            data: data,
            url: this.deleteEventUrl
        }).then(function successCallback(response) {
            if (JSON.parse(response.data)) {
                alertFactory.add('success', 'Event deletion successful');
            } else {
                alertFactory.add('danger', 'The server returned malformed data');
            }
        }, function errorCallback(response) {
            alertFactory.add('danger', response.data);
        });
    };

    this.modifyEvent = function() {
        var data = {
            origEventName: originalEvent.eventName,
            origTimeStart: originalEvent.timeStart,
            origTimeEnd: originalEvent.timeEnd,
            origLat: originalEvent.lat,
            origLng: originalEvent.lon,
            eventName: this.detailEvent.eventName,
            eventDescription: this.detailEvent.description,
            eventCost: this.detailEvent.cost,
            timeStart: this.detailEvent.timeStart,
            timeEnd: this.detailEvent.timeEnd,
            lat: this.detailEvent.lat,
            lng: this.detailEvent.lon,
            invitees: this.detailEvent.invitees,
            message: this.detailEvent.message,
            privateEvent: this.detailEvent.private
        }
        $http({
            method: 'POST',
            data: data,
            url: this.updateEventUrl
        }).then(function successCallback(response) {
            if (JSON.parse(response.data)) {
                alertFactory.add('success', 'Event update successful');
            } else {
                alertFactory.add('danger', 'The server returned malformed data');
            }
        }, function errorCallback(response) {
            alertFactory.add('danger', response.data);
        });
    }

    this.saveOriginalEvent = function() {
        originalEvent = angular.copy(this.detailEvent);
    }

    this.viewDetails = function(event) {
        this.detailEvent = event;
    }
    this.viewEventTypeDetails = function(eventType) {
        this.eventTypeDetail = eventType;
    }
    var formatDate = function(date) {
        return date.getFullYear() + "-" + (date.getMonth() + 1) + "-" + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds()
    }

    $scope.clearSearchData = function clearSearchData() {
        createTab = true;
        $scope.results = null;
        $scope.positions = [];
        NgMap.getMap({ id: 'event-map' }).then(function(map) {
            var bounds = new google.maps.LatLngBounds();
            drawUserPostion(bounds, map, userPosition);
        });
    }

    this.clearCreateData = function clearCreateData() {
        createTab = false;
        NgMap.getMap({ id: 'event-map' }).then(function(map) {
            $scope.positions = [];
            var bounds = new google.maps.LatLngBounds();
            drawUserPostion(bounds, map, userPosition);
        });
    }

    this.mapClick = function mapClick(event) {
        if (createTab) {
            $scope.positions = [];
            newEventLat = event.latLng.lat();
            newEventLng = event.latLng.lng();
            $scope.positions.push({
                lat: newEventLat,
                lng: newEventLng
            })
        }
    }

    var getLocation = function getLocation() {
        $window.navigator.geolocation.getCurrentPosition(function(position) {
            lat = position.coords.latitude;
            lon = position.coords.longitude;
            userPosition = {
                lat: lat,
                lng: lon
            };
            NgMap.getMap({ id: 'event-map' }).then(function(map) {
                var bounds = new google.maps.LatLngBounds();
                drawUserPostion(bounds, map, userPosition);
            });
        });
    }

    var drawUserPostion = function drawUserPostion(bounds, map, userPosition) {
        if (userPosition) {
            $scope.positions.push(userPosition);
            var up = new google.maps.LatLng(userPosition.lat, userPosition.lng);
            bounds.extend(up);
            newEventLat = userPosition.lat;
            newEventLng = userPosition.lng;
        }
        map.setCenter(bounds.getCenter());
        map.fitBounds(bounds);
    }


    if (!userPosition) {
        getLocation();
    }
    this.getEventTypes();
});

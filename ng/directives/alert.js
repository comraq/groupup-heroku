var alertService = angular.module('alertService', []);

alertService.directive('alerts', function(alertFactory){
  var directive = {
      restrict: 'E',
      scope: true,
      template: '<div class="alert-box" ng-show="alerts.length > 0">' +
                  '<div ng-repeat="alert in alerts | filter: {show:true}" class="alert alert-{{alert.type}}">' +
                  '<button type="button" class="close" ng-click="alert.show = false" aria-label="Dismiss"><span arai-hidden="true">&times;</span></button>{{alert.message}}</div></div>',
      link: function(scope) {
        scope.alerts = alertFactory.alerts;
      }
    }
    return directive;
});

alertService.factory('alertFactory', function($timeout){
  var alerts = [];
      var factory = {
          alerts: alerts,
          add: add,
          hide: hide,
          remove: remove
      };
      
      function add(type, message) {
          var alert = {
              type: type,
              message: message,
              show: true
          };
          alerts.push(alert);

          $timeout(function () {
              hide(alert);
          }, 2500);

          $timeout(function () {
              remove(alert);
          }, 40000);
      }

      function hide(alert) {
          alert.show = false;
      }

      function remove(alert) {
          for (var i = 0; i < alerts.length; i++) {
              if (alerts[i] === alert) {
                  alerts.splice(i, 1);
                  break;
              }
          }
      }
  return factory;

});
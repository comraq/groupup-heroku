var app = angular.module('groupUpApp');

app.directive('alert', function($timeout) {
  this.alert = alertFactory.alerts;
  return {
    restrict: 'E',
    replace: true,
    scope: {
      ngModel: '='
    },
    template: '<div class="alert fade" bs-alert="ngModel"></div>',
    link: function(scope, element, attrs) {
      $timeout(function(){
        element.hide();
      }, 2000);
    }
  }
});

app.controller('alertCtrl', function($scope){    
    this.alerts = [];
    this.addAlert = function(type, message) {
      this.alerts.push(
          {
            "type": type,
            "content": message
          }
      )
    }
});

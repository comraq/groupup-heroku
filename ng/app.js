var app = angular.module('groupUpApp', [
    'ngAnimate',
    'ngRoute',
    'ngSanitize',
    'ngMap',
    'alertService',
    'modalService',
    'selectionModel',
    'anguFixedHeaderTable',
    'ui.bootstrap.datetimepicker',
    'ui.bootstrap',
    'toggle-switch',
    'infinite-scroll'
]);

app.config(function($routeProvider) {
    $routeProvider
        .when("/SignIn", {
            templateUrl: 'ng/views/signIn.html',
            controller: 'SignInCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Event", {
            templateUrl: 'ng/views/event.html',
            controller: 'EventCtrl',
            controllerAs: 'ctrl',
            resolve: {
              sessionInfo: function(SessionService) {
                return SessionService.sessionSignedIn();
              }
            }
        })
        .when("/Group/:eventName/:lat/:lon/:timeStart/:timeEnd/:createdBy", {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl',
            resolve: {
              sessionInfo: function(SessionService) {
                return SessionService.sessionSignedIn();
              }
            }
        })
        .when("/Group", {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl',
            resolve: {
              sessionInfo: function(SessionService) {
                return SessionService.sessionSignedIn();
              }
            }
        })
        .when("/SignOut", {
            templateUrl: 'ng/views/signOut.html',
            controller: 'SignOutCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Register", {
            templateUrl: 'ng/views/register.html',
            controller: 'RegisterCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Account", {
            templateUrl: 'ng/views/userAccount.html',
            controller: 'UserAccountCtrl',
            controllerAs: 'ctrl',
            resolve: {
              sessionInfo: function(SessionService) {
                return SessionService.sessionSignedIn();
              }
            }
        })
        .otherwise({ redirectTo: "/SignIn" });
});

/*
 * Re-route to /SignIn if resolve could not
 * retrieve valid session info!
 */
app.run(function($rootScope, $location, SessionService, alertFactory) {
    $rootScope.$on( "$routeChangeStart", function(event, next, current) {
      var templateUrl = next.templateUrl;
      var promise = SessionService.sessionSignedIn();

      promise.then(function(data) {
        if (templateUrl == "ng/views/signIn.html" || templateUrl == "ng/views/register.html") {
          $location.path("/Event");
        }
      })
      .catch(function() {
        if (templateUrl != "ng/views/signIn.html" && templateUrl != "ng/views/register.html") {
          $location.path("/SignIn");
        }
      });
      
  })
});


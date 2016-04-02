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
        .when("/", {
            templateUrl: 'ng/views/signIn.html',
            controller: 'SignInCtrl',
            controllerAs: 'ctrl'
        })
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
                return SessionService.sessionResolve();
              }
            }
        })
        .when("/Group/:eventName/:lat/:lon/:timeStart/:timeEnd/:createdBy", {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl',
            resolve: {
              sessionInfo: function(SessionService) {
                return SessionService.sessionResolve();
              }
            }
        })
        .when("/Group", {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl',
            resolve: {
              sessionInfo: function(SessionService) {
                return SessionService.sessionResolve();
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
                return SessionService.sessionResolve();
              }
            }
        })
});

/*
 * Re-route to /SignIn if resolve could not
 * retrieve valid session info!
 */
app.run(function($rootScope, $location, SessionService, alertFactory) {
  $rootScope.$on("$routeChangeStart", function(event,
                                               current,
                                               previous) {
    console.log("routeChangeStart, event:");
    console.log(event);

    console.log("routeChangeStart, current:");
    console.log(current);

    console.log("routeChangeStart, event:");
    console.log(previous);
  });

  $rootScope.$on("$routeChangeError", function(event,
                                               current,
                                               previous,
                                               rejection) {
    $location.path("/SignIn");
    alertFactory.add('danger', rejection);
  });
});

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
        .when("/Event", {
            templateUrl: 'ng/views/event.html',
            controller: 'EventCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Group/:eventName/:lat/:lon/:timeStart/:timeEnd/:createdBy", {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Group", {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Profile", {
          templateUrl: 'ng/views/profile.html',
          controller: 'ProfileCtrl',
          controllerAs: 'ctrl'
        })
        .when("/SignIn", {
            templateUrl: 'ng/views/signIn.html',
            controller: 'SignInCtrl',
            controllerAs: 'ctrl'

        }).when("/SignOut", {
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
        });
});

app.run(function($rootScope, $location, SessionService, alertFactory) {
  SessionService.getSessionInfo();

  /*
   * Ovverride the default route change listener to prevent routing
   * to internal pages prior to sign-in/register
   *
   * References: http://stackoverflow.com/questions/11541695/
   *             redirecting-to-a-certain-route-based-on-condition
   */
  $rootScope.$on("$routeChangeStart", function(event, next, current) {
    if (SessionService.sessionInfo == null
        && (next.templateUrl != "ng/views/signIn.html"
            && next.templateUrl != "ng/views/register.html")) {
      $location.path("/SignIn");
      alertFactory.add('danger', 'User Must First Login!');
    }
  });
});

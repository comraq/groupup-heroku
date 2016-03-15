var app = angular.module('groupUpApp', ['ngAnimate', 'ngRoute', 'ngSanitize', 'ngMap']);

app.config(function ($routeProvider) {
    $routeProvider
        .when("/",
        {
        	templateUrl: 'ng/views/news.html',
            controller: 'NewsCtrl',
            controllerAs: 'ctrl'
        }) 
        .when("/Event",
        {
            templateUrl: 'ng/views/event.html',
            controller: 'EventCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Group",
        {
            templateUrl: 'ng/views/group.html',
            controller: 'GroupCtrl',
            controllerAs: 'ctrl'
        })
        .when("/SignIn",
        {
            templateUrl: 'ng/views/signIn.html',
            controller: 'SignInCtrl',
            controllerAs: 'ctrl'
        })
        .when("/Register",
        {
            templateUrl: 'ng/views/register.html',
            controller: 'RegisterCtrl',
            controllerAs: 'ctrl'
        });
        //.otherwise({redirectTo: "/"});
});
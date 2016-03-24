var app = angular.module('groupUpApp', ['ngAnimate', 'ngRoute', 'ngSanitize', 'alertService']);

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
        })
        .when("/Account",
        {
            templateUrl: 'ng/views/userAccount.html',
            controller: 'UserAccountCtrl',
            controllerAs: 'ctrl',
            resolve:{
                email: function(){ return "testUser1@test.com";}
            }
        });
        //.otherwise({redirectTo: "/"});
});
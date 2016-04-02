angular.module('groupUpApp').service("SessionService", function($http, $q){
  // To cache login status, avoids unnecessary HTTP requests to server
  var logAttempt = false;

  return {
    sessionInfo: null,

    isLoggedIn: function() {
      return this.sessionInfo != null;
    },

    getSessionInfo: function() {
      return $http.get('controller/login/getSessionInfo').then(
           function successCallback(response) {
             // accountType: user = 0, eventprovider = 1, admin = 2
             this.sessionInfo = response.data;
           }.bind(this));
    },

    sessionSignedIn: function() {

        var session = $q.defer();
      $http.get('controller/login/getSessionInfo').then(
           function successCallback(response) {
             // accountType: user = 0, eventprovider = 1, admin = 2
             this.sessionInfo = response.data;
             if (!response.data){
              session.reject("Need to be logged in");
             }else if (response.data){
               session.resolve(response.data);
             } 
           }.bind(this));

      return session.promise;
    },

    login: function(email, password, accountType) {
      return $http({
        method: 'POST',
        url: 'controller/login/login',
        data: {
          email: email,
          password: password,
          accountType: accountType
        }
      }).then(function successCallback(response) {
        this.sessionInfo = response.data;
        logAttempt = true;
      }.bind(this));
    },

    logout: function() {

      return $http.get('/controller/login/doLogout').then(
           function successCallback(response) {
             this.sessionInfo = null;
             logAttempt = false;
           }.bind(this));

    }

  };
});

angular.module('groupUpApp').service("SessionService", function($http, $q){
  // To cache login status, avoids unnecessary HTTP requests to server
  var logAttempt = true;
  var logoutAttempt = true;

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
      var reason = "signedInReject";

      if(this.sessionInfo)
        return this.sessionInfo;

      if (!logAttempt)
        return $q.reject(reason);

      var session = $q.defer();
      $http.get('controller/login/getSessionInfo').then(
           function successCallback(response) {
             // accountType: user = 0, eventprovider = 1, admin = 2
             this.sessionInfo = response.data;
             if (response.data)
               session.resolve(response.data);
             else {
               logAttempt = false;
               session.reject(reason);
             }

           }.bind(this));

      return session.promise;
    },

    sessionSignedOut: function() {
      var reason = "signedOutReject";

      if(this.sessionInfo)
        return $q.reject(reason);

      if (!logoutAttempt)
        return $q.reject(reason);

      var session = $q.defer();
      $http.get('controller/login/getSessionInfo').then(
           function successCallback(response) {
             // accountType: user = 0, eventprovider = 1, admin = 2
             this.sessionInfo = response.data;
             if (response.data)
               session.reject(reason);
             else
               session.resolve(response.data);

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

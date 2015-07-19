var angular = angular;

var services = angular.module('myApp.services', ['ngResource', 'ngSanitize']);

/*
|-----------------------------------------------------------------------------------------------------------------
| SERVICES :: Login and Authebtication
|-----------------------------------------------------------------------------------------------------------------
*/
services.factory('SessionService', function(){
	return {
		get: function(key){
			return sessionStorage.getItem(key);
		},
		set: function(key, val){
			return sessionStorage.setItem(key, val);
		},
		unset: function(key){
			//return sessionStorage.removeItem(key);
			return sessionStorage.clear();
		}
	}
});


// Display Success and Error messages on the $rootScope ...
services.factory('FlashService', function($rootScope) {
	return {
		show: function(message) {
			$rootScope.flash = message;
		},
		clear: function() {
			$rootScope.flash = '';
		}
	};
});


// Authentication Service
services.factory('AuthenticationService', function($http, $sanitize, SessionService, FlashService, CSRF_TOKEN){

	var cacheSession = function(){
		SessionService.set('authenticated', true);
	};
	var uncacheSession = function(){
		SessionService.unset('authenticated');
	};
	var loginError = function(response) {
		FlashService.show(response.flash);
	};
	var loginErrorReg = function(response) {
		FlashService.show(response.flash[0][0]);
	};
	var resetPass = function(response) {
		FlashService.show(response.flash);
	};


	return {
		register: function(regCredentials){
			var register = $http.post('/track_challenge/public/auth/register', angular.extend(regCredentials, CSRF_TOKEN));
			register.success(cacheSession);
			register.success(FlashService.clear);
			register.error(loginErrorReg);
			return register;
		},

		login: function(credentials){
			var login = $http.post('/track_challenge/public/auth/login', angular.extend(credentials, CSRF_TOKEN));
			login.success(cacheSession);
			login.success(FlashService.clear);
			login.error(loginError);
			return login;
		},

		logout: function(){
			var logout = $http.get('/track_challenge/public/auth/logout');
			logout.success(uncacheSession);
			return logout;
		},

		isLoggedIn: function(){
			return SessionService.get('authenticated');
		},

		resetPassword: function(passCredentials){
			var resetPassword = $http.post('/track_challenge/public/auth/resetPassword', angular.extend(passCredentials, CSRF_TOKEN));
			resetPassword.success(cacheSession);
			resetPassword.success(resetPass);
			resetPassword.error(resetPass);
			return resetPassword;
		}

	};

});



/*
|-----------------------------------------------------------------------------------------------------------------
| FACTORY :: AthletesFactory
|-----------------------------------------------------------------------------------------------------------------
*/
services.factory('AthletesFactory', function($resource) {
	
	return $resource('/track_challenge/public/athletes/selections/:id/:gender/:user', {}, {
		show: { method: 'GET', isArray: false, dataType:"json" },
		get: { method: 'GET', isArray: false, params: {id: '@id', gender: '@gender', user: '@user'} }
	});

});


/*
|-----------------------------------------------------------------------------------------------------------------
| FACTORY :: SelectionsFactory
|-----------------------------------------------------------------------------------------------------------------
*/
services.factory('SelectionsFactory', function($resource) {
	
	return $resource('/track_challenge/public/selections/selections/:user_id/:event_id/:gender', {}, {
		show: { method: 'GET', isArray: false, params: {user_id: '@user_id', event_id: '@event_id', gender: '@gender'}},
		create: { method: 'POST' },
		get: { method: 'GET', isArray: false, params: {id: '@user_id'} }
	});

});


/*
|-----------------------------------------------------------------------------------------------------------------
| FACTORY :: SelectionsTestFactory
|-----------------------------------------------------------------------------------------------------------------
*/
services.factory('SelectionsDisplayFactory', function($resource) {
	
	return $resource('/track_challenge/public/selections/:id/:gender', {}, {
		show: { method: 'GET', isArray: false, params: {id: '@id', gender: '@gender'} }
	});

});


/*
|-----------------------------------------------------------------------------------------------------------------
| FACTORY :: FriendFactory
|-----------------------------------------------------------------------------------------------------------------
*/
services.factory('FriendFactory', function($resource) {
	
	return $resource('/track_challenge/public/email/:id/:email', {}, {
		show: { method: 'GET', isArray: false, params: {id: '@id', email: '@email'} }
	});

});


/*
|-----------------------------------------------------------------------------------------------------------------
| FACTORY :: StatsFactory
|-----------------------------------------------------------------------------------------------------------------
*/
services.factory('StatsFactory', function($resource) {
	
	return $resource('/track_challenge/public/stats/:event_id/:gender', {}, {
		show: { method: 'GET', isArray: false, params: {event_id: '@event_id', gender: '@gender'} }
	});

});





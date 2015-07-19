var myApp = angular.module('myApp', ['myApp.services', 'myApp.mainCtrl', 'ngRoute', 'ngSanitize', 'ngAnimate', 'angular-sortable-view']);

// angular.module('mymod', []).config(['$httpProvider', function($httpProvider) {
//         $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
//     }]);

myApp.config(['$routeProvider', function ($routeProvider) {

		/*
		|-----------------------------------------------------------------------------------------------------------------
		| LIST ROUTES
		|-----------------------------------------------------------------------------------------------------------------
		*/
		$routeProvider.when('/login', // Display home/login page
			{
				templateUrl: 'partials/login.html',
				controller: 'LoginCtrl',
			});

		$routeProvider.when('/selections/:id/:gender', // Display list of athletes for each event
			{
				templateUrl: 'partials/selections.html',
				controller: 'SelectCtrl'
			});

		$routeProvider.when('/picks/:gender', // Display full list of users medal picks for each event
			{
				templateUrl: 'partials/picks.html',
				controller: 'PicksCtrl'
			});

		$routeProvider.when('/email', // Display list of athletes for each event
			{
				templateUrl: 'partials/email.html',
				controller: 'EmailCtrl'
			});

		//If none of the above routes exist - redirect to /login (default)
		$routeProvider.otherwise(
			{
				redirectTo: '/login'
			});
		
	}]);



// HTTP Interceptor ....
// If return 401 Not Authorised .. send back to login page ...
var interceptor = function ($q, $location, FlashService) {
	return {
		request: function (config) {
			//console.log(config);
			return config;
		},

		response: function (result) {
			//console.log('Success!');
			return result;
		},

		responseError: function (rejection) {
			//console.log('Failed with', rejection.status, 'status');
			if (rejection.status == 403) {
			//SessionService.unset('authenticated');
			sessionStorage.clear();
			FlashService.show(rejection.data.flash);
			$location.path('/login');
		}

			return $q.reject(rejection);
		}
	}
};



/*
|-----------------------------------------------------------------------------------------------------------------
| RUN TIME
|-----------------------------------------------------------------------------------------------------------------
*/

myApp.config(function ($httpProvider) {
        $httpProvider.interceptors.push(interceptor);
    })
	.run(function($rootScope, $location, AuthenticationService, FlashService){

		// Whitelist (the /login page is the only page that does NOT require Auth!) .. all other pages do!
		var routesThatRequireAuth = ['/login'];

		console.log($location.path());

		$rootScope.$on('$routeChangeStart', function(event, next, current){
			if( !_(routesThatRequireAuth).contains($location.path()) && !AuthenticationService.isLoggedIn() ) {
			$location.path('/login');
			FlashService.show('Please login to continue ...');
		}

	});

});

/*
|-----------------------------------------------------------------------------------------------------------------
| CUSTOM DIRECTIVES
|-----------------------------------------------------------------------------------------------------------------
*/
// Used for sorting the Medalists selections
myApp.directive('highlightOnLoad', function($parse){
	return {
		compile: function($element, $attrs){
			return function($scope, $element, $attrs){
				$element.text($parse($attrs.highlightOnLoad)($scope));
				PR.prettyPrint();
			};
		}
	};
}).
directive('highlightAfterDigest', function($parse){
	return function($scope, $element, $attrs){
		$scope.$$postDigest(function(){
			PR.prettyPrint();
		});
	};
})

// Used for Bootstrap 'Tabs' on home page login/registration etc ...
.directive('showtab',
    function () {
		return {
			link: function (scope, element, attrs) {
			element.click(function(e) {
				e.preventDefault();
				$(element).tab('show');
			});
		}
	};
})

// Used to overcome 'Saved Password' failure to login
// Doesn't allow user to login with saved username / password details
// This directive along with (data-trigger-change="#password,#email") in the form solves it
.directive('triggerChange', function($sniffer) {
	return {
		link : function(scope, elem, attrs) {
			elem.bind('click', function(){
				$(attrs.triggerChange).trigger($sniffer.hasEvent('input') ? 'input' : 'change');
			});
		},
		priority : 1
	};
})

// Scroll To directive - scrolls to a position on page
.directive('scrollToItem', function() {                                                      
    return {                                                                                 
        restrict: 'A',                                                                       
        scope: {                                                                             
            scrollTo: "@"                                                                    
        },                                                                                   
    link: function(scope, $elm, attr) {                                                   

        $elm.on('click', function() {                                                    
            $('html,body').animate({scrollTop: $(scope.scrollTo).offset().top - $(window).scrollTop() }, "slow");
        });                                                                              
    }                                                                                    
}});


/*
|-----------------------------------------------------------------------------------------------------------------
| CUSTOM FILTERS
|-----------------------------------------------------------------------------------------------------------------
*/

var app = angular.module('myApp.mainCtrl', []);

/*
|-----------------------------------------------------------------------------------------------------------------
| NAME :: LoginCtrl
|-----------------------------------------------------------------------------------------------------------------
*/
app.controller('LoginCtrl', ['$scope', '$rootScope', '$routeParams', '$location', 'AuthenticationService', 'FlashService', 'CSRF_TOKEN',
	function ($scope, $rootScope, $routeParams, $location, AuthenticationService, FlashService, CSRF_TOKEN) {

		$scope.credentials = {login_email: '', login_password: ''};

		$rootScope.loggedOut = !$rootScope.loggedOut; // Show/hide the nav-bar


		// LOG USER IN
		$scope.login = function(){
			AuthenticationService.login($scope.credentials).success(function(data){
				$location.path('/selections/1/m');
				sessionStorage.setItem('user_id', data[0].id); // Save to sessionStorage 'user_id'
				sessionStorage.setItem('screen_name', data[0].screen_name); // Save to sessionStorage 'screen_name'
				sessionStorage.setItem('gravatar', data[1]); // Save to sessionStorage 'gravatar'

				$rootScope.loggedOut = !$rootScope.loggedOut; // Show/hide the nav-bar
			});

		};
		

		// LOG USRE OUT
		$rootScope.logout = function(){
			AuthenticationService.logout().success(function(){
				//sessionStorage.clear(); // Clears ALL sessionStorage!
				$location.path('/login');
			});
		};
		

		// ACCEPT NEW USER REGISTRATION
		$scope.register = function(regCredentials){
			AuthenticationService.register($scope.regCredentials).success(function(data){

				//console.log('this data: ' + data.insertId);

				sessionStorage.setItem('user_id', data.insertId); // Save to sessionStorage 'user_id'
				sessionStorage.setItem('screen_name', data.screen_name); // Save to sessionStorage 'screen_name'

				$location.path('/selections/1/m'); // Redirect new registered member to 1st event (100m)
				
				$rootScope.loggedOut = !$rootScope.loggedOut; // Show/hide the nav-bar
			});

		};


		// LOST / RESET PASSWORD
		$scope.resetPassword = function(passCredentials){

			//console.log('so you have lost your password?');
			AuthenticationService.resetPassword($scope.passCredentials).success(function(data){
				console.log(data);
				$scope.passCredentials = '';
			});
		}


		// Clears Login Error Messages
		$scope.clearErrorMessage = function() {
			FlashService.clear();
		}
		

}]); // ENDS LoginCtrl



/*
|-----------------------------------------------------------------------------------------------------------------
| NAME :: SelectCtrl
|-----------------------------------------------------------------------------------------------------------------
*/
app.controller('SelectCtrl', ['$scope', '$rootScope', '$routeParams', '$location', '$timeout', 'AuthenticationService', 'AthletesFactory', 'SelectionsFactory', 'SelectionsDisplayFactory',
	function ($scope, $rootScope, $routeParams, $location, $timeout, AuthenticationService, AthletesFactory, SelectionsFactory, SelectionsDisplayFactory) {


		/********************************************************************************/
		// Check to see if user is still 'Authenticated'
		/********************************************************************************/

		//sessionStorage.removeItem('authentication');
		window.scrollTo(0, 0);
		

		/********************************************************************************/
		// Initialise key variables
		/********************************************************************************/
		$scope.orderPicks = [];
		$scope.selected = [];
		$scope.max3 = false;
		$scope.sortPicks = 'Add three athletes';
		$scope.currentEvent = parseInt($routeParams.id);
		$scope.currentGender = $routeParams.gender;
		$scope.routeParams = $routeParams.id; // Used to envoke a class on the current selected event in the medalists table



		// Prevent user from going out of 'event' range (i.e., below 100m or over 50km Walk)
		if($scope.currentEvent < 1 || $scope.currentEvent > 24) {
			$location.path('/selections/1/' + $routeParams.gender);
		}

		// Get current userID and screen_name
		$scope.orderPicks.userID = sessionStorage.getItem('user_id'); // Current user_id
		$rootScope.screen_name = sessionStorage.getItem('screen_name'); // Current screen_name
		$rootScope.gravatar = sessionStorage.getItem('gravatar'); // Current gravatar

		// Initialise $scopr.athletes as empty object
		$scope.athletes = {};


		// Show/hide Experts panel
		$scope.showExperts = false;
		$scope.toggle = function() {
			$scope.showExperts = !$scope.showExperts;
		};


		// Show/hide Events list panel
		$scope.showEvents = false;
		$scope.toggleEvt = function() {
			$scope.showEvents = !$scope.showEvents;
		};



		
		/********************************************************************************/
		// 1. GET LIST OF ATHLETES FOR EACH EVENT
		/********************************************************************************/
		$scope.startup = function startup(){

			$scope.loading = true;
	
			$scope.theGender = ($routeParams.gender === 'm') ? 'm' : 'w'; // Get the current gender
			$scope.theGenderLabel = ($routeParams.gender === 'm') ? 'Mens' : 'Womens'; // Get the current genderLabel


			$scope.athletes = AthletesFactory.show({id: $routeParams.id, gender: $routeParams.gender});

			$scope.athletes.$promise.then(function(result) {

				$scope.eventID = result.eventName[0]; // Gets Event ID
				$scope.eventName = result.eventName[0]; // Gets Event Name
				$scope.eventOrder = result.eventName[0]; // Gets Event Order
				$scope.athletes = result.athlete; // Gets Athletes for selection list

				
				$scope.expert_picks = result.expert_picks; // Gets the 1st Experts Picks ...

				console.log($scope.expert_picks);


				$scope.eventDropDowns = result.eventDropDown; // Gets FULL list of events from 'events' drop-down select

				//$scope.numAthletes = $scope.athletes.length; // Gets number of athletes in selection list
				//console.log($scope.numAthletes);

				// Add three manual wildcard ('Outside Athletes') to main list
				// if( ! $scope.athletes) - don't puch these values to the view
				if( $scope.athletes != 'no data') {
					$scope.athletes.push({id: 9999, name_last:'X Wildcard', name_first:'Athlete', country:'XXX', rank: 'X'});
					$scope.athletes.push({id: 9999, name_last:'X Wildcard', name_first:'Athlete', country:'XXX', rank: 'X'});
					$scope.athletes.push({id: 9999, name_last:'X Wildcard', name_first:'Athlete', country:'XXX', rank: 'X'});
				} else {
					$scope.athletes = [];
				}
				
				$scope.orderPicks.eventID = $scope.eventID; // Declare the eventID in the $scope for use in function submit()

				$scope.loading = false;

				// Now delete the 'success' message! (i.e., remove sessionStorage 'saved')
				sessionStorage.removeItem('saved');


			});
			

			//Has this event been completed?
			$scope.completed = []; // Initiate var
			$scope.completed = AthletesFactory.get({id: $routeParams.id, gender: $routeParams.gender, user: $scope.orderPicks.userID});
			$scope.completed.$promise.then(function(result) {

				if(result.found_user_pick === true) {
					$scope.completed = '<strong><span class="green"><i class="fa fa-check"></i> Completed</span></strong>';
				} else {
					$scope.completed = '<strong><span class="red"><i class="fa fa-times"></i> No selections made!</span></strong>';
				}
			});

		}

		// Initiate above function
		$scope.startup();

		// Binds the current page ($routeParams.id) to the selected value of 'events' drop-down select menu
		//$scope.eventMenu = $routeParams.id;


		
		
				


		/********************************************************************************/
		// 2. CLICK / SELECT THREE (3) ATHLETES
		/********************************************************************************/
		$scope.clickSelection = function clickSelection(athleteID) {

			// Order
			var index = $scope.selected.indexOf(athleteID);
			//console.log('Show index A: ' + index);
			//console.log($scope.selected.indexOf(athleteID));
			
			// List of athletes to make picks from 
			if (index > -1) { 
				$scope.selected.splice(index, 1); // if selection is 'unclicked' deselect it
			} else { 
				
				$scope.selected.push(athleteID); // if selection is 'clicked' select it
			}


			// Push selections above to re-ordering column
			var index = $scope.orderPicks.indexOf(athleteID);
			//console.log('Show index B: ' + index);

			// List of selected athletes - ready to reorder
			if (index > -1) {
				$scope.orderPicks.splice(index, 1); // Remove from '$scope.orderPicks' array[]
			} else {
				$scope.orderPicks.push(athleteID); // Add to '$scope.orderPicks' array[]
				$scope.max3 = true;
			}

			// Once 3 x athletes have been selected ($scope.max3) envoke classes and messages
			if( $scope.orderPicks.length > 2) {
				$scope.max3 = true;
				$scope.sortPicks = 'Drag athlete(s) name to change medal position ...';

				// Scroll mobile users to 'Save' medalists section
				function moveMe(){
					$('html,body').animate({
							scrollTop: 800,
					}, 800, function(){
						$('html,body').clearQueue();
					}); 
				};

				$timeout(moveMe, 300);

			} else {
				$scope.max3 = false;
				$scope.sortPicks = 'Add three athletes';
			}

			$scope.isChecked = false;

		};



		// Keeps the draggable items contained within their parent!
		$scope.containmentChange = function(val){
			this.opts.containment = val ? '.sortable-container' : 'html';
		};



		/********************************************************************************/
		// 3. INSERT NEW PICKS FUNCTION
		/********************************************************************************/
		$scope.insertNewPicks = function(values) {

			$scope.loading = true;

			$scope.insertNew = SelectionsFactory.create(values).$promise.then(function(data){

				$scope.selectionMessage = 'Selections Saved!';
				$scope.loading = false;

				// Advance to next event via $routeParams ...
				$location.path('/selections/' + (parseInt($routeParams.id) + 1) + '/' + $routeParams.gender);

			});

		};



		/********************************************************************************/
		// 4. UPDATE EXISTING PICKS FUNCTION
		/********************************************************************************/
		$scope.updatePicks = function(values){

			$scope.loading = true;

			$scope.update = SelectionsFactory.create(values).$promise.then(function(){

				$scope.selectionMessage = 'Selections Updated!';
				$scope.loading = false;

				// Advance to next event via $routeParams ...
				$location.path('/selections/' + (parseInt($routeParams.id) + 1) + '/' + $routeParams.gender);
				
			});

		}



		/********************************************************************************/
		// 5. INSERT OR UPDATE SELECTIONS FOR USER
		// First, use check() to see if any picks exist in the database for the user_id and event_id
		// If data exists -> update
		// Else -> insert with submit()
		/********************************************************************************/
		$scope.check = function(userID) {

			SelectionsFactory.show({user_id: userID, event_id: $routeParams.id, gender: $scope.theGender}).$promise.then(function(data){

				if(data.found_user_pick === true) {

					// Update existing picks via 'updatePicks()' function
					$scope.myArray = {
						id: data.user_pick[0].id,
						id2: data.user_pick[1].id,
						id3: data.user_pick[2].id,
						user_id: $scope.orderPicks.userID,
						event_id: $scope.orderPicks.eventID.eventID,
						gender: $scope.theGender,
						gold: $scope.orderPicks[0].id,
						silver: $scope.orderPicks[1].id,
						bronze: $scope.orderPicks[2].id
					};

					// Call function $scope.updatePicks(data)
					$scope.updatePicks($scope.myArray);

					
				} else {

					// Insert new picks via 'insertNewPicks()' function
					$scope.myArray = {
						user_id: $scope.orderPicks.userID,
						event_id: $scope.orderPicks.eventID.eventID,
						gender: $scope.theGender,
						gold: $scope.orderPicks[0].id,
						silver: $scope.orderPicks[1].id,
						bronze: $scope.orderPicks[2].id
					};

					// Call function $scope.insertNewPicks(data)
					$scope.insertNewPicks($scope.myArray);

				}

			});

		};


		/********************************************************************************/
		// 5. UNCHECK ALL PICKS (Checkboxes)
		// This allows the user to click the 'Change' button and reselect their medal choices
		/********************************************************************************/
		$scope.uncheckAll = function() {
			angular.forEach($scope.athletes, function (athlete) {
				athlete.isChecked = false;
			});
			
		};
		


}]); // ENDS SelectCtrl



/*
|-----------------------------------------------------------------------------------------------------------------
| NAME :: PicksCtrl
|-----------------------------------------------------------------------------------------------------------------
*/
app.controller('PicksCtrl', ['$scope', '$routeParams', 'SelectionsDisplayFactory',
	function ($scope, $routeParams, SelectionsDisplayFactory) {

		/********************************************************************************/
		// Set 'Men' or 'Women' as active gender (in sessionStorage)
		/********************************************************************************/
		$scope.currentGender = $routeParams.gender;
		$scope.theGenderLabel = ($routeParams.gender === 'm') ? 'Mens' : 'Womens'; // Get the current genderLabel
		sessionStorage.setItem('gender', $routeParams.gender); // Save to sessionStorage 'gender'

		$scope.gender = function gender(val){

			if( val === 'm') {
				sessionStorage.setItem('gender', 'm');
			} else {
				sessionStorage.setItem('gender', 'w');
			}

		}


		/********************************************************************************/
		// Display users previous selections
		/********************************************************************************/
		$scope.getPicks = function getPicks() {

			$scope.loading = true;

			// Initialise key variables
			$scope.orderPicks = [];
			$scope.orderPicks.userID = sessionStorage.getItem('user_id'); // Current userID
			$scope.theGender = sessionStorage.getItem('gender'); // Current event gender
			$scope.screen_name = sessionStorage.getItem('screen_name'); // Current user screen_name

			// Get logged in users medal picks ...
			$scope.mySelections = SelectionsDisplayFactory.show({id: $scope.orderPicks.userID, gender: $scope.theGender});
			$scope.mySelections.$promise.then(function(result) {

				$scope.mySelections = result.my_picks; // Gets User Picks (Gold, Silver, Bronze etc)
				$scope.loading = false;

				console.log($scope.mySelections);

			});

		}


		/********************************************************************************/
		// Initiate above function
		/********************************************************************************/
		$scope.getPicks();

		

}]); // ENDS PicksCtrl



/*
|-----------------------------------------------------------------------------------------------------------------
| NAME :: EmailCtrl
|-----------------------------------------------------------------------------------------------------------------
*/
app.controller('EmailCtrl', ['$scope', '$rootScope', '$routeParams', 'FriendFactory', 'CSRF_TOKEN',
	function ($scope, $rootScope, $routeParams, FriendFactory, CSRF_TOKEN) {

		// Get current userID and screen_name
		$scope.userID = sessionStorage.getItem('user_id'); // Current user_id
		$rootScope.screen_name = sessionStorage.getItem('screen_name'); // Current screen_name


		// EMAIL FRIEND WITH YOUR PICKS
		$scope.emailFriend = function(credentials){

			$scope.loading = true;

			$scope.emailFriend = FriendFactory.show({id: $scope.userID, email: $scope.credentials.email});
			$scope.emailFriend.$promise.then(function(result) {

				$scope.loading = false;

				$scope.emailSelections = result.email_picks[0]; // Object containing ALL users picks to email ...
				$scope.flash = result.flash; // Display success message
				$scope.credentials.email = ''; // Clear email input box

				//console.log($scope.emailSelections);
			});

			//console.log('keen to email a friend with your picks? ' + $scope.userID);

		}
		

}]); // ENDS EmailCtrl



/*
|-----------------------------------------------------------------------------------------------------------------
| NAME :: StatsCtrl
|-----------------------------------------------------------------------------------------------------------------
*/
app.controller('StatsCtrl', ['$scope', '$rootScope', '$routeParams', 'StatsFactory', 'CSRF_TOKEN',
	function ($scope, $rootScope, $routeParams, StatsFactory, CSRF_TOKEN) {

		$scope.stats = StatsFactory.show({event_id: $routeParams.id, gender: $routeParams.gender});
		$scope.stats.$promise.then(function(result) {
			$scope.stats = result.all_user_pick;
		});
	

}]); // ENDS StatsCtrl

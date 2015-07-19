<!doctype html>
<html lang="en" ng-app="myApp">

<head>
	<meta charset="UTF-8">
	<title>Bejing, 2015 Challenge</title>

	<!-- <meta name="csrf-token" ng-init="csrf_token = '<?php //echo csrf_token(); ?>'"> -->

	<!-- META -->
	<meta name="description" content="World Track Challenge">
	<meta name="viewport" content="width=device-width">
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no, minimum-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="mobile-web-app-capable" content="yes">


	<!-- CSS -->
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="dist/css/style.min.css">
	
	<!--<script src="js/lib/aangular.min.js"></script>-->




</head>
<body>

	<!-- Scroll to here from top 'View Picks' button -->
	<a id="top-scroll" name="top"></a>

	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation" ng-show="!loggedOut">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" ng-init="navCollapsed = true" ng-click="navCollapsed = !navCollapsed">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="" scroll-to-item scroll-to="#top-scroll"><img ng-src="{{gravatar}}" class="gravatar"> <strong>{{screen_name}}</strong></a>
			</div>
			<div class="collapse navbar-collapse" ng-class="!navCollapsed && 'in'" ng-click="navCollapsed=true">
				<ul class="nav navbar-nav">
					<li><a href="#/selections/1/m"><i class="fa fa-home"></i> HOME</a></li>
					<li><a href="#/email"><i class="fa fa-user-plus"></i> INVITE FRIEND</a></li>
					<li><a href="#/logout" ng-click="logout()"><i class="fa fa-unlock-alt"></i> LOGOUT</a></li>
				</ul>
			</div>
		</div>
	</div>



	


	

	<!-- Add your site or application content here -->
	<div class="container view-animate-container">
		<div ng-view=""></div>
		<!-- <div ng-view="" class="view-animate" autoscroll></div> -->
		
		<!-- <div class="col-sm-12 footer">
			<p class="copyright"><small>©Copyright <a href="http://www.lovegrovedesign.co.nz" target="_blank">Lovegrove Design.</a> All Rights Reserved.</small> </p>
		</div> -->
		
	</div><!--ENDS container-->


	
	<!-- JS 
	<script src="js/lib/angular-animate.min.js"></script>
	<script src="js/lib/angular-cookies.min.js"></script>
	<script src="js/lib/angular-resource.min.js"></script>
	<script src="js/lib/angular-route.min.js"></script>
	<script src="js/lib/angular-sanitize.min.js"></script>
	<script src="js/lib/angular-sortable-view.min.js"></script>
	<script src="js/lib/bootstrap.min.js"></script>
	<script src="js/lib/underscore-min.js"></script>-->


	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="dist/js/main.js"></script>

	<!-- ANGULAR -->
	<!-- all angular resources wull be loaded from the /public folder -->
	<script src="js/app.js"></script> <!--load our application -->
	<script src="js/services/services.js"></script> <!--load our services -->
	<script src="js/controllers/mainCtrl.js"></script> <!--load our mainCtrl controller -->

	<!-- CSRF TOKEN --> 
	<script>
		angular.module('myApp').constant('CSRF_TOKEN', {
			csrf_token: '<?php echo csrf_token(); ?>'
		});
	</script>


</body>
</html>

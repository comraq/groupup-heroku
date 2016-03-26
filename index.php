<!DOCTYPE html>
<html ng-app="groupUpApp">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- jQuery -->
    <script src="./components/jquery/jquery-2.2.0.js" type="text/javascript" charset="utf-8"></script>
    <script src="./components/jqueryUI/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
    <script src="./components/jqueryUI/jquery-ui-combobox.js" type="text/javascript" charset="utf-8"></script>
    <!-- bootstrap -->
    <link rel="stylesheet" type="text/css" href="./components/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./components/bootstrap/css/bootstrap-theme.css">
    <link rel="stylesheet" type="text/css" href="./components/jqueryUI/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="./components/jqueryUI/jquery-ui.theme.css">
    <link rel="stylesheet" type="text/css" href="./components/jqueryUI/jquery-ui-combobox.css">
    <link rel="stylesheet" type="text/css" href="components/angular-bootstrap-datetimepicker/src/css/datetimepicker.css" />
    <link rel="stylesheet" type="text/css" href="./components/angular-toggle-switch/angular-toggle-switch.css">
    <link rel="stylesheet" type="text/css" href="./components/angular-toggle-switch/angular-toggle-switch-bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./components/layout.css">
    
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?key=AIzaSyAOa4D0qXOm64G0MOUCdJjHEd-TZKGXkjM"></script>

    <script type="text/javascript" src="./components/angular-1.5.0/angular.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-route.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-animate.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-sanitize.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-resource.js"></script>
    <script type="text/javascript" src="./components/angular-google-maps/ng-map.min.js"></script>
    <script type="text/javascript" src="./components/angular-selection-model/selection-model.js"></script>
    <script type="text/javascript" src="./components/angular-fixed-header-table/angu-fixed-header-table.js"></script>

    <script type="text/javascript" src="./ng/app.js"></script>
    <script type="text/javascript" src="./ng/directives/alert.js"></script>
    <script type="text/javascript" src="./ng/controllers/newsCtrl.js"></script>


    <script type="text/javascript" src="./ng/controllers/eventCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/groupCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/signInCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/signOutCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/registerCtrl.js"></script>
    <script type="text/javascript" src="./components/bootstrap/js/bootstrap.js" charset="utf-8" async defer></script>
    <script type="text/javascript" src="./components/angular-ui-bootstrap/ui-bootstrap-tpls-1.2.5.min.js"></script>
    <script type="text/javascript" src="components/moment/moment.js"></script>
    <script type="text/javascript" src="components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js"></script>
    <script type="text/javascript" src="components/angular-bootstrap-datetimepicker/src/js/datetimepicker.templates.js"></script>
    <script type="text/javascript" src="components/angular-toggle-switch/angular-toggle-switch.min.js"></script>
    
  <!-- Custom Stylesheets -->
  <link rel="stylesheet" type="text/css" href="./components/custom-stylesheets/group-styles.css">
  <link rel="stylesheet" type="text/css" href="./components/custom-stylesheets/event-styles.css">

    <title>GroupUp | Attend Events in a Group</title>
</head>

<?php
	require_once("controller/login.php");

	$login = new Login();

	if ($login->isUserLoggedIn() == true) {
	    echo "Status message: logged in" . "<br />" . "Account type: " . $login->getAccountType() . "<br />" . "Email: " . $login->getEmail();

	} else {
	    echo "Status message: not logged in" . "<br />" . "Account type: " . $login->getAccountType() . "<br />" . "Email: " . $login->getEmail();;
	}
?>

<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#/">GroupUp</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="page"><a href="#/">News<span class="sr-only">(current)</span></a></li>
					<li class="page"><a href="#/Event">Event</a></li>
					<li class="page"><a href="#/Group">Group</a></li>														
				</ul>

				<form class="navbar-form navbar-left" role="search">
					<div class="form-group">
						<input type="text" class="form-control" placeholder="Search">
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
				<?php 
					if ($login->isUserLoggedIn() == false) { 
				?>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#/SignIn">Sign In</a></li>
						<li><a href="#/Register">Register</a></li>
					</ul>
				<?php 
					} 
				?>
				<?php 
					if ($login->isUserLoggedIn() == true) { 
				?>
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#/SignOut">Sign Out</a></li>
					</ul>
				<?php 
					} 
				?>
			</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

    <alerts></alerts>

	<div class="container-fluid">
		<div data-ng-view></div>
	</div><!-- /.container-fluid -->
</body>
</html>


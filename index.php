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
	<link rel="stylesheet" type="text/css" href="./components/layout.css">
	<script src="./components/bootstrap/js/bootstrap.js" type="text/javascript" charset="utf-8" async defer></script>

	 <!-- Angular Js -->
	 <!--
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-resource.min.js">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0/angular-route.min.js"></script>
-->

    <script type="text/javascript" src="./components/angular-1.5.0/angular.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-route.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-animate.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-sanitize.js"></script>
    <script type="text/javascript" src="./components/angular-1.5.0/angular-resource.js"></script>


    <script type="text/javascript" src="./ng/app.js"></script>
    <script type="text/javascript" src="./ng/directives/alert.js"></script>
    <script type="text/javascript" src="./ng/controllers/newsCtrl.js"></script>    
    <script type="text/javascript" src="./ng/controllers/eventCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/groupCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/signInCtrl.js"></script>
    <script type="text/javascript" src="./ng/controllers/registerCtrl.js"></script>
    

	<title>GroupUp | Attend Events in a Group</title>
</head>

<?php
	require_once("controller/login.php");

	$login = new Login();

	if ($login->isUserLoggedIn() == true) {
	    echo "Status message: logged in" . "<br />" . $login->getAccountType();

	} else {
	    echo "Status message: not logged in" . "<br />" . $login->getAccountType();
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
						<!-- <form method="post" action="/" name="loginform">

						    <label for="login_input_email">Email</label>
						    <input id="login_input_email" class="login_input" type="text" name="email" required />

						    <label for="login_input_password">Password</label>
						    <input id="login_input_password" class="login_input" type="password" name="password" autocomplete="off" required />

						    <input type="submit"  name="login" value="Log in" />

						</form> -->
						<li><a href="#/Register">Register</a></li>
					</ul>
				<?php 
					} 
				?>
				<?php 
					if ($login->isUserLoggedIn() == true) { 
				?>
					<ul class="nav navbar-nav navbar-right">
						<!-- <li><a href="#/SignOut">Sign Out</a></li> -->
						<li><a href="index.php?logout">Logout</a></li>
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


<!--
	Created by Matthew Williams
-->
<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="css/all.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="js/login.js"></script>
	</head>
	<body>
		<div id="darkbox" style="display: block;">
			<div id="lightbox">
				<h3>Log in to your account</h3>
				<input type="text" id="username-input" placeholder="username" onkeypress="return enterAttemptLogin(event)"><br>
				<input type="password" id="password-input" placeholder="password" onkeypress="return enterAttemptLogin(event)"><br><br>
				<a href="#" onclick="attemptLogin()">Login</a>
				<div id="output-message"></div>
			</div>
		</div>
	</body>
</html>
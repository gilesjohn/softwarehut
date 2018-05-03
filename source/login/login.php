<?php
	// Created by Giles Holdsworth

	require (__DIR__ . "/user_system.php");

	if (isset($_POST["t"])) {
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error());
		}

		if ($_POST["t"] === "login") {
			// User has attempted to login
			if (attempt_login($username, $password, $conn)) {
				echo("Logged in successfully.");
			} else {
				echo("Failed to log in.");
			}
			exit();
		} else if ($_POST["t"] === "logout") {
			// User has attempted to logout
			logout($id, $session, $conn);
			echo("Successfully logged out.");
			exit();
		}

		mysqli_close($conn);
	}


?>
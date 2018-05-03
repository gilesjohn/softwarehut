<?php
	// Created by Giles Holdsworth, Matthew Williams

	require (__DIR__ . "/../login/user_system.php");
	block_user_below($id, $session, $ADMINISTATOR_AUTH_LEVEL);


	function add_user($username, $password, $authorisation) {
		global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table, $id;

		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error($conn));
		}

		if ($authorisation > get_user_level($id, $conn)) {
			die("Can't create user with higher authorisation level than your own.");
		}


		$username = mysqli_real_escape_string($conn, $username);
		$password = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));
		$authorisation = mysqli_real_escape_string($conn, $authorisation);
		$query = "INSERT INTO `$DB_user_table` (username, password, authorisation, session) VALUES ('$username', '$password', '$authorisation', NULL)";
		if (mysqli_query($conn, $query)) {
			echo("User created successfully.");
		} else {
			die("Could not create user.");
		}

		mysqli_close($conn);
	}


	if (!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["authorisation"])) {
		add_user(htmlspecialchars($_POST["username"]), htmlspecialchars($_POST["password"]), intval($_POST["authorisation"]));
	}


?>

<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="../css/all.css">
		<link type="text/css" rel="stylesheet" href="../css/user_admin.css">
	</head>

	<body>
		<a class="button low-pad-button" href="../index.php">Home</a><br>
		<div class="input-container">
			<h3>Create User</h3>
			<form action="" method="post">
				<p>Username:</p> <input type="text" name="username"><br>
				<p>Password:</p> <input type="password" name="password"><br>
				<p>Authorisation level:</p> <input type="text" name="authorisation"><br>
				<input type="submit">
			</form>
		</div>
	</body>
</html>
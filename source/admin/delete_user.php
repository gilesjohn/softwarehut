<?php
	// Created by Giles Holdsworth, Matthew Williams

	require (__DIR__ . "/../login/user_system.php");
	block_user_below($id, $session, $ADMINISTATOR_AUTH_LEVEL);

	function delete_user($username) {
		global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table, $id, $session, $ADMINISTATOR_AUTH_LEVEL;

		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error());
		}

		if (is_logged_in($id, $session, $conn) && get_user_level($id, $conn) === $ADMINISTATOR_AUTH_LEVEL) {
			$username = mysqli_real_escape_string($conn, $username);
			$query = "DELETE FROM `$DB_user_table` WHERE username = '$username'";
			if (mysqli_query($conn, $query)) {
				echo("Deleted user successfully.");
			} else {
				die("Couldn't delete user.");
			}
		} else {
			die("You are not logged in as an administrator.");
		}
		mysqli_close($conn);
	}

	if (!empty($_POST["username"])) {
		delete_user(htmlspecialchars($_POST["username"]));
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
			<h3>Delete User</h3>
			<form method="post">
				<p>Username:</p> <input type="text" name="username"><br>
				<input type="submit">
			</form>
		</div>
	</body>
</html>
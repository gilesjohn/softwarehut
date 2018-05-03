<?php
	// Created by Giles Holdsworth, Matthew Williams

	require (__DIR__ . "/../login/user_system.php");
	block_user_below($id, $session, $ADMINISTATOR_AUTH_LEVEL);

	// Any user
	function change_my_password($new_password) {
		global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table, $id, $session;

		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error());
		}

		if (is_logged_in($id, $session, $conn)) {
			$new_password = mysqli_real_escape_string($conn, password_hash($new_password, PASSWORD_DEFAULT));
			$id = mysqli_real_escape_string($conn, $id);
			$query = "UPDATE `$DB_user_table` SET password = '$new_password' WHERE username = '$id'";
			if (mysqli_query($conn, $query)) {
				echo("Password changed successfully.");
			} else {
				die("Couldn't change password.");
			}
		} else {
			die("You are not logged in.");
		}
		mysqli_close($conn);
	}

	// Only admins
	function change_other_password($username, $new_password) {
		global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table, $id, $session, $ADMINISTATOR_AUTH_LEVEL;

		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error());
		}

		if (is_logged_in($id, $session, $conn) && get_user_level($id, $conn) === $ADMINISTATOR_AUTH_LEVEL) {
			$new_password = mysqli_real_escape_string($conn, password_hash($new_password, PASSWORD_DEFAULT));
			$username = mysqli_real_escape_string($conn, $username);
			$query = "UPDATE `$DB_user_table` SET password = '$new_password' WHERE username = '$username'";
			if (mysqli_query($conn, $query)) {
				echo("Password changed successfully.");
			} else {
				die("Couldn't change password.");
			}
		} else {
			die("You are not logged in as an administrator.");
		}
		mysqli_close($conn);
	}

	if (!empty($_POST["username"]) && !empty($_POST["new_password"])) {
		change_other_password(htmlspecialchars($_POST["username"]), htmlspecialchars($_POST["new_password"]));
	} else if (!empty($_POST["new_password"])) {
		change_my_password(htmlspecialchars($_POST["new_password"]));
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
			<h3>Change my password:</h3>
			<form method="post">
				<p>New password:</p> <input type="password" name="new_password"> <br>
				<input type="submit">
			</form>
		</div>
		
		<?php
			global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table, $id, $session;

			$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
			if(!$conn){
				die('Could not connect: ' . mysqli_error());
			}
		
			if (get_user_level($id, $conn) === $ADMINISTATOR_AUTH_LEVEL) {
				echo('<br><div class="input-container"><h3>Change user\'s password:</h3><form method="post"> Username: <input type="text" name="username"><br>Password: <input type="password" name="username"><br><input type="submit"></form></div>');
			}
		?>
	</body>
</html>
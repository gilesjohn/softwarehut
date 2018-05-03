<?php
	// Created by Giles Holdsworth

	/*
		$id - CAN ONLY BE USED SAFELY IN PHP FILES THAT HAVE RUN block_user_below FUNCTION
		Must check following variables exist before using them
		VARIABLES BELOW MUST BE SQL ESCAPED BEFORE USE IN A QUERY - https://www.w3schools.com/php/func_mysqli_real_escape_string.asp
		mysqli_real_escape_string($con, $var_to_be_escaped);

		$username - form submitted username
		$password - form submitted password

		$id - primary key in user table, used for looking up user details (could be username if )
		$session - session key, used for checking if user is logged in as $id


		// How to store password
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		// How to check password
		if (password_verify($password, $hashed_password))


		Default auth level is -1 (should have no privilleges at all, means an error happened)

		user table:
		username - varchar(32), primary
		password - varchar(255), NOT NULL
		authoristaion - int, NOT NULL
		session - varchar(32), can be NULL

	*/
	require (__DIR__ . "/../config.php");
	$DB_user_table = "users";

	// Variables
	$username = htmlspecialchars($_POST["username"]);
	$password = htmlspecialchars($_POST["password"]);
	$id = htmlspecialchars($_COOKIE["id"]);
	$session = htmlspecialchars($_COOKIE["session"]);
	$auth_level = 0;



	// Check if user is logged in as $id
	// Returns boolean whether user is logged in
	function is_logged_in($id, $session, $conn) {
		global $DB_user_table;
		if (isset($id, $session)) {
			// check if session in database on row labelled $id matches $session variable
			$id = mysqli_real_escape_string($conn, $id);
			$query = "SELECT session FROM `$DB_user_table` WHERE username = '$id' LIMIT 1";
			$result = mysqli_query($conn, $query);
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$db_session = $row["session"];
				if (!empty($db_session) && $db_session === $session) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	// Find user authorisation level in database
	// Returns integer equal to user authorisation level, -1 if not found
	function get_user_level($id, $conn) {
		global $DB_user_table, $auth_level;
		$id = mysqli_real_escape_string($conn, $id);
		$query = "SELECT authorisation FROM `$DB_user_table` WHERE username = '$id' LIMIT 1";
		$result = mysqli_query($conn, $query);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			$user_level = $row["authorisation"];
			if (!empty($user_level) || $user_level === 0) {
				$auth_level = intval($user_level);
				return intval($user_level);
			}
		}
		return -1;
	}

	// Block access to html if user has an authorisation level below $level_number
	function block_user_below($id, $session, $level_number) {
		global $DB_server_name, $DB_username, $DB_password, $DB_name, $DB_user_table;
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error($conn));
		}
		if (!is_logged_in($id, $session, $conn) || get_user_level($id, $conn) < $level_number) {
			header("location: " . $URL . $BASE_URL_PATH);
			echo('<meta http-equiv="refresh" content="0;url=' . $BASE_URL_PATH . 'login.php">Access Denied');
			exit();
		}
		mysqli_close($conn);
	}

	// Attempt to login with $username and $password credentials
	// Returns TRUE if success and sets $id and $session, FALSE if fail
	function attempt_login($username, $password, $conn) {
		global $DB_user_table;
		if (!empty($username) && !empty($password)) {
			$username = mysqli_real_escape_string($conn, $username);
			$password = mysqli_real_escape_string($conn, $password);

			$query = "SELECT password FROM `$DB_user_table` WHERE username = '$username' LIMIT 1";
			$result = mysqli_query($conn, $query);
			if ($result === FALSE) {
				die("Error: " . $query . "" . mysqli_error($conn));
			}
			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				$hashed_password = $row["password"];
				if (empty($hashed_password)) {
					echo("EMPTY PWD<br>");
					return FALSE;
				}
			} else {
				echo("USER DOESN'T EXIST<br>");
				return FALSE;
			}


			if (password_verify($password, $hashed_password)) {
				$session = bin2hex(openssl_random_pseudo_bytes(16)); // Generate 32 character unique id
				$query = "UPDATE `$DB_user_table` SET session = '$session' WHERE username = '$username'";
				if (!mysqli_query($conn, $query)) {
				   die("Error: " . $query . "" . mysqli_error($conn));
				}
				setcookie("session", $session, time() + (86400 * 30), "/"); // Set session cookie for 30 days
				$id = $username;
				setcookie("id", $id, time() + (86400 * 30), "/"); // Set id cookie for 30 days
				return TRUE;
			} else {
				echo("INCORRECT PASSWORD<br>");
				return FALSE;
			}

		} else {
			echo("NO USERNAME/PWD<br>");
			return FALSE;
		}
	}

	// Unset login cookies and clear session column for row
	function logout($id, $session, $conn) {
		global $DB_user_table;
		if (is_logged_in($id, $session, $conn)) {
			$id = mysqli_real_escape_string($conn, $id);
			$query = "UPDATE `$DB_user_table` SET session = NULL WHERE username = '$id'";
			if (!mysqli_query($conn, $query)) {
			   die("Error: " . $query . "" . mysqli_error($conn));
			}
			setcookie("session", "", 1);
			setcookie("id", "", 1);
		}

	}
?>
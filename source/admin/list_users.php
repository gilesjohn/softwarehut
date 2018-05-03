<?php
	// Created by Giles Holdsworth, Matthew Williams

	require (__DIR__ . "/../login/user_system.php");
	block_user_below($id, $session, $ADMINISTATOR_AUTH_LEVEL);
	
	$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
	if(!$conn){
		die('Could not connect: ' . mysqli_error($conn));
	}

	$query = "SELECT username, authorisation FROM users";

	$result = mysqli_query($conn, $query);
	if ($result !== false) {
		
		if (mysqli_num_rows($result) > 0) {
			echo "
			<!DOCTYPE html>
			<html>
			<head>
				<link type=\"text/css\" rel=\"stylesheet\" href=\"../css/all.css\">
				<link type=\"text/css\" rel=\"stylesheet\" href=\"../css/user_admin.css\">
				<style>
					body {
					text-align: center;
					}
					table {
						border-collapse: collapse;
						width: 100%;
					}
					td, th {
						text-align:center;
						padding: 8px;
						border-bottom: 1px solid #ddd;
					}
					tr:hover {background-color:#f5f5f5;}
				</style>
			<body>
			<a class=\"button low-pad-button\" href=\"../index.php\">Home</a>
			
			<h1> List of all users in the database </h1>
            <table>
            	<tr>
            		<th>Username</th>
            		<th>Authorisation Level</th>
            	</tr>";
			for ($i = 0; $i < mysqli_num_rows($result); $i++) {
				$row = mysqli_fetch_assoc($result);
				echo "<tr>";
				echo "<td>" . $row['username'] . "</td>";
				echo "<td>" . $row['authorisation'] . "</td>";
				echo "</tr>";
			}
			echo "
            </table>
			</body>
			</html>";
		} else {
			die('No Form Found: ' . mysqli_error($conn));
		}
	} else {
		// There was an error with the SQL statement
		echo $query;
		die('Unable to get a result: ' . mysqli_error($conn));
	}

	


	mysqli_close($conn);
?>
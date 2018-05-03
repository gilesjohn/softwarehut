<?php
	// Created by Giles Holdsworth

	/* Script called by js function in calendar.js, this returns VisitStartDate and VisitEndDate for all visits between $_GET['start_date'] and $_GET['end_date'] with each visit separated by '.' and each date separated by ','
	Dates are in ISO date format
	FORMAT:
	start_date_1,end_date_1.start_date_2,end_date_2.start_date_3,end_date_3
	EXAMPLE
	start_date = 2018-04-01
	end_date = 2018-04-30
	2 visits found: 2nd to 8th of april, and 6th to 12th of april:
	"2018-04-02,2018-04-08.2018-04-06,2018-04-12"
	*/

	require (__DIR__ . "/login/user_system.php");
	block_user_below($id, $session, 1);

	if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
		
		// Make connection to database using details defined in user_system.php
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			// Connection could not be made
			die('Could not connect: ' . mysqli_error($conn));
		}
		// Escape date strings to prevent SQL injection
		$start_date = mysqli_real_escape_string($conn, $_GET['start_date']);
		$end_date = mysqli_real_escape_string($conn, $_GET['end_date']);
		if ($auth_level >= 2) {
			// User is privelledged so they can see anyones visits
			$query = "SELECT VisitStartDate, VisitEndDate FROM visits WHERE VisitStartDate BETWEEN '$start_date' AND '$end_date'";
		} else {
			// If user is non privelledged then they can only see their own submitted forms
			// Escape username from user_system.php to prevent SQL injection
			$escaped_id = mysqli_real_escape_string($conn, $id);
			$query = "SELECT VisitStartDate, VisitEndDate FROM visits WHERE username = '$escaped_id' AND VisitStartDate BETWEEN '$start_date' AND '$end_date'";
		}
		// Execute $query statement
		$result = mysqli_query($conn, $query);
		if ($result !== false) {
			// Number of results from statement
			$results = mysqli_num_rows($result);
			for ($i = 0; $i < $results; $i++) {
				// For each result echo in formatted way described in top comment
				$row = mysqli_fetch_assoc($result);
				echo $row['VisitStartDate'] . "," . $row["VisitEndDate"];
				if ($i < $results - 1) {
					echo ".";
				}
			}
		}
	}


?>
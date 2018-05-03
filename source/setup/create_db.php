<?php
	// Created by Giles Holdsworth

	if (!empty($_POST["setup_key"]) && $_POST["setup_key"] == "gc3thI7PuUNY1l3p") {
		// The user has entered a setup key and it is correct
		require (__DIR__ . "/../login/user_system.php");
		$query = "CREATE DATABASE `$DB_name`;";
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password);
		if(!$conn){
			error_log('Could not connect: ' . mysqli_error($conn));
			http_response_code(500);
			exit();
		}

		if (!mysqli_query($conn, $query)) {
			error_log("Error: " . $query . " " . mysqli_error($conn));
			http_response_code(500);
			exit();
		}

		$query = "USE `$DB_name`;";

		if (!mysqli_query($conn, $query)) {
			error_log("Error: " . $query . " " . mysqli_error($conn));
			http_response_code(500);
			exit();
		} else {
			// Successful creationg of database
			echo "Database setup correctly.";
		}

		// Create users
		$query = "CREATE TABLE `users` (
		  `username` varchar(32) PRIMARY KEY,
		  `password` varchar(255) NOT NULL,
		  `authorisation` int(11) NOT NULL,
		  `session` varchar(32) DEFAULT NULL,
		  `role` varchar(32) DEFAULT NULL
		);";

		if (!mysqli_query($conn, $query)) {
			error_log("Error: " . $query . " " . mysqli_error($conn));
			http_response_code(500);
			exit();
		}


		// Create admin account
		$query = "INSERT INTO `users` (`username`, `password`, `authorisation`, `session`, `role`) VALUES
		('admin', '$2y$10$2uwsqugngtY/YtYLCWNuA.5rHQWgLM24sivqOUMnLqS0BJw8a52jq', 3, '2119aa60f55ab8c3055dcc44f4bd6b02', 'administrator');";

		if (!mysqli_query($conn, $query)) {
			error_log("Error: " . $query . " " . mysqli_error($conn));
			http_response_code(500);
			exit();
		} else {
			// Successful creationg of and users
			echo "<br>users table setup correctly.";
		}
		
		// Create visits
		$query = "CREATE TABLE visits (
			VisitID INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
			username varchar(32) NOT NULL,
			HostingSchool TEXT,
			HostingAcademic TEXT,
			VisitorName TEXT,
			HomeContactDetails TEXT,
			CvCopyLocation TEXT,
			VisitStartDate DATE,
			VisitEndDate DATE,
			VisitorUndergraduateExperience TEXT,
			VisitorPhdExperience TEXT,
			TypeOfVisitorAcademic TEXT,
			TypeOfVisitorOther TEXT,
			HomeInstitutionName TEXT,
			HomeInstitutionPosition TEXT,
			RegistrationTypeStaff TEXT,
			RegistrationTypeHR TEXT,
			RegistrationTypeStudent TEXT,
			IPRIssues TEXT,
			VisitActivitySummary TEXT,
			RoomAllocation TEXT,
			ComputingFacilities TEXT,
			EmailLibraryAccess TEXT,
			FinancialDetails TEXT,
			HoSApproved TEXT,
			RecordDateHR DATE,
			RecordDateAcademicRegistry DATE,
			FOREIGN KEY (username)
			REFERENCES users(username)
			ON UPDATE CASCADE ON DELETE CASCADE
		);";
		if (!mysqli_query($conn, $query)) {
			error_log('Error: ' . $query . ' ' . mysqli_error($conn));
			http_response_code(500);
			exit();
		}

		// Add fulltext indices to allow MATCH AGAINST searching
		$query = "ALTER TABLE `visits` ADD FULLTEXT KEY `HostingAcademic` (`HostingAcademic`);
		ALTER TABLE `visits` ADD FULLTEXT KEY `HostingAcademic_2` (`HostingAcademic`,`VisitorName`);
		ALTER TABLE `visits` ADD FULLTEXT KEY `VisitorName` (`VisitorName`);";
		if (!mysqli_multi_query($conn, $query)) {
			error_log('Error: ' . $query . ' ' . mysqli_error($conn));
			http_response_code(500);
			exit();
		} else {
			// Successful creation of visits table
			echo "<br>visits table set up correctly.";
		}

		mysqli_close($conn);
	} else {
		echo "<form method=\"post\">Setup Key: <input type=\"text\" name=\"setup_key\"><br><input type=\"submit\"></form>";
	}


?>
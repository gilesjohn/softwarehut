<?php
	// Created by Giles Holdsworth, Edgaras Juodele
	//Only allow logged in users to see below page
	require (__DIR__ . "/login/user_system.php");
	block_user_below($id, $session, 1);

	$conn;
	// Convert $field in to format ready to add to MySQL query to add it to the visits database
	function mysql_convert($field, $is_date = false) {
		global $conn;
		if (isset($field) && $field != '*split*' && !empty($field)) {
			// Escape string to prevent SQL injection
			$field = mysqli_real_escape_string($conn, $field);
			if ($is_date) {
				return "STR_TO_DATE('$field', '%Y-%m-%d')";
			} else {
				return "'$field'";
			}
		} else {
			return 'NULL';
		}
	}
	/*
	//Currently unecessary function
	function convert_back($field) {
		if ($field == 'NULL') {
			return '';
		} else {
			return explode('\'', $field)[1];
		}
		// (substr($field, 0, strlen('STR_TO_DATE(\'')) === 'STR_TO_DATE(\'')
	}*/
	// Change unset/null variables to empty strings
	function null_to_empty($null_var) {
		if (isset($null_var)) {
			return $null_var;
		} else {
			return '';
		}
	}

	// is array full of 'NULL' or empty strings
	function is_array_empty($array_var) {
		foreach ($array_var as $value) {
			if ($value != 'NULL' || $value == '') {
				return false;
			}
		}
		return true;
	}

	/*** visits table columns
	VisitID INT UNSIGNED ZEROFILL NOT NULL PRIMARY KEY AUTO_INCREMENT,
	username VARCHAR(32) NOT NULL,
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
	RecordDateAcademicRegistry DATE
	*/

	// SPLIT FIELDS
	//RegistrationTypeStudent1 & RegistrationTypeStudent2
	//IPRIssuesNo & IPRIssuesYes

	// If there is an error put old values back in to input fields so progress isnt lost
	$insert_fields = true;

	// Create connection to database using details defined in user_system.php
	$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
	if(!$conn){
		die('Could not connect: ' . mysqli_error());
	}

	// Create an array of values to add to database
	$fields = array();
	array_push($fields, 'NULL');
	$escaped_id = mysqli_real_escape_string($conn, $id);
	array_push($fields, "'$escaped_id'");
	array_push($fields, mysql_convert($_POST['HostingSchool']));
	array_push($fields, mysql_convert($_POST['HostingAcademic']));
	array_push($fields, mysql_convert($_POST['VisitorName']));
	array_push($fields, mysql_convert($_POST['HomeContactDetails']));
	array_push($fields, mysql_convert($_POST['CvCopyLocation']));
	array_push($fields, mysql_convert($_POST['VisitStartDate'], true));
	array_push($fields, mysql_convert($_POST['VisitEndDate'], true));
	array_push($fields, mysql_convert($_POST['VisitorUndergraduateExperience']));
	array_push($fields, mysql_convert($_POST['VisitorPhdExperience']));
	array_push($fields, mysql_convert($_POST['TypeOfVisitorAcademic']));
	array_push($fields, mysql_convert($_POST['TypeOfVisitorOther']));
	array_push($fields, mysql_convert($_POST['HomeInstitutionName']));
	array_push($fields, mysql_convert($_POST['HomeInstitutionPosition']));
	array_push($fields, mysql_convert($_POST['RegistrationTypeStaff']));
	array_push($fields, mysql_convert($_POST['RegistrationTypeHR']));
	array_push($fields, mysql_convert($_POST['RegistrationTypeStudent1'] . '*split*' . $_POST['RegistrationTypeStudent2']));
	array_push($fields, mysql_convert($_POST['IPRIssuesYes'] . '*split*' . $_POST['IPRIssuesNo']));
	array_push($fields, mysql_convert($_POST['VisitActivitySummary']));
	array_push($fields, mysql_convert($_POST['RoomAllocation']));
	array_push($fields, mysql_convert($_POST['ComputingFacilities']));
	array_push($fields, mysql_convert($_POST['EmailLibraryAccess']));
	array_push($fields, mysql_convert($_POST['FinancialDetails']));
	array_push($fields, mysql_convert($_POST['HoSApproved']));
	array_push($fields, mysql_convert($_POST['RecordDateHR'], true));
	array_push($fields, mysql_convert($_POST['RecordDateAcademicRegistry'], true));

	// If there is at least 1 filled out field create new record in the database
	if (!is_array_empty($fields)) {
		$query = 'INSERT INTO visits VALUES (';
		// Format submitted fields in to query
		for ($i = 0; $i < count($fields); ++$i) {
			$query .= $fields[$i];
			if ($i < count($fields) - 1) {
				$query .= ',';
			}
		}
		$query .= ');';
		if (!mysqli_query($conn, $query)) {
			// Error with statement or running it with connection
			die("Error: " . $query . "" . mysqli_error($conn));
		} else {
			// If successful no need to put old values back in to input fields
			$insert_fields = false;
		}

		mysqli_close($conn);
	}




?>

<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="css/all.css">
		<link type="text/css" rel="stylesheet" href="css/forms.css">
	
	</head>
	<body>
		<div id="form-options-padding"></div>
		<form id="submit-form" method="post">
			<table>
				<tr>
					<th colspan="6"><h3>Visitor Record</h3></th>
				</tr>
				<tr>
					<th>Hosting School:</th>
					<td colspan="2"><textarea name="HostingSchool" tabindex="1"><?php
							if ($insert_fields) {
								// If there was an error with execution fill fields with submitted values so user doesnt lose progress
								echo null_to_empty($_POST['HostingSchool']);
							}
						?></textarea></td>
					<th rowspan="4">Details of Financial Implications <br>and Means of Funding</th>
					<td rowspan="4" colspan="2"><textarea name="FinancialDetails" tabindex="5"><?php
							if ($insert_fields) {
								// If there was an error with execution fill fields with submitted values so user doesnt lose progress
								echo null_to_empty($_POST['FinancialDetails']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>Hosting Academic:</th>
					<td colspan="2"><textarea name="HostingAcademic" tabindex="2"><?php
							if ($insert_fields) {
								// If there was an error with execution fill fields with submitted values so user doesnt lose progress
								echo null_to_empty($_POST['HostingAcademic']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>Name of Visitor:</th>
					<td colspan="2"><textarea name="VisitorName" tabindex="3"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['VisitorName']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>Home Contact Details:</th>
					<td colspan="2"><textarea name="HomeContactDetails" tabindex="4"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['HomeContactDetails']);
							}
						?></textarea></td>
				</tr>

				<tr>
					<th>Attach Copy of CV (Electronic):</th>
					<td colspan="2"><textarea name="CvCopyLocation"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['CvCopyLocation']);
							}
						?></textarea></td>
					<th>Approved by the HoS</th>
					<td colspan="2"><textarea name="HoSApproved"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['HoSApproved']);
							}
						?></textarea></td>
				</tr>

				<tr>
					<th>Date of Visit</th>
					<th>Start:</th>
					<td><input type="date" name="VisitStartDate" value="<?php
							if ($insert_fields) {
								echo null_to_empty($_POST['VisitStartDate']);
							}
						?>"></td>
					<th>End:</th>
					<td colspan="2"><input type="date" name="VisitEndDate" value="<?php
							if ($insert_fields) {
								echo null_to_empty($_POST['VisitEndDate']);
							}
						?>"></td>
				</tr>

				<tr>
					<th rowspan="2">Type of Visitor</th>
					<th>Undergraduate Experience:</th>
					<td><textarea name="VisitorUndergraduateExperience"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['VisitorUndergraduateExperience']);
							}
						?></textarea></td>
					<th>PhD Experience:</th>
					<td colspan="2"><textarea name="VisitorPhdExperience"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['VisitorPhDExperience']);
							}
						?></textarea></td>

				</tr>
				<tr>
					<th>Visiting Academic:</th>
					<td><textarea name="TypeOfVisitorAcademic"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['TypeOfVisitorAcademic']);
							}
						?></textarea></td>
					<th>Other (Please Specify):</th>
					<td colspan="2"><textarea name="TypeOfVisitorOther"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['TypeOfVisitorOther']);
							}
						?></textarea></td>

				</tr>
				<tr>
					<th rowspan="2">Home Institution &amp; Position</th>
					<td rowspan="2" colspan="2"><textarea name="HomeInstitutionName"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['HomeInstitutionName']);
							}
						?></textarea></td>
					<td rowspan="2" colspan="3"><textarea name="HomeInstitutionPosition"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['HomeInstitutionPosition']);
							}
						?></textarea></td>

				</tr>
				<tr></tr>
				<tr>
					<th rowspan="2">Registration Type</th>
					<th>Staff (Notify HR):</th>
					<td colspan="2"><textarea name="RegistrationTypeStaff"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RegistrationTypeStaff']);
							}
						?></textarea></td>
					<th>HR (Check availability):</th>
					<td colspan="1"><textarea name="RegistrationTypeHR"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RegistrationTypeHR']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th colspan="2">Non graduating student (notify student records - waive fees):</th>
					<td colspan="1"><textarea name="RegistrationTypeStudent1"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RegistrationTypeStudent1']);
							}
						?></textarea></td>
					<td colspan="2"><textarea name="RegistrationTypeStudent2"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RegistrationTypeStudent2']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>IPR Issues</th>
					<th>Yes</th>
					<td colspan="2"><textarea placeholder="If YES complete form and attach the signed Deed" name="IPRIssuesYes"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['IPRIssuesYes']);
							}
						?></textarea></td>
					<th>No</th>
					<td colspan="1"><textarea placeholder="" name="IPRIssuesNo"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['IPRIssuesNo']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th rowspan="2">Brief Summary of Visit Activity:</th>
					<td rowspan="2" colspan="5"><textarea name="VisitActivitySummary"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['VisitActivitySummary']);
							}
						?></textarea></td>
				</tr>
				<tr></tr>
				<tr>
					<th>Record Date of Copy Sent To... (As Applicable)</th>
					<td colspan="2">HR: <input type="date" name="RecordDateHR" value="<?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RecordDateHR']);
							}
						?>"></td>
					<td colspan="3">Academic Registry: <input type="date" name="RecordDateAcademicRegistry" value="<?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RecordDateAcademicRegistry']);
							}
						?>"></td>
				</tr>
				<tr>
					<th colspan="6">Ensure that the School or College H&amp;S Induction Briefing is completed by the hosting academic and lodged with the School Office</th>
				</tr>
				<tr>
					<th>Room Allocation:</th>
					<td><textarea name="RoomAllocation"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['RoomAllocation']);
							}
						?></textarea></td>
					<th>Computing Facilities:</th>
					<td><textarea name="ComputingFacilities"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['ComputingFacilities']);
							}
						?></textarea></td>
					<th>Email/Library Access:</th>
					<td><textarea name="EmailLibraryAccess"><?php
							if ($insert_fields) {
								echo null_to_empty($_POST['EmailLibraryAccess']);
							}
						?></textarea></td>
				</tr>

			</table>

			<div id="form-options">
				<input type="submit" class="button right-buttons green-button">
				<a class="button left-buttons" href="index.php">Home</a>
				<a class="button left-buttons" href="search_form.php">Find an Existing Form</a>
				<!--<input type="reset" class="button right-buttons">-->
			</div>

		</form>
	</body>
</html>
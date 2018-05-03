<?php
	// Created by Giles Holdsworth, Edgaras Juodele, Daniel Fairhead
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


	require (__DIR__ . "/login/user_system.php");
	block_user_below($id, $session, 1);

	// Convert a null variable to an empty string
	function null_to_empty($null_var) {
		if (isset($null_var)) {
			return $null_var;
		} else {
			return '';
		}
	}
	
	
	if (!empty($_GET['visit_id'])) {
		// Create connection to database using details defined in user_system.php
		$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
		if(!$conn){
			die('Could not connect: ' . mysqli_error($conn));
		}
		
		if (!empty($_POST['new_values'])) {
			$escaped_visit_id = mysqli_real_escape_string($conn, $_GET['visit_id']);
			$query = "UPDATE visits SET ";
			$escaped_id = mysqli_real_escape_string($conn, $id);
			$query .= "username = '$escaped_id', ";
			$query .= "HostingSchool = " . mysql_convert($_POST['HostingSchool']) . ", ";
			$query .= "HostingAcademic = " . mysql_convert($_POST['HostingAcademic']) . ", ";
			$query .= "VisitorName = " . mysql_convert($_POST['VisitorName']) . ", ";
			$query .= "HomeContactDetails = " . mysql_convert($_POST['HomeContactDetails']) . ", ";
			$query .= "CvCopyLocation = " . mysql_convert($_POST['CvCopyLocation']) . ", ";
			$query .= "VisitStartDate = " . mysql_convert($_POST['VisitStartDate']) . ", ";
			$query .= "VisitEndDate = " . mysql_convert($_POST['VisitEndDate']) . ", ";
			$query .= "VisitorUndergraduateExperience = " . mysql_convert($_POST['VisitorUndergraduateExperience']) . ", ";
			$query .= "VisitorPhdExperience = " . mysql_convert($_POST['VisitorPhdExperience']) . ", ";
			$query .= "TypeOfVisitorAcademic = " . mysql_convert($_POST['TypeOfVisitorAcademic']) . ", ";
			$query .= "TypeOfVisitorOther = " . mysql_convert($_POST['TypeOfVisitorOther']) . ", ";
			$query .= "HomeInstitutionName = " . mysql_convert($_POST['HomeInstitutionName']) . ", ";
			$query .= "HomeInstitutionPosition = " . mysql_convert($_POST['HomeInstitutionPosition']) . ", ";
			$query .= "RegistrationTypeStaff = " . mysql_convert($_POST['RegistrationTypeStaff']) . ", ";
			$query .= "RegistrationTypeHR = " . mysql_convert($_POST['RegistrationTypeHR']) . ", ";
			$query .= "RegistrationTypeStudent = " . mysql_convert($_POST['RegistrationTypeStudent1'] . '*split*' . $_POST['RegistrationTypeStudent2']) . ", ";
			$query .= "IPRIssues = " . mysql_convert($_POST['IPRIssuesYes'] . '*split*' . $_POST['IPRIssuesNo']) . ", ";
			$query .= "VisitActivitySummary = " . mysql_convert($_POST['VisitActivitySummary']) . ", ";
			$query .= "RoomAllocation = " . mysql_convert($_POST['RoomAllocation']) . ", ";
			$query .= "ComputingFacilities = " . mysql_convert($_POST['ComputingFacilities']) . ", ";
			$query .= "EmailLibraryAccess = " . mysql_convert($_POST['EmailLibraryAccess']) . ", ";
			$query .= "FinancialDetails = " . mysql_convert($_POST['FinancialDetails']) . ", ";
			$query .= "HoSApproved = " . mysql_convert($_POST['HoSApproved']) . ", ";
			$query .= "RecordDateHR = " . mysql_convert($_POST['RecordDateHR']) . ", ";
			$query .= "RecordDateAcademicRegistry = " . mysql_convert($_POST['RecordDateAcademicRegistry']) . "";
			$query .= " WHERE VisitID = $escaped_visit_id";
			
			$result = mysqli_query($conn, $query);
			if ($result === false) {
				die('Unable to execute statement: ' . mysqli_error($conn));
			}
		} if (!empty($_POST['delete_record'])) {
			$escaped_visit_id = mysqli_real_escape_string($conn, $_GET['visit_id']);
			$query = "DELETE FROM visits WHERE VisitID = $escaped_visit_id";
			$result = mysqli_query($conn, $query);
			if ($result === false) {
				die('Unable to execute statement: ' . mysqli_error($conn));
			}
		}

		
		
		
		
		
		
		
		
		
		
		
		// Dont need to display values if no form is found
		$form_found = false;
		// Escape parameters to be used in query to prevent SQL injection
		$escaped_visit_id = mysqli_real_escape_string($conn, $_GET['visit_id']);
		$escaped_id = mysqli_real_escape_string($conn, $id);
		if ($auth_level >= 2) {
			// Priveleged user can see anyones visits
			$query = "SELECT * FROM visits WHERE VisitID = $escaped_visit_id";
		} else {
			// Low level user can only see their own visits
			$query = "SELECT * FROM visits WHERE username = '$escaped_id' AND VisitID = $escaped_visit_id";
		}
		$result = mysqli_query($conn, $query);
		if ($result !== false) {
			if (mysqli_num_rows($result) > 0) {
				// If there is a visit
				// There will only be one visit since we are searching by primary key which by definition is unique
				$form_found = true;
				$values = mysqli_fetch_assoc($result);
			}
		} else {
			// There was an error with the SQL statement
			echo $query;
			die('Unable to get a result: ' . mysqli_error($conn));
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
			<?php
				if ($form_found) {
					echo "<table>";
				} else {
					echo "<h3>No form found</h3><table style=\"display: none;\">";
				}
			?>
				<tr>
					<th colspan="6"><h3>Visitor Record</h3></th>
				</tr>
				<tr>
					<th>Hosting School:</th>
					<td colspan="2"><textarea name="HostingSchool" tabindex="1"><?php
							if ($form_found && array_key_exists('HostingSchool', $values)) {
								// A visit form was found and needs to be displayed
								echo null_to_empty($values['HostingSchool']);
							}
						?></textarea></td>
					<th rowspan="4">Details of Financial Implications <br>and Means of Funding</th>
					<td rowspan="4" colspan="2"><textarea name="FinancialDetails" tabindex="5"><?php
							if ($form_found && array_key_exists('FinancialDetails', $values)) {
								// A visit form was found and needs to be displayed
								echo null_to_empty($values['FinancialDetails']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>Hosting Academic:</th>
					<td colspan="2"><textarea name="HostingAcademic" tabindex="2"><?php
							if ($form_found && array_key_exists('HostingAcademic', $values)) {
								// A visit form was found and needs to be displayed
								echo null_to_empty($values['HostingAcademic']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>Name of Visitor:</th>
					<td colspan="2"><textarea name="VisitorName" tabindex="3"><?php
							if ($form_found && array_key_exists('VisitorName', $values)) {
								// A visit form was found and needs to be displayed
								echo null_to_empty($values['VisitorName']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>Home Contact Details:</th>
					<td colspan="2"><textarea name="HomeContactDetails" tabindex="4"><?php
							if ($form_found && array_key_exists('HomeContactDetails', $values)) {
								// A visit form was found and needs to be displayed
								echo null_to_empty($values['HomeContactDetails']);
							}
						?></textarea></td>
				</tr>

				<tr>
					<th>Attach Copy of CV (Electronic):</th>
					<td colspan="2"><textarea name="CvCopyLocation"><?php
							if ($form_found && array_key_exists('CvCopyLocation', $values)) {
								echo null_to_empty($values['CvCopyLocation']);
							}
						?></textarea></td>
					<th>Approved by the HoS</th>
					<td colspan="2"><textarea name="HoSApproved"><?php
							if ($form_found && array_key_exists('HoSApproved', $values)) {
								echo null_to_empty($values['HoSApproved']);
							}
						?></textarea></td>
				</tr>

				<tr>
					<th>Date of Visit</th>
					<th>Start:</th>
					<td><input type="date" name="VisitStartDate" value="<?php
							if ($form_found && array_key_exists('VisitStartDate', $values)) {
								echo null_to_empty($values['VisitStartDate']);
							}
						?>"></td>
					<th>End:</th>
					<td colspan="2"><input type="date" name="VisitEndDate" value="<?php
							if ($form_found && array_key_exists('VisitEndDate', $values)) {
								echo null_to_empty($values['VisitEndDate']);
							}
						?>"></td>
				</tr>

				<tr>
					<th rowspan="2">Type of Visitor</th>
					<th>Undergraduate Experience:</th>
					<td><textarea name="VisitorUndergraduateExperience"><?php
							if ($form_found && array_key_exists('VisitorUndergraduateExperience', $values)) {
								echo null_to_empty($values['VisitorUndergraduateExperience']);
							}
						?></textarea></td>
					<th>PhD Experience:</th>
					<td colspan="2"><textarea name="VisitorPhdExperience"><?php
							if ($form_found && array_key_exists('VisitorPhdExperience', $values)) {
								echo null_to_empty($values['VisitorPhdExperience']);
							}
						?></textarea></td>

				</tr>
				<tr>
					<th>Visiting Academic:</th>
					<td><textarea name="TypeOfVisitorAcademic"><?php
							if ($form_found && array_key_exists('TypeOfVisitorAcademic', $values)) {
								echo null_to_empty($values['TypeOfVisitorAcademic']);
							}
						?></textarea></td>
					<th>Other (Please Specify):</th>
					<td colspan="2"><textarea name="TypeOfVisitorOther"><?php
							if ($form_found && array_key_exists('TypeOfVisitorOther', $values)) {
								echo null_to_empty($values['TypeOfVisitorOther']);
							}
						?></textarea></td>

				</tr>
				<tr>
					<th rowspan="2">Home Institution &amp; Position</th>
					<td rowspan="2" colspan="2"><textarea name="HomeInstitutionName"><?php
							if ($form_found && array_key_exists('HomeInstitutionName', $values)) {
								echo null_to_empty($values['HomeInstitutionName']);
							}
						?></textarea></td>
					<td rowspan="2" colspan="3"><textarea name="HomeInstitutionPosition"><?php
							if ($form_found && array_key_exists('HomeInstitutionPosition', $values)) {
								echo null_to_empty($values['HomeInstitutionPosition']);
							}
						?></textarea></td>

				</tr>
				<tr></tr>
				<tr>
					<th rowspan="2">Registration Type</th>
					<th>Staff (Notify HR):</th>
					<td colspan="2"><textarea name="RegistrationTypeStaff"><?php
							if ($form_found && array_key_exists('RegistrationTypeStaff', $values)) {
								echo null_to_empty($values['RegistrationTypeStaff']);
							}
						?></textarea></td>
					<th>HR (Check availability):</th>
					<td colspan="1"><textarea name="RegistrationTypeHR"><?php
							if ($form_found && array_key_exists('RegistrationTypeHR', $values)) {
								echo null_to_empty($values['RegistrationTypeHR']);
							}
						?></textarea></td>
				</tr>
				<tr>
					<th colspan="2">Non graduating student (notify student records - waive fees):</th>
					<td colspan="1"><textarea name="RegistrationTypeStudent1"><?php
							if ($form_found && array_key_exists('RegistrationTypeStudent', $values) && $values['RegistrationTypeStudent'] != null) {
								echo explode('*split*',$values['RegistrationTypeStudent'])[0];
							}
						?></textarea></td>
					<td colspan="2"><textarea name="RegistrationTypeStudent2"><?php
							if ($form_found && array_key_exists('RegistrationTypeStudent', $values) && $values['RegistrationTypeStudent'] != null) {
								echo explode('*split*',$values['RegistrationTypeStudent'])[1];
							}
						?></textarea></td>
				</tr>
				<tr>
					<th>IPR Issues</th>
					<th>Yes</th>
					<td colspan="2"><textarea placeholder="If YES complete form and attach the signed Deed" name="IPRIssuesYes"><?php
							if ($form_found && array_key_exists('IPRIssues', $values) && $values['IPRIssues'] != null) {
								echo explode('*split*',$values['IPRIssues'])[0];
							}
						?></textarea></td>
					<th>No</th>
					<td colspan="1"><textarea placeholder="" name="IPRIssuesNo"><?php
							if ($form_found && array_key_exists('IPRIssues', $values) && $values['IPRIssues'] != null) {
								echo explode('*split*',$values['IPRIssues'])[1];
							}
						?></textarea></td>
				</tr>
				<tr>
					<th rowspan="2">Brief Summary of Visit Activity:</th>
					<td rowspan="2" colspan="5"><textarea name="VisitActivitySummary"><?php
							if ($form_found && array_key_exists('VisitActivitySummary', $values)) {
								echo null_to_empty($values['VisitActivitySummary']);
							}
						?></textarea></td>
				</tr>
				<tr></tr>
				<tr>
					<th>Record Date of Copy Sent To... (As Applicable)</th>
					<td colspan="2">HR: <input type="date" name="RecordDateHR" value="<?php
							if ($form_found && array_key_exists('RecordDateHR', $values)) {
								echo null_to_empty($values['RecordDateHR']);
							}
						?>"></td>
					<td colspan="3">Academic Registry: <input type="date" name="RecordDateAcademicRegistry" value="<?php
							if ($form_found && array_key_exists('RecordDateAcademicRegistry', $values)) {
								echo null_to_empty($values['RecordDateAcademicRegistry']);
							}
						?>"></td>
				</tr>
				<tr>
					<th colspan="6">Ensure that the School or College H&amp;S Induction Briefing is completed by the hosting academic and lodged with the School Office</th>
				</tr>
				<tr>
					<th>Room Allocation:</th>
					<td><textarea name="RoomAllocation"><?php
							if ($form_found && array_key_exists('RoomAllocation', $values)) {
								echo null_to_empty($values['RoomAllocation']);
							}
						?></textarea></td>
					<th>Computing Facilities:</th>
					<td><textarea name="ComputingFacilities"><?php
							if ($form_found && array_key_exists('ComputingFacilities', $values)) {
								echo null_to_empty($values['ComputingFacilities']);
							}
						?></textarea></td>
					<th>Email/Library Access:</th>
					<td><textarea name="EmailLibraryAccess"><?php
							if ($form_found && array_key_exists('EmailLibraryAccess', $values)) {
								echo null_to_empty($values['EmailLibraryAccess']);
							}
						?></textarea></td>
				</tr>

			</table>
			<input type="hidden" name="new_values" value="true">
		</form>

			<div id="form-options">
				
				<form method="post">
					<input type="hidden" name="delete_record" value="true">
					<input type="submit" class="button right-buttons red-button" value="Delete Record">
				</form>
				<button type="submit" class="button right-buttons green-button" form="submit-form" value="Submit with updated values">Submit with updated values</button>
				<a class="button left-buttons" href="index.php">Home</a>
				<a class="button left-buttons" href="search_form.php">Find a different form</a>
				
				<?php
					if ($form_found && array_key_exists('username', $values)) {
						// Visit form was found, display username of user that submitted the form initially
						echo "<span style=\"clear: both; color: white; margin-left: 40px;\">Form submitted by: " . null_to_empty($values['username']) . "</span>";
					}
				?>
				
				<!--<input type="reset" class="button right-buttons">-->
				
			</div>

		
	</body>
</html>
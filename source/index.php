<?php
	// Created by Matthew Williams, Andrew Griffin, Daniel Griffin

	require (__DIR__ . "/login/user_system.php");
	block_user_below($id, $session, 1);

	function visit_summary($row) {
		// Format row from visits table into sentence
		/* Combinations
			visitor name and hosting academic name
			visitor name and hosting academic name and dates
			visitor name and hostingschool and hosting academic name and homeinstitutionname
			visitor name and hostingschool and hosting academic name and homeinstitutionname and dates
		*/
		$summary = "";
		if (!empty($row['VisitorName']) && !empty($row['HostingAcademic'])) {
			
			if (!empty($row['HostingSchool']) && !empty($row['HomeInstitutionName'])) {
				$summary = $row['VisitorName'] . " from " . $row['HomeInstitutionName'] . " is visiting " . $row['HostingAcademic'] . " from " . $row['HostingSchool'];
			} else {
				$summary = $row['VisitorName'] . " is visiting " . $row['HostingAcademic'];
			}
			
			
			if (!empty($row['VisitStartDate'])) {
				if (!empty($row['VisitEndDate'])) {
					$summary .= " between " . $row['VisitStartDate'] . " and " . $row['VisitEndDate'];
				}
			}
		}		
		return $summary;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Visit Form | Hub </title>
		<link type="text/css" rel="stylesheet" href="css/all.css">
		<link rel="stylesheet" type="text/css" href="css/fontawesome-all.css">
		<link rel="stylesheet" type="text/css" href="css/hub.css">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="js/login.js"></script>
	</head>
	<body>

		<nav>
		</nav>

		<div id="left-box">
			<div class="profileBox">
				<p><center> <span id="username-display"></span> </center></p>
				<center><img src="img/avatar.png" width="96" height="96" class="avatar"></center>
			</div>

			<div class="menuBar">
				<ul>
					<li><a href="index.php">Hub</a></li>
					<li><a href="submit_form.php">Book a visit</a></li>
					<br><br>
					<?php
						if ($auth_level === $ADMINISTATOR_AUTH_LEVEL) {
							echo '<li><a href="admin/create_user.php">Create an account</a></li>
								<li><a href="admin/delete_user.php">Delete an account</a></li>
								<li><a href="admin/change_password.php">Change an account\'s password</a></li>
								<li><a href="admin/list_users.php">List all users</a></li>
								<br><br>';
						}
					?>
					<li onclick="logout()"><a href="#">Log Out</a></li>
				</ul>
			</div>

		</div>
		
		<div id="right-box">

			<div class="dashboardText">
				<i class="fas fa-columns"></i>
				My Dashboard
			</div><br>

			<div class="content-box" id="current-visits" onclick="location.href='search_form.php';">
				<h3> <i class="fas fa-search" style="font-size:60px; vertical-align: middle"></i> Find Visits </h3>
			</div>

			<div class="content-box" id="visit-calendar" onclick="location.href='visit_calendar.php';">
				<h3> <i class="far fa-calendar-alt" style="font-size:60px; vertical-align: middle"></i> Visit Calendar </h3>
			</div>

			<div class="content-box" id="new-form" onclick="location.href='submit_form.php';">
				<h3> <i class="fas fa-id-badge" style="font-size:60px; vertical-align: middle"></i> Submit a New Visit Form </h3>
			</div>
			
			<div class="dashboardText">
				<i class="fas fa-taxi"></i>
				My Current and Upcoming Visits
			</div>

			<div class="scrollBox">
				<?php
					// Get summary of all visits that havent ended yet belonging to this user
				
					$conn = mysqli_connect($DB_server_name, $DB_username, $DB_password, $DB_name);
					if(!$conn){
						die('Could not connect: ' . mysqli_error($conn));
					}
					$query = "SELECT * FROM visits WHERE username = '$id' AND VisitEndDate >= CURDATE() ORDER BY VisitStartDate ASC";
					$result = mysqli_query($conn, $query);
					if ($result !== false) {
						$results = mysqli_num_rows($result);
						if ($results > 0) {
							echo "<ul>";
							for ($i = 0; $i < $results; $i++) {
								$row = mysqli_fetch_assoc($result);
								echo "<li><a href=\"view_form.php?visit_id=". $row['VisitID'] . "\">" . visit_summary($row) . "</a></li>";
							}
							echo "</ul>";
						} else {
							echo "<h3>No visits to display</h3>";
						}
					} else {
						error_log("Could not access database." . mysqli_error($conn));
						echo "Could not access database.";
					}
					
				
				?>
			</div>
		</div>

	</body>
</html>

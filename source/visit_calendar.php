<?php
	// Created by Giles Holdsworth, Matthew Williams, Daniel Fairhead, Joshua Shaw

	require (__DIR__ . "/login/user_system.php");
	block_user_below($id, $session, 1);
?>
<!DOCTYPE html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="css/all.css">
		<link type="text/css" rel="stylesheet" href="css/calendar.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="js/calendar.js"></script>
	</head>
	<body>
	<div id="calendar">
		<div id="month-bar">
			<a class="button" style="position:fixed;display:block;" href="index.php">Home</a>
			<h3 id="year">2018</h3>
			<h1><a href="#" onclick="prevMonth()">&lt;</a> <span id="month-name">August</span> <a href="#" onclick="nextMonth()">&gt;</a></h1>
		</div>

		<ul id="weekdays">
			<li>Monday</li><li>
				Tuesday</li><li>
				Wednesday</li><li>
				Thursday</li><li>
				Friday</li><li>
				Saturday</li><li>
				Sunday</li>
		</ul>
		
		<ul id="days">
			<li id="fi1">
			</li><li id="fi2">
			</li><li id="fi3">
			</li><li id="fi4">
			</li><li id="fi5">
			</li><li id="fi6">
			</li><li id="li1">
			1st</li><li id="li2">
			2nd</li><li id="li3">
			3rd</li><li id="li4">
			4th</li><li id="li5">
			5th</li><li id="li6">
			6th</li><li id="li7">
			7th</li><li id="li8">
			8th</li><li id="li9">
			9th</li><li id="li10">
			10th</li><li id="li11">
			11th</li><li id="li12">
			12th</li><li id="li13">
			13th</li><li id="li14">
			14th</li><li id="li15">
			15th</li><li id="li16">
			16th</li><li id="li17">
			17th</li><li id="li18">
			18th</li><li id="li19">
			19th</li><li id="li20">
			20th</li><li id="li21">
			21st</li><li id="li22">
			22nd</li><li id="li23">
			23rd</li><li id="li24">
			24th</li><li id="li25">
			25th</li><li id="li26">
			26th</li><li id="li27">
			27th</li><li id="li28">
			28th</li><li id="li29">
			29th</li><li id="li30">
			30th</li><li id="li31">
			31st</li>
		</ul>
		
		<div id="key-container">
			<span class="key low-visits">1-3 visits</span>
			<span class="key medium-visits">4-10 visits</span>
			<span class="key high-visits">11+ visits</span>
		</div>
	</div>

</body>
</html>
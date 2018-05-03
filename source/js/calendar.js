// Created by Joshua Shaw, Daniel Fairhead, Giles Holdsworth
var year = parseInt((new Date()).getFullYear());
var month = parseInt((new Date()).getMonth());

var daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
function getDaysInMonth(monthNo) {
	
	
	if (monthNo == 1 && (((year) % 4 == 0) && ((year) % 100 != 0)) || ((year) % 400 == 0)) {
		// is a leap year
		console.log("leapyear")
		return 29;
	} else {
		return daysInMonth[monthNo];
	}
}
function getFirstDayInMonth(monthNo) {
	
	var dayNo = new Date(Date.UTC(year, month, 1)).getDay();
	if (dayNo == 0) {
		dayNo = 7;
	}
	var i;
	for (i = 1; i <= 6; i++) {
		if (i < dayNo) {
			$("#fi" + i.toString()).show();
		} else {
			$("#fi" + i.toString()).hide();
		}
		
	}

}

$(document).keydown(function(e) {
    switch(e.which) {
        case 37:
			// Left arrow key
			prevMonth();
        	break;
        case 39:
			// Right arrow key
			nextMonth();
        	break;
        default:
			return;
    }
});

function nextMonth() {
	switchMonth(month + 1);
}
function prevMonth() {
	switchMonth(month - 1);
}
function switchMonth(monthNo) {
	if (monthNo > 11) {
		monthNo = 0;
		year += 1;
		$("#year").html(year.toString());
	}
	if (monthNo < 0) {
		monthNo = 11;
		year -= 1;
		$("#year").html(year.toString());
	}
	
	
	month = monthNo;
	$("#month-name").html(monthNames[month]);
	var days = getDaysInMonth(month);
	if (days > 28) {
		$("#li29").show();
		if (days > 29) {
			$("#li30").show();
			if (days > 30) {
				$("#li31").show();
			} else {
				$("#li31").hide();
			}
		} else {
			$("#li30").hide();
			$("#li31").hide();
		}
	} else {
		$("#li29").hide();
		$("#li30").hide();
		$("#li31").hide();
	}
	
	getFirstDayInMonth(month);
	getDates();
	
}

function getDates() {
	var startDate = new Date(Date.UTC(year, month, 1)).toISOString().split("T")[0];
	var endDate = new Date(Date.UTC(year, month, getDaysInMonth(month))).toISOString().split("T")[0];
	$.ajax({
	  url: "visits_in_months.php",
	  data: "start_date=" + startDate + "&end_date=" + endDate,
	  success: fillColours,
	});
}
function fillColours(resultText) {
	if (resultText != "") {
		var dates = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
		var days = getDaysInMonth(month);
		var results = resultText.split('.');
		var x;
		for (x in results) {
			var d1 = results[x].split(",")[0];
			var d2 = results[x].split(",")[1];
			console.log(d1);
			console.log(d2);
			for (var i = 0; i < days; i++) {
				var d3 = new Date(Date.UTC(year, month, i + 1)).toISOString().split('T')[0];
				if (d3 >= d1 && d3 <= d2) {
					dates[i]++;
				}
			}
		}
		console.log(dates);
		for (var i = 0; i < days; i++) {
			if (dates[i] > 0 && dates[i] < 4) {
				$("#li" + (i + 1).toString()).addClass("low-visits");
				$("#li" + (i + 1).toString()).removeClass("medium-visits");
				$("#li" + (i + 1).toString()).removeClass("high-visits");
			} else if (dates[i] >= 4 && dates[i] < 11) {
				$("#li" + (i + 1).toString()).addClass("medium-visits");
				$("#li" + (i + 1).toString()).removeClass("low-visits");
				$("#li" + (i + 1).toString()).removeClass("high-visits");
			} else if (dates[i] >= 11) {
				$("#li" + (i + 1).toString()).addClass("high-visits");
				$("#li" + (i + 1).toString()).removeClass("low-visits");
				$("#li" + (i + 1).toString()).removeClass("medium-visits");
			} else {
				$("#li" + (i + 1).toString()).removeClass("low-visits");
				$("#li" + (i + 1).toString()).removeClass("medium-visits");
				$("#li" + (i + 1).toString()).removeClass("high-visits");
			}
		}
	} else {
		for (var i = 0; i < getDaysInMonth(month); i++) {
			$("#li" + (i + 1).toString()).removeClass("low-visits");
			$("#li" + (i + 1).toString()).removeClass("medium-visits");
			$("#li" + (i + 1).toString()).removeClass("high-visits");
		}
	}
}


$(document).click(function(event) {
	if ($(event.target).attr('id') != null) {
		if ($(event.target).attr('id').substr(0,2) === "li") {
			var clickedDay = parseInt($(event.target).attr('id').substr(2));
			window.location.href = "search_form.php?date=" + new Date(Date.UTC(year,month,clickedDay)).toISOString().split('T')[0];
		}
	}
});

$(document).ready( function () {
	year = 2016;
	month = 1;
	
	year = parseInt((new Date()).getFullYear());
	month = parseInt((new Date()).getMonth());
	switchMonth(month);
	
});
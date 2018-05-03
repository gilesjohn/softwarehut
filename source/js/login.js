// Created by Joshua Shaw, Daniel Fairhead, Giles Holdsworth
var loginPHPLoc = "login/login.php";

function enterAttemptLogin(e) {
	if (e.which == 13) {
		attemptLogin();
		return false;
	}
}

function ajaxPost(path, params, handler) {
	// Params should be like: "lorem=ipsum&name=binny"
	var req = new XMLHttpRequest();
	req.onreadystatechange = function () {
		if (req.readyState === 4 && req.status === 200) {
			handler(req.responseText);
		}
	};
	req.open("POST", path, true);
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.send(params);
}

function attemptLogin() {
	var usernameInput = document.getElementById('username-input').value;
	var passwordInput = document.getElementById('password-input').value;
	$.post({
		url: loginPHPLoc,
		data: {
			t: "login",
			username: usernameInput,
			password: passwordInput
    	},
		success: outputHandler
	});
}

function logout() {
	document.cookie = "id=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;"
	document.cookie = 'session=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;';  
	$.ajax({
		type: "POST",
		url: loginPHPLoc,
		data: {
			t: "logout"
    	},
		success: outputHandler,
	});
}

function outputHandler(responseText) {
	if (responseText == "Logged in successfully.") {
		if (document.getElementById("output-message") != null) {
			document.getElementById("output-message").innerHTML = '<p style="color:green">' + responseText + '</p>';
		}
		window.location.replace("index.php");
	} else if (responseText == "Successfully logged out.") {
		if (document.getElementById("output-message") != null) {
			document.getElementById("output-message").innerHTML = '<p style="color:green">' + responseText + '</p>';
		}
		window.location.replace("login.php");
	} else {
		console.log(responseText);
		if (document.getElementById("output-message") != null) {
				document.getElementById("output-message").innerHTML = '<p style="color:red">' + responseText + '</p>';
		}
		
	}
	displayUsername();
}

function displayUsername() {
	if (document.getElementById("username-display") == null) {
		console.log("Cant find username-display");
		return;
	}
	var username = getCookieValue("id");
	if (username === "") {
		username = "You are not logged in.";
		if (document.getElementById("darkbox") != null) {
			displayLoginBox();
		}
	} else {
		username = "You are logged in as " + username;
	}
	document.getElementById("username-display").innerHTML = username;
}

function getCookieValue(cookieName) {
	var cookies = decodeURIComponent(document.cookie);
    cookies = cookies.split(';');
	for (var i = 0; i < cookies.length; ++i) {
		var cookie = cookies[i].trim();
		if (cookie.startsWith(cookieName + "=")) {
			return cookie.substring(cookieName.length + 1);
		}
	}
	return "";
}

function displayLoginBox() {
	document.getElementById("darkbox").style.display = "block";
}
function hideLoginBox() {
	document.getElementById("darkbox").style.display = "none";
}

function toggleLoginBox() {
	var loginbox = document.getElementById("darkbox").style;
    if (loginbox.display === "none") {
        loginbox.display = "block";
    } else {
        loginbox.display = "none";
    }
}

window.onload = displayUsername;

<?php
require("config.php");
session_name($inputSession);
session_start();

if (isset($_POST["userName"])) {
	if ($_POST["userName"] == $userName && $_POST["password"] == $password) {
		$_SESSION["loggedIn"] = 1;
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		$extra = 'dashboard.php';
		header("Location: http://$host$uri/$extra");
		exit();
	} else {
		$message = "Wrong Username/Password.<br/>Who are you, and what do you want?";
	}
}
?>
<html>
<head>
<title>
Login
</title>
</head>
<body>
	<div><?= $message ?></div>
	<form action='login.php' method='post'>
		<label for='userName'>Username</label>
		<input id ='userName' name='userName' type='text'/>
		<label for='password'>Password</label>
		<input id ='password' name='password' type='password'/>
		<input type='submit' value='Submit'/>
	</form>
</body>
</html>
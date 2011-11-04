<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_name("map_input");
session_start();

if (!isset($_SESSION["loggedIn"])) {
	$_SESSION["loggedIn"] = 0;
}

if (!$_SESSION["loggedIn"]) {
	$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = 'login.php';
	header("Location: http://$host$uri/$extra");
	exit();
}
?>
<html>
	<head>
		<title>Map Dashboard</title>
	</head>
	<body>
		<h1>Map Dashboard</h1>
		<ul>
			<li><a href='count.php'>Count</a></li>
			<li><a href='input.php'>Input</a></li>
		</ul>
	</body>
</html>

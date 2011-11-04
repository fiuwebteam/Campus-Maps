<?php 
require("config.php");

session_name($inputSession);
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

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'map';
mysql_select_db($dbname);

$sql = "SELECT title, count FROM locations ";
if (isset($_GET["sort"]) && $_GET["sort"] == "name") {
	$sql .= "ORDER by title ASC, count";
} else {
	$sql .= "ORDER by count DESC, title";
}


$result = mysql_query($sql) or die(mysql_error($result));
$resultsArray = array();
while ($row = mysql_fetch_assoc($result)) { $resultsArray[] = $row; }


mysql_close($conn);
?>
<html>
	<head>
		<title>Locations Count-o-meter</title>
	</head>
	<body>
		<h1>Locations Count-o-meter</h1>
		<a href='dashboard.php'>Back to Dashboard</a>
		<table>
		<tr>
			<th><a href='count.php?sort=name'>Name</a></th>
			<th><a href='count.php?sort=count'>Count</a></th>
		</tr>
		<?php foreach($resultsArray as $row) {
			echo "<tr>";
			foreach ($row as $value) {
				echo "<td>$value</td>";
			}
			echo "</tr>";
		}?>
		</table>
	</body>
</html>

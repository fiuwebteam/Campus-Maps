<?php 
require("config.php");

$memcache = new Memcache();
$memcache->connect("localhost") or die ("Could not connect to memcache");

$memKey = "";
// o = owner
// t = type

if(!isset($_REQUEST['o']) && !isset($_REQUEST['t'])) { exit(); }

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'map';
mysql_select_db($dbname);


$sql = "";

if (isset($_REQUEST['c']) ) {
	$memKey = sha1("campusmaps_variables_count" . $_REQUEST['o'] . $_REQUEST['c'] . $_REQUEST['t']);
	$results = $memcache->get($memKey);
	if (!empty($results) ) {
		echo json_encode($results);
		exit();
	}
	$sql = "SELECT count(*) as COUNT
	FROM variables	 
	WHERE variables.type = '";
	$sql .= mysql_real_escape_string($_REQUEST['t']);
	$sql .= "' AND variables.owner = '";
	$sql .= mysql_real_escape_string($_REQUEST['o']);
	$sql .= "' ORDER BY variables.id";
	$result = mysql_query($sql) or die(mysql_error($result));
	$resultsArray = array();
	$resultsArray = mysql_fetch_assoc($result);
	$memcache->set($memKey, $resultsArray, 0, 86400);
	echo json_encode($resultsArray);
	mysql_close($conn);
} else {
	$memKey = sha1("campusmaps_variables_type" . $_REQUEST['o']. $_REQUEST['t']);
	$results = $memcache->get($memKey);
	if (!empty($results) ) {
		echo json_encode($results);
		exit();
	}
	$sql = "SELECT variables.id, variables.data
	FROM variables	 
	WHERE variables.type = '";
	$sql .= mysql_real_escape_string($_REQUEST['t']);
	$sql .= "' AND variables.owner = '";
	$sql .= mysql_real_escape_string($_REQUEST['o']);
	$sql .= "' ORDER BY variables.id";
	$result = mysql_query($sql) or die(mysql_error($result));
	$resultsArray = array();
	while ($row = mysql_fetch_assoc($result)) { $resultsArray[] = $row; }	
	$memcache->set($memKey, $resultsArray, 0, 86400);
	echo json_encode($resultsArray);
	mysql_close($conn);
}
?>
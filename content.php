<?php 
require("config.php");

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'map';
mysql_select_db($dbname);

$memcache = new Memcache();
$memcache->connect("localhost") or die ("Could not connect to memcache");
$memKey = "";
if (isset($_REQUEST['i'])) {
	$sql = "UPDATE locations SET count=count+1 WHERE id ='".mysql_real_escape_string($_REQUEST['i'])."'";	
	$result = mysql_query($sql) or die(mysql_error($result));
	$memKey = sha1("campusmaps_content" . $_REQUEST['i']);
} else if (isset($_REQUEST['m'])) {
	$memKey = sha1("campusmaps_content_m" . $_REQUEST['m']);
} else {
	mysql_close($conn);
	exit();
}

$results = $memcache->get($memKey);

if (!empty($results)) {
	echo json_encode($results);
	mysql_close($conn);
	exit();
}



// i = id
// m = multiple ids
$sql = "";
$resultsArray = array();
if (isset($_REQUEST['i'])) {
	$sql = "
	SELECT locations.id, locations.title, locations.description, locations.latitude, locations.longitude, locations.image, locations.campus, locations.count
	FROM locations 
	WHERE id = '";
	$sql .= mysql_real_escape_string($_REQUEST['i']);
	$sql .= "'";
	$result = mysql_query($sql) or die(mysql_error($result));
	$resultsArray = mysql_fetch_assoc($result);	
} else if (isset($_REQUEST['m'])) {
	$query = explode(",", $_REQUEST['m']);
	$sql = "
	SELECT locations.id, locations.title, locations.description, locations.latitude, locations.longitude, locations.image, locations.campus, locations.count
	FROM locations 
	WHERE ";
	$first = true;
	foreach($query as $value) {
		if ($first) {$first = false;}
		else {$sql .= "OR ";}
		$sql .= "id='".mysql_real_escape_string($value)."' ";
	}
	$sql .= "GROUP BY title ORDER BY title";
	$result = mysql_query($sql) or die(mysql_error($result));
	while ($row = mysql_fetch_assoc($result)) { $resultsArray[] = $row; }
}

$memcache->set($memKey, $resultsArray, 0, 86400);

echo json_encode($resultsArray);

mysql_close($conn);
?>
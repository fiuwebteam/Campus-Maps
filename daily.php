<?php 
require("config.php");

if ($_SERVER["REMOTE_ADDR"] != "127.0.0.1") { 
	echo "Error";
	return;
} else {
	echo "Running...";
}

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'map';
mysql_select_db($dbname);

$sql = "SELECT locations.title as location, tags.title as tags, changes.change_id FROM changes
LEFT JOIN (locations, tags)
ON (changes.location_id = locations.id AND changes.tag_id = tags.id )
WHERE date = '" . date("Y-m-d", strtotime("yesterday")) . "'";	
$result = mysql_query($sql) or die(mysql_error($result));

$resultsArray = array();
while ($row = mysql_fetch_assoc($result)) {	
	$resultsArray[] = $row; 
}

$output = "
These are the changes for today:

Added:
";

foreach($resultsArray as $key => $value) {
	if ($value["change_id"] == 1) {
		$output .= $value["location"] . " - " . $value["tags"] . "
";
	}
}

$output .= "
Dropped:
";

foreach($resultsArray as $key => $value) {
	if ($value["change_id"] == 0) {
		$output .= $value["location"] . " - " . $value["tags"] . "
";
	}
}

$to = 'Matthew Herzberger <mherzber@fiu.edu>, Andre Oliveira <amolive@fiu.edu>, Ash Brymer <abrymer@fiu.edu>';
$subject = 'FIU Map Changes for today.';
$headers = 'From: FIU Map <noreply@fiu.edu>' . "\r\n" .
'X-Mailer: PHP/' . phpversion();

mysql_close($conn);
mail($to, $subject, $output, $headers);

?>
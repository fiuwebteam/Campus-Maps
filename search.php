<?php 
require("config.php");

$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Error connecting to mysql');

$dbname = 'map';
mysql_select_db($dbname);

// q = query
// a = alphabetical
$sql = "";

if (isset($_REQUEST['q'])) {
	$query = $_REQUEST['q'];
	$queryExplode = explode('"', $query);
	
	$quotedStrings = array();
	$andStrings = array();
	$orString = array();
	
	foreach ($queryExplode as $key1 => $value1) {
		if ($value1 != "" && $value1 != " ") {
			$spaceExplode = explode(" ", $value1);
			foreach($spaceExplode as $key2 => $value2) { 
				if ($value2 == "") { continue; }
				if ($key1%2) { $quotedStrings[] = trim($value2); }
				else {
					if ($value2 == "OR") { continue;}
					if ($key2 != 0 && $spaceExplode[$key2-1] == "OR") { $orString[] = trim($value2); } 
					else { $andStrings[] = trim($value2); }
				}
			}
		}
	}
	
	$sql = "";
	if (!empty($quotedStrings)) {
		$sql .= "
		SELECT locations.id, locations.title
		FROM locations
		JOIN locations_tags
		  ON locations.id = locations_tags.location_id
		JOIN tags 
		  ON tags.id = locations_tags.tag_id
		WHERE tags.title IN (";
		$first = true;
		$count = 0;
		foreach($quotedStrings as $value) {
			if ($first) { $first = false; }
			else {$sql .= ", ";}
			$count++;
			$sql .= '"'.mysql_real_escape_string($value).'"';				
		}
		$sql .= ")
		GROUP BY locations.id
		HAVING COUNT(DISTINCT tags.title) >= $count ";
	}
	
	if (!empty($andStrings)) {
		if ($sql != "") { $sql .= "UNION "; }
		$sql .= "
		SELECT locations.id, locations.title
		FROM locations
		JOIN locations_tags
		  ON locations.id = locations_tags.location_id
		JOIN tags 
		  ON tags.id = locations_tags.tag_id
		WHERE tags.title REGEXP (\"";
		$first = true;
		$count = 0;
		foreach($andStrings as $value) {
			if ($first) { $first = false; }
			else {$sql .= "|";}
			$sql .= mysql_real_escape_string($value);
			$count++;				
		}
		$sql .= "\")
		GROUP BY locations.id
		HAVING COUNT(DISTINCT tags.title) >= $count ";
	}
	if (!empty($orString)) {
		if ($sql != "") { $sql .= "UNION "; }
		$sql .= "
		SELECT locations.id, locations.title
		FROM locations
		JOIN locations_tags
		  ON locations.id = locations_tags.location_id
		JOIN tags 
		  ON tags.id = locations_tags.tag_id
		WHERE tags.title IN (";
		$first = true;
		foreach($orString as $value) {
			if ($first) { $first = false; }
			else {$sql .= ", ";}
			$sql .= '"'.mysql_real_escape_string($value).'"';				
		}
		$sql .= ")
		GROUP BY locations.id";
	}
	
	$sql .= "
	UNION
	SELECT locations.id, locations.title
	FROM locations
	WHERE ";
	
	$tmp = "";
	foreach($quotedStrings as $value) { $tmp .= "$value "; }
	$tmp = trim($tmp);
	
	if ($tmp != "") { $sql .= "locations.title = '".mysql_real_escape_string($tmp)."' "; }	
	
	$first = true;
	foreach ($andStrings as $key => $value) {
		if ($value == "OR") {continue;}
		if ($first && $tmp == "") { $first = false; }
		else if ($key != 0 && $andStrings[$key-1] == "OR") {$sql .= "OR ";}
		else { $sql .= "AND "; }			
		$sql .= "locations.title LIKE '%".mysql_real_escape_string($value2)."%' ";		
	}
	
	$sql .= "ORDER BY title";
	
	$result = mysql_query($sql) or die(mysql_error($result));	
	$resultsArray = array();
	while ($row = mysql_fetch_assoc($result)) {	$resultsArray[] = $row; }	
	
} else if (isset($_REQUEST['a'])) {
	$sql = "SELECT locations.id, locations.title FROM locations WHERE title LIKE '";
	$sql .= mysql_real_escape_string(substr($_REQUEST['a'], 0, 1));
	$sql .= "%' GROUP BY title ORDER BY title";
	$result = mysql_query($sql) or die(mysql_error($result));
	$resultsArray = array();
	while ($row = mysql_fetch_assoc($result)) { $resultsArray[] = $row; }

} 

echo json_encode($resultsArray);

mysql_close($conn);
?>
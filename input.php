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


$sql = "SELECT id, title FROM locations order by title";
$result = mysql_query($sql) or die(mysql_error($result));
$resultsArray = array();
while ($row = mysql_fetch_assoc($result)) { $resultsArray[] = $row; }

if (isset($_POST["description"]) && isset($_POST["locations"])) {
	$sql = "
	UPDATE locations 
	SET 
	  description ='".mysql_real_escape_string($_POST["description"])."',
	  image = '".mysql_real_escape_string($_POST["descImage"])."'	
	WHERE id ='".mysql_real_escape_string($_POST["locations"])."'";
	$result = mysql_query($sql) or die(mysql_error($result));
	
	$sql = "DELETE FROM variables WHERE owner='".mysql_real_escape_string($_POST["locations"])."'";
	$result = mysql_query($sql) or die(mysql_error($result));
	
	$sql = "INSERT INTO variables (type, owner, data) VALUES ";
	
	$first = true;
	foreach ($_POST["image"] as $value) {
		if ($value != "") {
			if (!$first) { $sql .= ","; } 
			else { $first = false; }
			$sql .= "(1, '".mysql_real_escape_string($_POST["locations"])."', '".mysql_real_escape_string($value)."')";			
		}
	}
	
	foreach ($_POST["video"] as $value) {
		if ($value != "") {
			if (!$first) { $sql .= ","; } 
			else { $first = false; }
			$sql .= "(2, '".mysql_real_escape_string($_POST["locations"])."', '".mysql_real_escape_string($value)."')";
		}
	}
	
	if (!$first) {
		$result = mysql_query($sql) or die(mysql_error($result));
	}
	
	$sql = "
	UPDATE locations_tags 
	SET permanency = 0 
	WHERE location_id='".mysql_real_escape_string($_POST["locations"])."'";
	
	$result = mysql_query($sql) or die(mysql_error($result));
	
	if (!empty($_POST["tag"])) {		
		foreach ($_POST["tag"] as $key => $value) {
			$sql = "
			UPDATE locations_tags 
			SET permanency = 1 
			WHERE location_id='".mysql_real_escape_string($_POST["locations"])."' 
			AND tag_id='{$key}'";
			$result = mysql_query($sql) or die(mysql_error($result));
		}
	}
	
	$memcache = new Memcache();
	$memcache->connect("localhost") or die ("Could not connect to memcache");
	$memcache->flush();
}

if (isset($_POST["video"]) && isset($_POST["locations"]) ) {
	
}

mysql_close($conn);
?>
<html>
	<head>
		<title>Add Description</title>
		<script type="text/javascript"
	src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>	
		<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("#locations").change(function() {
		$.getJSON(("content.php?i=" + $(this).val()), function(json) {
			$("#descImage").val(json.image);
			$("#description").val(json.description);
			$("#count").html(json.count);			
		});
		$.getJSON(("variables.php?t=1&o=" + $(this).val()), function(json) {
			for(var x = 1; x <= 10; x++){
				if (x <= json.length) { $("#image" + x).val(json[x-1].data); } 
				else { $("#image" + x).val(""); }							
			}
		});
		$.getJSON(("variables.php?t=2&o=" + $(this).val()), function(json) {
			for(var x = 1; x <= 6; x++){
				if (x <= json.length) { $("#video" + x).val(json[x-1].data); } 
				else { $("#video" + x).val(""); }
			}
		});
		$.getJSON(("tags.php?i=" + $(this).val()), function(json) {
			var output = "<fieldset><legend>Tag Permanency</legend>";
			for(var x= 0; x < json.length; x++) {
				output += "<input type='checkbox'";
				if (json[x].permanency == 1) {
					output += " checked='checked'";
				}
				output += " id='tag"+json[x].id+"' name='tag["+json[x].id+"]'/> <label for='tag"+json[x].id+"'>"+json[x].title+"</label> ";				
			}
			output += "</fieldset>";

			$("#permanentTags").html(output);
			/*
			for(var x = 1; x <= 6; x++){
				if (x <= json.length) { $("#video" + x).val(json[x-1].data); } 
				else { $("#video" + x).val(""); }
			}*/
		});
	});	
});
//]]>
		</script>	
	</head>
	<body>
		<a href='dashboard.php'>Back to Dashboard</a>
		<form name='descriptionForm' id='descriptionForm' action='input.php' method='post' >
			<p>
			<label for='locations'>Locations:</label>
			<br/>
			<select name='locations' id ='locations'>
				<option value='#'> </option>
				<?php foreach ($resultsArray as $value) {
					echo "<option value='{$value["id"]}' >{$value["title"]}</option>";
				} ?>
			</select>
			</p>
			<p>
			Count: <span id='count'>0</span>
			</p>
			<p>
			<label for='image'>Description Image:</label>
			<input type='text' id = 'descImage' name='descImage'/>
			</p>
			<p>
			<label for='description'>Description:</label><br/>
			<textarea id='description' name='description' style='width:500px;height:500px' ></textarea>
			</p>
			<div id='permanentTags'>
			</div>
			<fieldset>
				<legend>Gallery Images</legend>
				<p>Urls for the images.</p>
				<p>
				<label for='image1'>Image 1</label> <input type='text' id = 'image1' name='image[1]'/>
				<label for='image2'>Image 2</label> <input type='text' id = 'image2' name='image[2]'/>
				</p><p>
				<label for='image3'>Image 3</label> <input type='text' id = 'image3' name='image[3]'/>
				<label for='image4'>Image 4</label> <input type='text' id = 'image4' name='image[4]'/>
				</p><p>
				<label for='image5'>Image 5</label> <input type='text' id = 'image5' name='image[5]'/>
				<label for='image6'>Image 6</label> <input type='text' id = 'image6' name='image[6]'/>
				</p><p>
				<label for='image7'>Image 7</label> <input type='text' id = 'image7' name='image[7]'/>
				<label for='image8'>Image 8</label> <input type='text' id = 'image8' name='image[8]'/>
				</p><p>
				<label for='image9'>Image 9</label> <input type='text' id = 'image9' name='image[9]'/>
				<label for='image10'>Image 10</label> <input type='text' id = 'image10' name='image[10]'/>
				</p>
			</fieldset>
			<fieldset>
				<legend>Gallery Videos</legend>
				<p>Put the youtube ids here. Ex: "S8yjnHW_E-Q"</p>
				<p>
				<label for='video1'>Video 1</label> <input type='text' id = 'video1' name='video[1]'/>
				<label for='video2'>Video 2</label> <input type='text' id = 'video2' name='video[2]'/>
				</p><p>
				<label for='video3'>Video 3</label> <input type='text' id = 'video3' name='video[3]'/>
				<label for='video4'>Video 4</label> <input type='text' id = 'video4' name='video[4]'/>
				</p><p>
				<label for='video5'>Video 5</label> <input type='text' id = 'video5' name='video[5]'/>
				<label for='video6'>Video 6</label> <input type='text' id = 'video6' name='video[6]'/>
				</p>
			</fieldset>
			<input type='submit' value='Submit'/>
		</form>
	</body>
</html>

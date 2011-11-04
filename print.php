<?php
$lat = $_REQUEST["lat"];
$lng = $_REQUEST["lng"];
$myloc = $_REQUEST["loc"];
$args = "'$lat', '$lng', '$myloc'";
?><!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="css/print.css" media="screen" />
<link rel="stylesheet" href="css/print.css" media="print" />
<script type="text/javascript"
	src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript"
	src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="js/print.js"></script>
</head>
<body class='noPadding' onload="initialize(<?= $args; ?>)">

<div id="map_canvas"></div>

<div id='myLocations'></div>

<label for='notesInput'>Notes:</label><br/>
<textarea name='notesInput' id='notesInput'></textarea><br/>
<input type='button' id='printButton' name='printButton' value='Print'/>

</body>
</html>
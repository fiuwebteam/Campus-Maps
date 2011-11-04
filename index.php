<!DOCTYPE html>
<html>
<head>
<title>Campus Maps</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<!-- Style Sheet -->
<link rel="stylesheet" href="css/style.css" media="screen" />

<!-- Google Map API -->
<script type="text/javascript"
	src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!-- JQuery -->
<script type="text/javascript"
	src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

<!-- JQuery UI for draggability -->
<script type="text/javascript" src="js/jquery-ui-1.8.12.custom.min.js"></script>

<!-- Modal Window plugin --> 
<link rel="stylesheet" href="css/modal-basic.css" media="screen" />
<script type="text/javascript" src="js/jquery.simplemodal.1.4.1.min.js"></script>

<!-- Cycle Plugin -->
<script type="text/javascript" src="js/jquery.cycle.all.js"></script>

<!-- Where the Magic happens -->
<script type="text/javascript" src="js/work.js"></script>

<!--  Google Analytics -->
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-1218730-30']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>
<body class='noPadding'>
<!-- menuBox is draggable -->
<div id="menuBox">
	<div id='extrasList'>
		<!-- This is where you can put different layers in your map. -->
		<ul id='extras'>
			<li><input type='checkbox' id='blgLayer' name='blgLayer' checked='checked' /> <label for='blgLayer'>Buildings</label></li>
			<li><input type='checkbox' id='prkLayer' name='prkLayer'/> <label for='prkLayer'>Parking</label></li>
			<li><input type='checkbox' id='meterLayer' name='meterLayer'/> - <label for='meterLayer' class='smallFont' >Metered Parking</label></li>
			<li><input type='checkbox' id='bikeLayer' name='bikeLayer'/> <label for='bikeLayer'>Bike Racks</label></li>
			<li><input type='checkbox' id='artLayer' name='artLayer'/> <label for='artLayer'>Art/Sculptures</label></li>
			<li><input type='checkbox' id='fieldLayer' name='fieldLayer'/> <label for='fieldLayer'>Fields</label></li>
		</ul>
		<!-- Click here to show the extra layers above. You probably shouldn't drop this.  -->
		<div id='extraShowHide'>
			<span>&raquo;</span>
		</div>		
	</div>
	<!-- Your logo goes here! -->
	<img src="images/mapslogo.gif" style="margin: auto; margin-left: 15px;" />
	
	<span id="campus">Select a campus/center:</span>
	<div id='campusOptions'>
		<!-- On a change, jump to somewhere else. -->
		<select id='campusList'>
			<option value='mmc' >Modesto A. Maidique</option>
			<option value='bbc' >Biscayne Bay</option>
			<option value='eng' >Engineering Center</option>
			<option value='pine' >Pines Center</option>
			<option value='wolf' >The Wolfsonian</option>
			<option value='MBUS' >Miami Beach Urban Studios</option>
			<option value='Downtown' >FIU Downtown</option>
		</select>
	</div>
	
	<div id='searchInfo'  class="smallFont" >
	Use quotation marks ("") to search with exact wording.<br/>
	Use the keyword "OR" to search for one or more words.
	</div>
	
	<!-- Search Box, but you didn't need me to tell you that. -->
	<div id='search_box'>		
		<input type='text' id='searchField' name='searchField' value='Search' /> (<span id='searchInfoMarker'> ? </span>)	
	</div>
	
	<!-- Search results box -->
	<div id='resultBox'>
		<div id='closeResult'><span><img src='./images/loc-x.gif'/></span></div>
		<div id='result'></div>
	</div>
	<div id='myLocations'></div>
</div>

<!-- When you're looking at a specific location, this is where the info, images, and tags go. -->
<div id='contentBox' class='noPadding'>
	<div id='closeContent'><span><img src='./images/loc-x.gif'/></span></div>
	<div id='content'></div>
	<div id='functions'>
		<div id='tags'></div>
		<div id='optionsDiv'></div>
	</div>
</div>

<!-- Extra functions you may or may not want in your map. Printing options, linking options, and pdfs.  -->
<div id='otherOptions'><span class="otherTools">Tools: </span>
	<a href='print.php?lat=25.75607862586501&lng=-80.37631291534427' id='printA' name='printA'>Print</a> | 
	<span id='linkA' name='linkA'>Link</span> | 
	<span id='pdfA' name='pdfA'>PDF</span>
	<div id='pdfBox'>
		<a href='bbc-map.pdf'>BBC</a><br/>
		<a href='mmc-map.pdf'>MMC</a>
	</div>
</div>

<div id='feedback'>
	<!-- Link to your feedback form -->
	<a href="http://link.to.your/feedback.form" onclick="window.open(this.href,  null, 'height=620, width=680, toolbar=0, location=0, status=1, scrollbars=1, resizable=1'); return false" title="FIU Campus Maps Feedback">Please fill out my form.</a>
</div>

<div id="map_canvas"></div>

</body>
</html>
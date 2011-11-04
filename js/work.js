var markersArray = [];
var locationMarkersArray = [];
var map = null;

var buildingsLayer = buildingsLayer = new google.maps.KmlLayer('http://foo.edu/layers/Buildings.kmz', 
		{ suppressInfoWindows: true, preserveViewport:true });
var parkingLayer = new google.maps.KmlLayer('http://foo.edu/layers/Park.kmz', 
		{ suppressInfoWindows: true, preserveViewport:true });
var parkingMeterLayer = new google.maps.KmlLayer('http://foo.edu/layers/Meters.kmz', 
		{ suppressInfoWindows: true, preserveViewport:true });
var bikeLayer = new google.maps.KmlLayer('http://foo.edu/layers/Bike.kmz', 
		{suppressInfoWindows: true, preserveViewport:true });
var artLayer = new google.maps.KmlLayer('http://foo.edu/layers/Arts.kmz', 
		{ suppressInfoWindows: true, preserveViewport:true });
var fieldLayer = new google.maps.KmlLayer('http://foo.edu/layers/Fields.kmz', 
		{suppressInfoWindows: false, preserveViewport:true });

var defaultLat = "25.75607862586501";
var defaultLng = "-80.37631291534427";

var imagesArray = [];
var videosArray = [];

var defaultMapOptions = {
  zoom: 16,
  mapTypeId: google.maps.MapTypeId.SATELLITE,
  scaleControl: true
};


/*
 * Add the specified location to My Locations
 * id: The id of the location you are adding to your My Location
 */

function addLocation(id) {
	var params = getHashParams();
	var ids = "";
	var location = "";
	for(var x = 0; x < params.length; x++) {
		if (params[x] == "loc") { ids = params[++x]; } 
		else { location += params[x] + "/"; }
	}
	ids = ids.split(",");	
	if ($.inArray(id, ids) == -1) {
		ids.push(id);
	}	
	var first = true;	
	for (var x = 0; x < ids.length; x++ ) {
		if (ids[x] != "") {
			if (first == false) { location += ","; } 
			else {
				location += "loc/";
				first = false;			
			}
			location += ids[x];			
		}
	}	
	window.location.hash = location;	
	populateMyLocations();
	updatePrint();
}

/*
 * Insert new tags to a location.
 * id: The id of the location you are adding the tag to.
 */

function addTags(id) {
	var tags = '';
	while (answer = prompt('Enter new tags separated by spaces. (10 max)', tags), answer.split(' ').length > 10) {
		alert("Less than 10 tags, please.");
		tags = answer;
	}	
	answer = answer.replace(/,/g, "+");
	answer = answer.replace(/ /g, "+");
	
	$.getJSON(("tags.php?i=" + id + "&t=" + answer), function(json) {
		alert(json.message);
		getTags(id);
	});
}

/*
 * Name says it all, it animates the show hide bar.
 */

function animateShowHide() {
	if ($("#extraShowHide span").html() == "«") {
		$("#extraShowHide span").html("»");
		$("#extras").hide("slow");
	} else {
		$("#extraShowHide span").html("«");			
		$("#extras").show("slow");
	}
}

/*
 * Name says it all, it closes the detail box.
 */

function closeDetailBox() {
	$("#tags").html("");
	$("#contentBox").slideUp("slow");
}

/*
 * Deletes all markers in the array by removing references to them.
 */
function deleteOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
    markersArray.length = 0;
  }
}

/*
 * Drop the location from My Locations
 * id: The id of the location you are dropping.
 */

function dropLocation(id) {
	var params = getHashParams();
	var ids = "";
	var location = "";
	for(var x = 0; x < params.length; x++) {
		if (params[x] == "loc") { ids = params[++x]; } 
		else { location += params[x] + "/";  }		
	}
	ids = ids.split(",");
	location += "loc/";
	var first = true;
	for(var x = 0; x < ids.length; x++) {
		if (ids[x] != id && ids[x] != "") {
			if (first) { first = false; } 
			else { location += ","; }
			location += ids[x]; 			
		}	
	}	
	window.location.hash = location;
	populateMyLocations();
	updatePrint();
}

/*
 * Drop the tag attached to this location.
 * locationTag: The tag to be dropped.
 * tagId: The id of the tag.
 */
function dropTag(locationTag, tagId) {
	$.getJSON(("tags.php?i=" + locationTag + "&d=" + tagId), function(json) {
		if (json.valid != 1) { alert(json.message); }
		getTags(locationTag);
	});
}

/*
 * Get The details of the location and populate the results window with the date.
 * id: The id of the location.
 */
function getDetail(id) {
	$.getJSON(("content.php?i=" + id), function(json) {
		var myLatlng = new google.maps.LatLng(json.latitude, json.longitude);
		deleteOverlays();
		
		placeMarker(myLatlng)
		
		map.panTo(myLatlng);
		var output = "";
		if (json.image != null && json.image != "") {
			output += "<img src='./images/"+json.image+"'/><br/>";
		}
		output += "<span class='title'>"+json.title+"</span>";
		output += "<p class='descriptionText'>"+json.description+"</p>";
		
		$.getJSON(("variables.php?t=1&o=" + id), function(imageJson) {
			if (imageJson.length > 0) {
				output += "<span class='fancyBox' onclick='showModal(1);'><img src='./images/photos.png'/></span> ";		
				
				imagesArray = [];
				for (var x = 0; x < imageJson.length; x++) {
					imagesArray[x] = new Image();
					imagesArray[x].src = imageJson[x].data;
				}
				
			}
			$.getJSON(("variables.php?t=2&o=" + id), function(videoJson) {
				if (videoJson.length > 0) {
					output += "<span class='fancyBox' onclick='showModal(2);'><img src='./images/video.png'/></span>";
					
					videosArray = [];
					for (var x = 0; x < videoJson.length; x++) {
						videosArray[x] = videoJson[x].data;
					}
					
				}
				$("#tags").html("");
				$("#campusList").val(json.campus);
				$("#contentBox").slideUp("slow", function() {
					$("#content").html(output);
					$("#content").fadeIn("slow");
					$("#contentBox").slideDown("slow");
				});		
				getTags(id);
				updatePrint();
			});
		});
	});
}

/*
 * Get the Hash parameters in the url bar.
 */

function getHashParams() {
	var hash = window.location.hash;
	var params = hash.substring(1);
	params = params.split("/");
	return params;
}

/*
 * Return the Google LatLng object with the starting location.
 */

function getStartLatLng(params) {
	// The Default Location
	var lat = defaultLat;
	var lng = defaultLng;
	
	for(var x = 0; x < params.length; x++) {
		if (params[x] == "lat") { lat = params[++x]; } 
		else if (params[x] == "lng") { lng = params[++x]; } 
	}
	return new google.maps.LatLng(lat, lng);
}

/*
 * Get the tags associated to this location.
 * id: The id of the location you are searching tags of.
 */

function getTags(id) {
	$.getJSON(("tags.php?i=" + id), function(json) {
		var output = "";
		if (json.length > 0) {
			output += "Tags:<br/>";
			for(var i=0; i< json.length; i++) {
				output += "<span class='searchTag' onclick='searchTag(\"" + json[i].title + "\");'>";
				output += json[i].title;
				output += "</span>";
				if (json[i].permanency == 0) {
					output += "<span class='dropTag' onclick='dropTag(\"" + id + "\", \"" + json[i].id + "\");' >(-)</span> ";
				} else {
					output += "<span class='dropTag' onclick='alert(\"This tag has been permanently added to this location\")' > </span> ";
				}
									
			}				
		}
		$("#tags").html(output);
		populateOptions(id);
	});	
}

/*
 * Return if the location marker is enable for the first page load. 
 */

function isLocationMarkerEnabled(params) {
	var doubleCheck = 0;
	for(var x = 0; x < params.length; x++) {
		if (params[x] == "lat") { doubleCheck++; } 
		else if (params[x] == "lng") { doubleCheck++; } 
	}
	if (doubleCheck == 2) { return true; } 
	else { return false; }
}


function initChangeCampus() {
	$("#campusList").change(function() {
		var lat = 25.75607862586501;
		var lng = -80.37631291534427;
		switch($(this).val()) {
			case "mmc":
				lat = 25.75607862586501;
				lng = -80.37631291534427;
				map.setZoom(16);
				break;
			case "bbc":
				lat = 25.910582835543167;
				lng = -80.13935044667386;
				map.setZoom(16);
				break;
			case "eng":
				lat = 25.769359606707788;
				lng = -80.36767083789829;
				map.setZoom(16);
				break;
			case "pine":
				lat = 26.03068335172636;
				lng = -80.37600916024815;
				map.setZoom(17);
				break;
			case "wolf":
				lat = 25.780741197430313;
				lng = -80.13244769781124;
				map.setZoom(17);
				break;		
			case "MBUS":
				lat = 25.7892238839808;
				lng = -80.1324549892031;
				map.setZoom(17);
				break;
			case "Downtown":
				lat = 25.76310595898423;
				lng = -80.1911445053277;
				map.setZoom(17);
				break;
		}	
		var myLatlng = new google.maps.LatLng(lat, lng);	
		map.panTo(myLatlng);		
	});
}

function initLayerCheckBoxes() {
	$("#blgLayer").change(function() {
		if ($(this).is(":checked")) { buildingsLayer.setMap(map); } 
		else { buildingsLayer.setMap(null); }
	});
	$("#prkLayer").change(function() {
		if ($(this).is(":checked")) { parkingLayer.setMap(map); } 
		else { parkingLayer.setMap(null); }
	});
	$("#meterLayer").change(function() {
		if ($(this).is(":checked")) { parkingMeterLayer.setMap(map); } 
		else { parkingMeterLayer.setMap(null); }
	});
	$("#bikeLayer").change(function() {
		if ($(this).is(":checked")) { bikeLayer.setMap(map); } 
		else { bikeLayer.setMap(null); }		
	});
	$("#artLayer").change(function() {
		if ($(this).is(":checked")) { artLayer.setMap(map); } 
		else { artLayer.setMap(null); }		
	});
	$("#fieldLayer").change(function() {
		if ($(this).is(":checked")) { fieldLayer.setMap(map); } 
		else { fieldLayer.setMap(null); }		
	});
}

/*
 * Initiate the actions for the link functions, allowing user to copy and paste a url with their favorite locations.
 */

function initLink() {
	$("#linkA").click(function() {		
		var latLng = map.getCenter();
		var lat = latLng.lat();
		var lng = latLng.lng();
		var params = getHashParams();
		var location = "";
		var loc = "";
		for(var x = 0; x < params.length; x++) {
			if (params[x] == "loc") {
				loc = params[++x];
				break;
			} 
		}
		prompt("Copy and Paste", "http://" + window.location.hostname + window.location.pathname  + "#lat/" + lat + "/lng/" + lng + "/loc/" + loc );
	});
} 

/*
 * Initiate the mouseover effect for the search info marker.
 */

function initSearchInfoMarker() {
	$("#searchInfoMarker").mouseover(function() { $("#searchInfo").toggle(); });	
	$("#searchInfoMarker").mouseout(function() { $("#searchInfo").toggle(); });
}

/*
 * Make sure google is up and running, if not fire up an error.
 */

function hasGoogleApi() {
	if (typeof(google.maps.Map) == "undefined") {
		var message = "An error occurred during the loading of the campus map " +
				"or during its use. Please reload the map to try and correct this error." +
				"\r\n\r\nIf you have tried reloading and it has not corrected the " +
				"issue please try clearing your Internet Browser's cache." +
				"\r\n\r\nFinally, if reloading the map and clearing your cache fail " +
				"to correct the problem there might be an issue with the Google Mapping " +
				"Service." +
				"\r\n\r\nWe apologize for the inconvenience.";
		alert(message);
		return false;
	}
	return true;
}

/*
 * Put a new marker on the map.
 */

function placeMarker(myLatlng) {
	marker = new google.maps.Marker({
        position: myLatlng, 
        map: map,
        animation: google.maps.Animation.BOUNCE
    });	
	markersArray.push(marker);
}

/*
 * The "My Locations" Div is filled with the 
 * location details found in the loc hash parameter.
 */

function populateMyLocations() {
	var params = getHashParams();
	var ids;
	for(var x = 0; x < params.length; x++) {
		if (params[x] == "loc") { ids = params[x+1]; break; }
	}
	$.getJSON(("content.php?m=" + ids), function(json) {
		if (locationMarkersArray) {
			for (i in locationMarkersArray) { locationMarkersArray[i].setMap(null); }
			locationMarkersArray.length = 0;
		}
		var output = "";
		if (json.length > 0 ) {
			output = "<span id='myLocationsTitle'>My Locations</span><ul>";
			for(var i=0; i< json.length; i++) {
				output += "<li class='locItem'><span class='locTitle' onclick='getDetail(\"" + json[i].id + "\");'>";
				output += json[i].title;
				output += "</span>";
				output += "<span class='closeMe' onclick='dropLocation(\"" + json[i].id + "\");' ><img src='./images/loc-x.gif'/>";
				output += "</span>";				
				output += "</li>";
				var myLatlng = new google.maps.LatLng(json[i].latitude, json[i].longitude);
				var marker = new google.maps.Marker({
			        position: myLatlng, 
			        map: map,
			        icon: "http://maps.google.com/mapfiles/ms/micons/yellow.png"
			        
			    });	
				locationMarkersArray.push(marker);
			}
			output += "</ul>";
		}
		$("#myLocations").html(output);
	});
}

/*
 * Populate the options for the results box.
 */

function populateOptions(id) {
	var output = "<img src='./images/add.gif' class='add' /> <span onclick='addLocation(\"" + id + "\")'>Add to My Locations</span> | ";
	output += "<span onclick='addTags(\"" + id + "\");'>Add Tags</span>";
	$("#optionsDiv").html(output);
}

/*
 * Search the locations for something.
 */

function search() {
	$.getJSON(("search.php?q=" + $("#searchField").val()), function(json) {
		var output = "<ul>";
		if (json.length == 0) { output += "<li><strong>No matches.</strong><br/><span class='smallFont'>Something missing? Tag it!</span></li>"; }
		for(var i=0; i< json.length; i++) {
			output += "<li onclick='getDetail(\"" + json[i].id + "\");' >";
			output += json[i].title;
			output += "</li>";
		}
		output += "</ul>";
		$("#resultBox").slideUp("slow", function() {
			$("#result").fadeIn("slow");
			$("#result").html(output);
			$("#resultBox").slideDown("slow");
		});	
	});
}

/*
 * Search locations with the tag
 */
function searchTag(tag) {
	$("#searchField").val(tag);
	search();
}

/*
 * Show the Modal window for a location.
 * type: either a photo gallery or a video gallery.
 */

function showModal(type) {
	var output = "<div id='slideshow'>";
	var length = 0;
	switch (type) {
		case 1:
			length = imagesArray.length;
			for (var x = 0; x < length; x++) {
				var height = 550;
				var width = 750;
				if (imagesArray[x].width > imagesArray[x].height) { height = "auto"; } 
				else { width = "auto"; }
				output += "<div><img src='"+imagesArray[x].src+"' width="+width+" height="+height+" /></div>";
			}
			break;
		case 2:
			length = videosArray.length;
			for (var x = 0; x < length; x++) {
				output += 
				"<div>" +
				"<iframe width='780' height='482' src='http://www.youtube.com/embed/" +
				videosArray[x] + "' frameborder='0' allowfullscreen></iframe>" +
				"</div>";
			}
			break;
	}
	output += "</div>";
	
	if (length > 1) {
		output += "<div>" +
			"<a href='#' id='prev'>Prev</a> " +
			"<a href='#' id='next'>Next</a>" +
			"</div>";
	}
	$.modal(output,{
		opacity:20,
		overlayCss: {backgroundColor:"#000"},
		overlayClose:true
	});	
	$('#slideshow').cycle({
		fx: 'fade',
        speed: "fast",
        timeout: 0,
        next: "#next",
        prev: "#prev"
	});
}

/*
 * Update the url for the print link with the proper parameters.
 */

function updatePrint() {
	var latLng = map.getCenter();
	var lat = latLng.lat();
	var lng = latLng.lng();
	var params = getHashParams();
	var location = "";
	var loc = "";
	for(var x = 0; x < params.length; x++) {
		if (params[x] == "loc") { loc = params[++x]; break; } 
	}
	$("#printA").attr("href", "print.php?lat="+lat+"&lng="+lng+"&loc="+loc);
}

$(document).ready(function(){
	// Make the meduBox draggable... duh?
	$("#menuBox").draggable();	
	
	// Make sure we have the google api available, if not stop the application.
	if (hasGoogleApi() == false) { return; }	
	
	// Get Hash Parameters
	params = getHashParams();
	
	// default Lat Lng
	var myLatlng = getStartLatLng(params);
	
	// Default Map variables 
	var myOptions = defaultMapOptions; 
	myOptions["center"] = myLatlng;
	
	// setting the global variable map.
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	
	// if the hash parameters dicate, place a marker on the map
	if (isLocationMarkerEnabled(params)) { placeMarker(myLatlng); }	
	
	populateMyLocations();	
	updatePrint();
	
	// The default active layer, the buildings.
	buildingsLayer.setMap(map);

	// Active the details window when a building or an art piece is clicked.
	google.maps.event.addListener(buildingsLayer, 'click', function(kmlEvent) { getDetail(kmlEvent.featureData.id); });
	google.maps.event.addListener(artLayer, 'click', function(kmlEvent) { getDetail(kmlEvent.featureData.id); });
	
	// Blank out the search field when it gets the focus.
	$("#searchField").focus(function() { $("#searchField").val(""); });
	
	// Close the search box when the X is clicked
	$("#closeResult span").click(function() { $("#resultBox").slideUp("slow"); });
	
	// Close the detail box and empty out the tags when the X is clicked.
	$("#closeContent span").click(function() { closeDetailBox(); });
	
	// If you click outside of the details box, close it.
	$("#map_canvas").click(function() { closeDetailBox(); });
	
	// Press enter, activate the search.
	$("#searchField").keypress(function(e) { if(e.which == 13) { search(); } });
	
	// Click the show/hide bar, animate it!
	$("#extraShowHide").click(function() { animateShowHide(); });
	
	// Show the pdf box when clicked.
	$("#pdfA").click(function() {$("#pdfBox").toggle(); });
	
	// Actions are prepared to fire when you click them.
	initLayerCheckBoxes();	
	initChangeCampus();
	initLink();
	initSearchInfoMarker();

});

var markersArray = [];
var map = null;

//Deletes all markers in the array by removing references to them
function deleteOverlays() {
  if (markersArray) {
    for (i in markersArray) {
      markersArray[i].setMap(null);
    }
    markersArray.length = 0;
  }
}

function getDetail(id) {
	$.getJSON(("content.php?i=" + id), function(json) {
		var myLatlng = new google.maps.LatLng(json.latitude, json.longitude);
		deleteOverlays();
		marker = new google.maps.Marker({
	        position: myLatlng, 
	        map: map,
	        animation: google.maps.Animation.BOUNCE
	    });	
		markersArray.push(marker);
		map.panTo(myLatlng);
		var output = "";
		output += json.title + "\n";
		output += json.description;
		
		var tmp = document.createElement("DIV");
		tmp.innerHTML = output;
		output = tmp.textContent||tmp.innerText;
		
		$("#notesInput").val(output);
	});
}

function initialize(lat, lng, loc) {
    var myLatlng = new google.maps.LatLng(lat, lng);
	var myOptions = {
      zoom: 16,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.SATELLITE,
      scaleControl: true
    }
	map = new google.maps.Map(document.getElementById("map_canvas"),
	        myOptions);
	buildingsLayer = new google.maps.KmlLayer('http://campusmaps.fiu.edu/layers/Buildings.kmz', 
			{ suppressInfoWindows: true, preserveViewport:true }
	);
	buildingsLayer.setMap(map);
	
	$.getJSON(("content.php?m=" + loc), function(json) {
		var output = "";
		if (json.length > 0 ) {
			output = "My Locations<ul>";
			for(var i=0; i< json.length; i++) {
				output += "<li><span onclick='getDetail(\"" + json[i].id + "\");'>";
				output += json[i].title;
				output += "</span>";
				output += "</li>";
				
				var myLatlng = new google.maps.LatLng(json[i].latitude, json[i].longitude);
				var marker = new google.maps.Marker({
			        position: myLatlng, 
			        map: map,
			        icon: "http://maps.google.com/mapfiles/ms/micons/yellow.png"
			        
			    });
				
			}
			output += "</ul>";
		}
		$("#myLocations").html(output);
	});
	
	google.maps.event.addListener(buildingsLayer, 'click', function(kmlEvent) {		
		var id = kmlEvent.featureData.id;
		getDetail(id);		
	});
	$("#printButton").click(function() {
		window.print();
	});
  }

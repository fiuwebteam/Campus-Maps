var markersArray = [];
var keys = [];
var kmlName = "";

function addslashes(str) {
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}

$(document).ready(function(){
	var kml = prompt("Where can I find this kml file? (KML not KMZ)");
	var output = "INSERT INTO locations (id, title, description, latitude, longitude, image) VALUES ";
	$.ajax({
		type: "GET",
		url: kml,
		dataType: "xml",
		success: function(xml) {
			var first = true;
			$(xml).find('Placemark').each(function(){				
				var id = $(this).attr('id');
				var name = $(this).find('name').text();
				name = name.replace("'", "&#39;");
				var description = $(this).find('description').text();
				description = description.replace("'", "&#39;");
				var fullCoords = $(this).find('coordinates').text();
				var fullCoordsArray = fullCoords.split(" ");
				var lngCoords = new Array();
				var latCoords = new Array();
				
				$.each(fullCoordsArray, function(key, value) {
					var coords = jQuery.trim(value);
					var coordsArray = coords.split(",");
					lngCoords.push(coordsArray[0]);
					latCoords.push(coordsArray[1]);					
				});
				
				var smallX, largeX, smallY, largeY;				
				var first = true;
				
				for(var x = 0; x < fullCoordsArray.length; x++) {
					if (first) {
						smallX = largeX = parseFloat(latCoords[x]);
						smallY = largeY = parseFloat(lngCoords[x]);
						first = false;
					}
					if (latCoords[x] < smallX) { smallX = parseFloat(latCoords[x]); }
					if (latCoords[x] > largeX) { largeX = parseFloat(latCoords[x]); }
					if (lngCoords[x] < smallY) { smallY = parseFloat(lngCoords[x]); }
					if (lngCoords[x] > largeY) { largeY = parseFloat(lngCoords[x]); }
				}
				
				centroidX = ( (smallX + largeX) / 2 );				
				centroidY = ( (smallY + largeY) / 2 );
				
				if (first) {first = false;}
				else {output += ", ";}
				output += "('" + id + "','" + name + "','" + description + "','" + centroidX + "','" + centroidY + "', '" + id.toLowerCase() + ".jpg')";
							
				
			});	
			$("#output").html(output);
		}
	});
});

/*				
$.each(fullCoordsArray, function(key, value) {
	var coords = jQuery.trim(value);
	var coordsArray = coords.split(",");
	lngCoords.push(coordsArray[0]);
	latCoords.push(coordsArray[1]);					
});
var area = 0.0;
var centroidX = 0.0;
var centroidY = 0.0;
var minX, minY, maxX, maxY;
for(var x = 0; x < (fullCoordsArray.length - 2); ++x) {
	var x0 = parseFloat(latCoords[x]);
	var y0 = parseFloat(lngCoords[x]);
	var x1 = parseFloat(latCoords[x+1]);
	var y1 = parseFloat(lngCoords[x+1]);
	if (x == 0) {
		minX = x0;
		minY = y0;
		maxX = x0;
		maxY = y0;
	} else {
		if (minX > x0) { minX = x0;	}
		if (minY > y0) { minY = y0; }
		if (maxX < x0) { maxX = x0; }
		if (maxY < y0) { maxY = y0; }
	}
	a = x0*y1 - x1*y0;
	area += a;
	centroidX += (x0 + x1) * a;
	centroidY += (y0 + y1) * a;
}
x0 = parseFloat(latCoords[x]);
y0 = parseFloat(lngCoords[x]);
x1 = parseFloat(latCoords[0]);
y1 = parseFloat(lngCoords[0]);

if (minX > x0) { minX = x0;	}
if (minY > y0) { minY = y0; }
if (maxX < x0) { maxX = x0; }
if (maxY < y0) { maxY = y0; }

a = x0*y1 - x1*y0;
area += a;

centroidX += (x0 + x1) * a;		
centroidY += (y0 + y1) * a;

area /= 2;

centroidX /= (6*area);
centroidY /= (6*area);

if (centroidX > maxX || centroidX < minX || centroidY > maxY || centroidY < minY) { 
	centroidX = ((maxX + minX) / 2);
	centroidY = ((maxY + minY) / 2);
}				
*/	
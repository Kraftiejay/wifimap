//------------------------------------------------
// setVariables
// This is called when the page is first loaded
// and sets different variables required to do
// a predefined search when the page is opened.
//------------------------------------------------
function setVariables() {
  searchinput = "";
  open_network = "yes";
  wep_network = "yes";
  wpa_wps_network = "yes";
  wpa_no_wps_network = "yes";
  selected_fromtime = "";
  selected_totime = "";
  band = "%";
  connected_clients = "%";
  probing_clients = "%";
  predefined_search = "%";
  activeSite = "overview";
  loadMapThemes();
}

//------------------------------------------------
// deleteMarkers
// This first deletes already existing markers on
// the map, then makes sure new data is loaded
// correctly. Calls 'loadMap'
//------------------------------------------------
function deleteMarkers() {
  var markersarrayLength = markersarray.length;
  for (i = 0; i < markersarrayLength; i++) {
    markersarray[i].setMap(null);
  }

  if (document.getElementById("open").checked) {
    open_network = "yes";
  } else {
    open_network = "no";
  }

  if (document.getElementById("wep").checked) {
    wep_network = "yes";
  } else {
    wep_network = "no";
  }

  if (document.getElementById("wpa_wps").checked) {
    wpa_wps_network = "yes";
  } else {
    wpa_wps_network = "no";
  }

  if (document.getElementById("wpa_no_wps").checked) {
    wpa_no_wps_network = "yes";
  } else {
    wpa_no_wps_network = "no";
  }

  searchinput = document.getElementById("searchinput").value;
  selected_fromtime = document.getElementById("selected_fromtime").value;
  selected_totime = document.getElementById("selected_totime").value;

  //Sets "band", which bands to show
  if (document.getElementById("2.4ghz_band").checked) {
    band = "2.4ghz";
  } else if (document.getElementById("5ghz_band").checked) {
    band = "5ghz";
  } else {
    band = "%";
  }

  if (document.getElementById("connected_clients").checked) {
    connected_clients = ":";
  } else {
    connected_clients = "%";
  }

  if (document.getElementById("probing_clients").checked) {
    probing_clients = ":";
  } else {
    probing_clients = "%";
  }

  predefined_search = document.getElementById("predefined_search").value;

  loadMap();
}

//This runs when page is loaded, after variables are set
function loadMap() {
  var map = new google.maps.Map(document.getElementById("map"), {
    center: new google.maps.LatLng(mapCenterLatitude, mapCenterLongitude),
    mapTypeId: mapTypeNetwork,
    zoom: defaultZoomLevelNetwork,
    zoomControlOptions: {
      position: google.maps.ControlPosition.LEFT_BOTTOM
    },
    fullscreenControl: false,
    streetViewControl: false
  });

  map.setOptions({
    styles: mapThemeNetwork
  });
  var mcOptions = {
    gridSize: 70,
    maxZoom: 16
  };
  var infoWindow = new google.maps.InfoWindow;

  downloadUrl("php/genxml.php", function(data) {
    var xml = data.responseXML;
    markers = xml.documentElement.getElementsByTagName("marker");

    markersarray = [];

    var markersLength = markers.length;

    for (var i = 0; i < markersLength; i++) {
      var BSSID = markers[i].getAttribute("BSSID");
      var VENDOR = markers[i].getAttribute("VENDOR");
      var SSID = markers[i].getAttribute("SSID");
      var FREQUENCY = markers[i].getAttribute("FREQUENCY");
      var CHANNEL = markers[i].getAttribute("CHANNEL");
      var CAPABILITIES = markers[i].getAttribute("CAPABILITIES");
      var ICON = markers[i].getAttribute("ICON");
      var LASTSEEN = markers[i].getAttribute("LASTSEEN");
      var LASTLAT = markers[i].getAttribute("LASTLAT");
      var LASTLON = markers[i].getAttribute("LASTLON");
      var BESTLEVEL = markers[i].getAttribute("BESTLEVEL");
      var BESTLAT = markers[i].getAttribute("BESTLAT");
      var BESTLON = markers[i].getAttribute("BESTLON");
      var CONNECTED_CLIENTS = markers[i].getAttribute("CONNECTED_CLIENTS");
      var PROBING_CLIENTS = markers[i].getAttribute("PROBING_CLIENTS");

      var point = new google.maps.LatLng(
        parseFloat(BESTLAT),
        parseFloat(BESTLON));

      var BSSIDFunctionFriendly = '"' + BSSID + '"';
      var SSIDFunctionFriendly = '"' + SSID + '"';

      //This is the pop-up window that appears when clicking on a network
      var html = "<b>" + SSID + "</b> <br>" + BSSID + "<br>" + VENDOR + "<br>" + "<br>" + CAPABILITIES + "<br>" + popup_channel + CHANNEL + " (" + FREQUENCY + " MHz)" + "<br>" + "Signal: " + BESTLEVEL + " dBm" + "<br>" + popup_lastseen + LASTSEEN + "<br>" + "<br>" + popup_connectedclients + "<b>" + CONNECTED_CLIENTS + "</b>" + "<br>" + popup_probingclients + "<b>" + PROBING_CLIENTS + "</b>" + "<br>" + "<input type='button' onclick='getLocation(" + BSSIDFunctionFriendly + ");' value='Precise location' class='infoWindowSearchButton'>" + "   " + "<input type='button' id='showclients' onclick='openClientTab();' value='Client info' class='infoWindowSearchButton'>";

      var marker = new google.maps.Marker({
        map: map,
        position: point,
        icon: ICON
      });

      bindInfoWindow(marker, map, infoWindow, html);
      markersarray.push(marker);

    } //END FOR LOOP

    //This is grouping of networks that are close together
    mc = new MarkerClusterer(map, markersarray, mcOptions);

  }); //END downloadUrl

} //END loadMap

function bindInfoWindow(marker, map, infoWindow, html) {
  google.maps.event.addListener(marker, 'click', function() {
    infoWindow.setContent(html);
    infoWindow.open(map, marker);
  });
}

//------------------------------------------------
// downloadUrl
// sends form data to server and receives xml file
// with network data in return
//------------------------------------------------
function downloadUrl(url, callback) {
  var request = new XMLHttpRequest();
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      request.onreadystatechange = doNothing;
      callback(request, request.status);
    }
  };

  request.open('POST', url, true);
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send("open_network=" + open_network + "&" + "wep_network=" + wep_network + "&" + "wpa_wps_network=" + wpa_wps_network + "&" + "wpa_no_wps_network=" + wpa_no_wps_network + "&" + "searchinput=" + searchinput + "&" + "selected_fromtime=" + selected_fromtime + "&" + "selected_totime=" + selected_totime + "&" + "band=" + band + "&" + "connected_clients=" + connected_clients + "&" + "probing_clients=" + probing_clients + "&" + "predefined_search=" + predefined_search);
}

//------------------------------------------------
// getLocation
// opens a new tab with location of network
//------------------------------------------------
function getLocation(BSSIDFunctionFriendly) {
  bssid = BSSIDFunctionFriendly;
  locationWindow = window.open(locationPageAddress);
}

//------------------------------------------------
// openClientTab
// opens a new tab, for client lookup
//------------------------------------------------
function openClientTab() {
  alert("Copy client MAC from this window, and paste in client window");
  locationWindow = window.open(clientsPageAddress);
}

function doNothing() {}

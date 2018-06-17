<?php

require("dbinfo.php");

// Start XML file, create parent node
$dom = new DOMDocument("1.0", "UTF-8");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Opens a connection to a MySQL server
$mysqli = new mysqli("localhost", $username, $password, $database);

// Change character set to utf8
mysqli_set_charset($mysqli,"utf8");

// Convert selected_fromtime to epoch time
if ($_POST[selected_fromtime] == "") {
	$selected_fromtime_epoch = "0000000000000";
} else {
	$selected_fromtime_epoch = 1000 * date_format(date_create($_POST[selected_fromtime]), 'U');
}

// Convert selected_totime to epoch time
if ($_POST[selected_totime] == "") {
	$selected_totime_epoch = "32503679995000";
} else {
	$selected_totime_epoch = 1000 * date_format(date_create($_POST[selected_totime]), 'U');
}

// Select rows in network table
//ONLY OPEN
if ($_POST[open_network] == "yes" && $_POST[wep_network] == "no" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "no") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND NOT	(CAPABILITIES LIKE '%WEP%' OR CAPABILITIES LIKE '%WPA%')
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//EVERYTHING EXCEPT OPEN
elseif ($_POST[open_network] == "no" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "yes" && $_POST[wpa_no_wps_network] == "yes") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND	(CAPABILITIES LIKE '%WEP%' OR CAPABILITIES LIKE '%WPA%')
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY WEP
elseif ($_POST[open_network] == "no" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "no") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND	CAPABILITIES LIKE '%WEP%'
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//EVERYTHING EXCEPT WEP
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "no" && $_POST[wpa_wps_network] == "yes" && $_POST[wpa_no_wps_network] == "yes") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND NOT	CAPABILITIES LIKE '%WEP%'
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY WPA WITH WPS
elseif ($_POST[open_network] == "no" && $_POST[wep_network] == "no" && $_POST[wpa_wps_network] == "yes" && $_POST[wpa_no_wps_network] == "no") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND	(CAPABILITIES LIKE '%WPA%' AND CAPABILITIES LIKE '%WPS%' AND CAPABILITIES NOT LIKE '%WEP%')
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//EVERYTHING EXCEPT WPA WITH WPS
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "yes") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND NOT	(CAPABILITIES LIKE '%WPA%' AND CAPABILITIES LIKE '%WPS%' AND CAPABILITIES NOT LIKE '%WEP%')
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY WPA WITHOUT WPS
elseif ($_POST[open_network] == "no" && $_POST[wep_network] == "no" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "yes") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND	(CAPABILITIES LIKE '%WPA%' AND CAPABILITIES NOT LIKE '%WEP%' AND CAPABILITIES NOT LIKE '%WPS%')
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//EVERYTHING EXCEPT WPA WITHOUT WPS
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "yes" && $_POST[wpa_no_wps_network] == "no") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch'	AND LASTTIME < '$selected_totime_epoch')
	AND NOT	(CAPABILITIES LIKE '%WPA%' AND CAPABILITIES NOT LIKE '%WEP%' AND CAPABILITIES NOT LIKE '%WPS%')
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY OPEN + WEP
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "no") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND	((CAPABILITIES NOT LIKE '%WEP%'	AND CAPABILITIES NOT LIKE '%WPA%') OR (CAPABILITIES LIKE '%WEP%'))
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY OPEN + WPA WITH WPS
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "no" && $_POST[wpa_wps_network] == "yes" && $_POST[wpa_no_wps_network] == "no") {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND	((CAPABILITIES NOT LIKE '%WEP%'	AND CAPABILITIES NOT LIKE '%WPA%') OR (CAPABILITIES LIKE '%WPA%' AND CAPABILITIES LIKE '%WPS%' AND CAPABILITIES NOT LIKE '%WEP%'))
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY OPEN + WPA WITHOUT WPS
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "no" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "yes")	{
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND	((CAPABILITIES NOT LIKE '%WEP%'	AND CAPABILITIES NOT LIKE '%WPA%') OR (CAPABILITIES LIKE '%WPA%' AND CAPABILITIES NOT LIKE '%WEP%' AND CAPABILITIES NOT LIKE '%WPS%'))
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY WEP + WPA WITH WPS
elseif ($_POST[open_network] == "no" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "yes" && $_POST[wpa_no_wps_network] == "no")	{
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND NOT	((CAPABILITIES NOT LIKE '%WEP%'	AND CAPABILITIES NOT LIKE '%WPA%') OR (CAPABILITIES LIKE '%WPA%' AND CAPABILITIES NOT LIKE '%WEP%' AND CAPABILITIES NOT LIKE '%WPS%'))
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY WEP + WPA WITHOUT WPS
elseif ($_POST[open_network] == "no" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "yes")	{
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND NOT	((CAPABILITIES NOT LIKE '%WEP%'	AND CAPABILITIES NOT LIKE '%WPA%') OR (CAPABILITIES LIKE '%WPA%' AND CAPABILITIES LIKE '%WPS%' AND CAPABILITIES NOT LIKE '%WEP%'))
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//ONLY WPA (BOTH TYPES)
elseif ($_POST[open_network] == "yes" && $_POST[wep_network] == "yes" && $_POST[wpa_wps_network] == "no" && $_POST[wpa_no_wps_network] == "no")	{
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
  AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND NOT	((CAPABILITIES NOT LIKE '%WEP%'	AND CAPABILITIES NOT LIKE '%WPA%') OR (CAPABILITIES LIKE '%WEP%'))
  AND BAND LIKE '$_POST[band]'
  AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
  AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
  AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

//IF NOTHING MATCHES/DEFAULT
else {
	$query = "SELECT * FROM network WHERE
	(SSID LIKE '%$_POST[searchinput]%' OR BSSID LIKE '%$_POST[searchinput]%')
	AND (LASTTIME > '$selected_fromtime_epoch' AND LASTTIME < '$selected_totime_epoch')
	AND BAND LIKE '$_POST[band]'
	AND CONNECTED_CLIENTS LIKE '%$_POST[connected_clients]%'
	AND PROBING_CLIENTS LIKE '%$_POST[probing_clients]%'
	AND PREDEFINED_SEARCH LIKE '%$_POST[predefined_search]%'";
}

$result = $mysqli->query($query);

header("Content-type: text/xml;charset=UTF-8");

// Iterate through the rows, adding XML nodes for each
while ($row = $result->fetch_assoc()){
	// ADD TO XML DOCUMENT NODE
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("BSSID", $row['bssid']);
  $newnode->setAttribute("SSID", $row['ssid']);
  $newnode->setAttribute("FREQUENCY", $row['frequency']);
  $newnode->setAttribute("CAPABILITIES", $row['capabilities']);
  $newnode->setAttribute("LASTLAT", $row['lastlat']);
  $newnode->setAttribute("LASTLON", $row['lastlon']);
  $newnode->setAttribute("BESTLEVEL", $row['bestlevel']);
  $newnode->setAttribute("BESTLAT", $row['bestlat']);
  $newnode->setAttribute("BESTLON", $row['bestlon']);
  $newnode->setAttribute("CHANNEL", $row['channel']);
  $newnode->setAttribute("VENDOR", $row['vendor']);
  $newnode->setAttribute("LASTSEEN", $row['lastseen']);
  $newnode->setAttribute("ICON", $row['icon']);
  $newnode->setAttribute("CONNECTED_CLIENTS", $row['connected_clients']);
  $newnode->setAttribute("PROBING_CLIENTS", $row['probing_clients']);
  $newnode->setAttribute("PREDEFINED_SEARCH", $row['predefined_search']);
}

echo $dom->saveXML();

?>

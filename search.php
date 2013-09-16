<?
header('Content-Type: application/json; charset=utf-8');
require_once "MysqliDb.php";

$lat = $_GET['latitude'];  // latitude of centre of bounding circle in degrees
$lon = $_GET['longitude'];  // longitude of centre of bounding circle in degrees
$rad = $_GET['radius'];  // radius of bounding circle in kilometers

if (!checkNumeric(array($lat, $lon, $rad))) {
	http_response_code(400);
	exit();
}

$db = new MysqliDb("host","user","password","database");

$max = intval($_GET['max']);

if($max == 0) {
	$max = 20;
}

$R = 6371*1000;  // earth's radius, km
// first-cut bounding box (in degrees)
$maxLat = $lat + rad2deg($rad/$R);
$minLat = $lat - rad2deg($rad/$R);
// compensate for degrees longitude getting smaller with increasing latitude
$maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
$minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));
// convert origin of filter circle to radians
$lat = deg2rad($lat);
$lon = deg2rad($lon);


$q="Select metadata, latitude, longitude,altitude, type,". 
"acos(sin($lat)*sin(radians(latitude)) + cos($lat)*cos(radians(latitude))*cos(radians(longitude)-$lon))*$R As distance".
" From (".
"Select * ".
"From geopoint ".
"Where latitude > $minLat And latitude < $maxLat".
"  And longitude > $minLon And longitude < $maxLon".
") As FirstCut ".
"Where acos(sin($lat)*sin(radians(latitude)) + cos($lat)*cos(radians(latitude))*cos(radians(longitude)-$lon))*$R < $rad".
" order by distance ASC limit $max";

$res=$db->rawQuery($q);

function checkNumeric($input) {
	foreach($input as $f) {
		if ($f == null || !is_numeric($f)) {
			return false;
		}
	}
	return true;
}

?>
{"results":<?=json_encode($res)?>}

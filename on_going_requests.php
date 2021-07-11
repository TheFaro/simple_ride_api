<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'), true);


if(isset($_POST['user_id']) && isset($_POST['driver_id'])){

	$userId = $_POST['user_id'];
	$driverId = $_POST['driver_id'];
	$latitude;
	$longitude;
	$quantity = (int)$_POST['quantity'];

	getUserLocation($con, $userId);

	$sql = "SELECT * FROM on_going_requests WHERE driver_data_driver_id = '$driverId' AND request_status = 'Accepted'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		$response["success"] = 0;
		$response["message"] = "This driver has too many requests. Please select another.";		
	}else{
		if ($latitude != null && $longitude != null) {
			$sql = "INSERT INTO on_going_requests(user_data_user_id, driver_data_driver_id,source_latitude, source_longitude, destination_latitude, destination_longitude, current_latitude, current_longitude, destination_name,user_payment_method,request_status, distance_km, quantity, rating_from_user, rating_from_driver) VALUES('$userId', '$driverId','$latitude', '$longitude', NULL, NULL, '$latitude', '$longitude', NULL, NULL, NULL, NULL, '$quantity', 0, 0)";	
		}

		if($con->query($sql)){
			//insert was successful.
			$response["success"] = 1;
			$response["message"] = "Insert successfull, Continue.";
			$response["id"] = $con->insert_id;
		}else{
			$response["success"] = 0;
			$response["message"] = "Insert was not successful.";
		}		
	}
}elseif(isset($_POST['id']) && isset($_POST['latitude']) && isset($_POST['longitude'])){

	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];
	$id = $_POST['id'];
	$name = $_POST['destination_name'];
	$distance;

	getDistance($con, $id);
	$sql = "UPDATE on_going_requests SET destination_latitude = '$latitude', destination_longitude = '$longitude', destination_name = '$name', distance_km = '$distance' WHERE id = '$id'";
	if($con->query($sql)){
		//update successful.
		$response["success"] = 1;
		$response["message"] = "Update successful.";
	}else{
		$response["success"] = 0;
		$response["message"] = "Update unsuccessful.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

function getUserLocation($con, $userId){

	global $latitude, $longitude;

	$sql = "SELECT latitude, longitude FROM user_data WHERE user_id = '$userId'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		$row = $result->fetch_assoc();
		$latitude = $row["latitude"];
		$longitude = $row["longitude"];
	}
}

//function to calculate the distance between source and destination.
function getDistance($con, $id){

	global $distance;
	//first get the source and destination coordinates.
	$sql = "SELECT * FROM on_going_requests WHERE id = '$id'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		$row = $result->fetch_assoc();
		$sourceLat = $row["source_latitude"];
		$sourceLng = $row["source_longitude"];
		$destLat = $row["destination_latitude"];
		$destLng = $row["destination_longitude"];

		$distance = twopoints_on_earth($sourceLat, $sourceLng, $destLat, $destLng);

	}
}

//function that calculates the distance between two points on earth.
function twopoints_on_earth($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo){

	$long1 = deg2rad($longitudeFrom);
	$long2 = deg2rad($longitudeTo);
	$lat1 = deg2rad($latitudeFrom);
	$lat2 = deg2rad($latitudeTo);

	//Harvesine formular
	$dlong = $long2 - $long1;
	$dlat = $lat2 - $lat1;

	$val = pow(sin($dlat/2),2) + cos($lat1)*cos($lat2)*pow(sin($dlong/2),2);
	$res = 2 * atan2(sqrt($val),sqrt(1-$val));
	$radius = 6372.797;

	return ($res*$radius);
}
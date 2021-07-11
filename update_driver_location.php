<?php
require_once __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['driver_id']) && isset($_POST['latitude']) && isset($_POST['longitude'])){

	$id =$_POST['driver_id'];
	$latitude = $_POST['latitude'];
	$longitude = $_POST['longitude'];

	$sql = "UPDATE driver_data SET latitude = '$latitude', longitude = '$longitude', location_update_time = CURRENT_TIMESTAMP WHERE driver_id ='$id'";

	if($con->query($sql)){
		//update successful.
		$response["success"] = 1;
		$response["message"] = "Driver location update successful.";
	}else{
		//update not successful.
		$response["success"] = 0;
		$response["message"] = "Driver location could not be updated.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
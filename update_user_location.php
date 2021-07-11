<?php

require_once __DIR__ . '/db_config.php';

$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$response = array();

$_POST = json_decode(file_get_contents('php://input'), true);


if(isset($_POST['user_id']) && isset($_POST['latitude']) && isset($_POST['longitude'])){

	$id = $_POST['user_id'];
	$lat = $_POST['latitude'];
	$lng = $_POST['longitude'];

	$sql = "UPDATE user_data SET latitude = '$lat', longitude = '$lng' WHERE user_id = '$id'";

	if($con->query($sql)){
		//updated successfully.
		$response["success"] = 1;
		$response["message"] = "Success in uploading current location.";
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not update current location in database.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
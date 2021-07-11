<?php

require_once  __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['driver_id']) && isset($_POST['status'])){

	$id = $_POST['driver_id'];
	$status = $_POST['status'];

	$sql = "UPDATE driver_data SET work_status = '$status' WHERE driver_id = '$id'";

	if($con->query($sql)){
		//update successful.
		$response["success"] = 1;
		$response["message"] = "Work Status changed to '$status'.";
	}else{
		//update not successful.
		$response["success"] = 0;
		$response["message"] = "Work status could not be changed.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
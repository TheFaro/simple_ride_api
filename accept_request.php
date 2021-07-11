<?php

require_once __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'),true);

if(isset($_POST['request_id']) && isset($_POST['status'])){

	$requestId = $_POST['request_id'];
	$status = $_POST['status'];

	$sql = "UPDATE on_going_requests SET request_status = '$status' WHERE id = '$requestId'";
	if($con->query($sql)){
		//update successfully.
		//the request has been successfully accepted.
		$response["success"] = 1;
		$response["message"] = "Request has been accepted.";
	}else{
		//update was not successful.
		$response["success"] = 0;
		$response["message"] = "Could not accepted user request.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
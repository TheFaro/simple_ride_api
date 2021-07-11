<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['user_id'])){
	$id = $_POST['user_id'];

	$sql = "SELECT * FROM user_data WHERE user_id = '$id'";
	$result =$con->query($sql);

	if($result->num_rows > 0){
		$row = $result->fetch_assoc();
		$response["latitude"] = $row["latitude"];
		$response["longitude"] = $row["longitude"];
		$response["success"] = 1;
		$response["message"] = "Retrieved last known location successfully.";
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not retrieve last known location.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
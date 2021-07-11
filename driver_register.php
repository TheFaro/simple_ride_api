<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'),true);

if(isset($_POST['first_name']) && isset($_POST['id_number']) && isset($_POST['phone_number'])){

	$firstName = $_POST['first_name'];
	$lastName = $_POST['last_name'];
	$idNum = $_POST['id_number'];
	$phoneNum = $_POST['phone_number'];
	$password = $_POST['password'];

	if(checkRegistration($con, $idNum, $phoneNum, $password)){
		$response["success"] = 0;
		$response["message"] = "Driver has already been registered. Login instead.";
	}else{
		if(registerDriver($con, $firstName, $lastName, $idNum, $phoneNum, $password)){
			$response["id"] = $con->insert_id;
			$response["firstName"] = $firstName;
			$response["lastName"] = $lastName;
			$response["idNum"] = $idNum;
			$response["phoneNum"] = $phoneNum;
			$response["profileUrl"] = "no image";
		}else{
			$response["success"] = 0;
			$response["message"] = "Could not register driver.";
		}
	}

}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

function checkRegistration($con, $idNum, $phoneNum, $password){

	$sql = "SELECT * FROM driver_data WHERE identity_number = '$idNum' AND phone_num ='$phoneNum'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//driver is registered.
		return true;
	}else{
		//driver is not registered.
		return false;
	}
}

function registerDriver($con, $firstName, $lastName, $idNum, $phoneNum, $password){

	$sql = "INSERT INTO driver_data(driver_id, first_name, last_name, identity_number, phone_num, profile_image, create_time, password, latitude, longitude, location_update_time, work_status, rating) VALUES(NULL,'$firstName', '$lastName','$idNum', '$phoneNum', '', CURRENT_TIMESTAMP, '$password', 0, 0, CURRENT_TIMESTAMP, 1, 0)";

	if($con->query($sql)){
		//insert was successful.
		return true;
	}else{
		//insert was not successful.
		return false;
	}
}
<?php

require_once __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$temp;
$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['phoneNum']) && isset($_POST['password']) && isset($_POST['is_user'])){

	$phoneNum = $_POST['phoneNum'];
	$password = $_POST['password'];

	if(checkRegistration($con, $phoneNum, $password)){
		$response["id"] = (int)$temp['id'];
		$response["firstName"] = $temp['firstName'];
		$response["lastName"] = $temp['lastName'];
		$response["email"] = $temp['email'];
		$response["phoneNum"] = $temp['phoneNum'];
		$newUrl = str_replace("/var/www/html", "http://192.168.43.56", $temp['profileUrl']);
		$response["profileUrl"] = $newUrl;
		$response["driver"] = false;
	}else{
		$response["success"] = 0;
		$response["message"] = "User has not been registered. Register first.";
	}
}elseif(isset($_POST['phoneNum']) && isset($_POST['password']) && isset($_POST['is_driver'])){

	$phoneNum = $_POST['phoneNum'];
	$password = $_POST['password'];

	if(checkRegistrationDriver($con, $phoneNum, $password)){
		$response['id'] = (int)$temp['id'];
		$response['firstName'] = $temp['firstName'];
		$response['lastName'] = $temp['lastName'];
		$response['idNum'] = $temp['idNum'];
		$response['phoneNum'] = $temp['phoneNum'];
		$response['profileUrl'] = $temp['profileUrl'];
	}else{
		$response['success'] = 0;
		$response['message'] = "Vehicle owner has not been registered. Register first.";
	}


}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

function checkRegistration($con, $phoneNum, $password){

	global $temp;

	$sql = "SELECT * FROM user_data WHERE phone_num = '$phoneNum' AND password = '$password'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//user has been registered.
		//return a success for authentication.
		$row = $result->fetch_assoc();
		$temp = array(
			"id" => $row["user_id"],
			"firstName" => $row["first_name"],
			"lastName" => $row["last_name"],
			"email" => $row["email"],
			"phoneNum" => $row["phone_num"],
			"profileUrl" => $row["profile_image"]
		);
		return true;
	}else{
		//user has not been registered.
		//return a false for authentication.
		return false;
	}
}

function checkRegistrationDriver($con, $phoneNum , $password){

	global $temp;

	$sql = "SELECT * FROM driver_data WHERE phone_num = '$phoneNum' AND password = '$password'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//driver has been registered.
		//return success for authentication.
		$row = $result->fetch_assoc();
		$temp = array(
			"id" => $row['driver_id'],
			"firstName" => $row['first_name'],
			"lastName" => $row['last_name'],
			"idNum" => $row['identity_number'],
			"phoneNum" => $row['phone_num'],
			"profileUrl" => $row['profile_image']
		);

		return true;
	}else{
		//driver has not been registered
		return false;
	}
}
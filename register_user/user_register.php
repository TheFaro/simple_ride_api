<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// array holding allowed Origin domains
$allowedOrigins = array(
	'(http(s)://)?(www\.)?my\-domain\.com'
);

if (!empty($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
	foreach ($allowedOrigins as $allowedOrigin) {
		if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
			header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 1000');
			header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
			break;
		}
	}
}

require_once dirname(__DIR__, 1) . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die($con->error);
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['first_name']) && !empty($json['last_name']) && !empty($json['phone_num'])) {

	$firstName = $json['first_name'];
	$lastName = $json['last_name'];
	$email = $json['email'];
	$phoneNum = $json['phone_num'];
	$password = $json['password'];

	if (checkUserRegistration($con, $email, $phoneNum)) {
		//user is already registered.
		$response["success"] = 0;
		$response["message"] = "User has already been registered. Login instead.";
	} else {
		//user has not been registered yet.
		if (registerUser($con, $firstName, $lastName, $email, $phoneNum, $password)) {
			//user has been registered successfully.
			$temp = array();
			$temp["id"] = $con->insert_id;
			$temp["firstName"] = $firstName;
			$temp["lastName"] = $lastName;
			$temp["email"] = $email;
			$temp["phoneNum"] = $phoneNum;
			$temp["driver"] = false;

			$response['payload'] = $temp;
			$response['success'] = 1;
			$response['message'] = "Success.";
		} else {
			$response["success"] = 0;
			$response["message"] = "Could not register user.";
		}
	}
} else {
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);


function checkUserRegistration($con, $email, $phoneNum)
{

	$sql = "SELECT * FROM user_data WHERE email = '$email' AND phone_num = '$phoneNum'";
	$result = $con->query($sql);

	if ($result->num_rows > 0) {
		//user is already registered
		return true;
	} else {
		//user has not yet been registered
		return false;
	}
}

function registerUser($con, $firstName, $lastName, $email, $phoneNum, $password)
{
	global $response;

	$sql = "INSERT INTO user_data(first_name, last_name, email, phone_num, password, profile_image, create_time) VALUES('$firstName', '$lastName', '$email', '$phoneNum', '$password', NULL, CURRENT_TIMESTAMP)";

	if ($con->query($sql)) {
		//inserted successfully.
		return true;
	} else {
		//could not insert successfully.
		$response['error'] = $con->error;
		return false;
	}
}

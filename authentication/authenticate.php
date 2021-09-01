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


if (!empty($json['phone_num']) && !empty($json['password']) && !empty($json['is_user'])) {

	$phoneNum = $json['phone_num'];
	$password = $json['password'];

	if (checkRegistration($con, $phoneNum, $password)) {

		$response['success'] = 1;
		$response['message'] = 'Successfully authenticated.';
	} else {
		$response["success"] = 0;
		$response["message"] = "Authentication failed. Register first.";
	}
} elseif (!empty($json['phone_num']) && !empty($json['password']) && !empty($json['is_driver'])) {

	$phoneNum = $json['phone_num'];
	$password = $json['password'];

	if (checkRegistrationDriver($con, $phoneNum, $password)) {
		$response['id'] = (int)$temp['id'];
		$response['first_name'] = $temp['first_name'];
		$response['last_name'] = $temp['last_name'];
		$response['id_number'] = $temp['id_number'];
		$response['phone_num'] = $temp['phone_num'];
		$response['profileUrl'] = $temp['profileUrl'];
	} else {
		$response['success'] = 0;
		$response['message'] = "Vehicle owner has not been registered. Register first.";
	}
} else {
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

function checkRegistration($con, $phoneNum, $password)
{
	global $response;

	$sql = "SELECT * FROM user_data WHERE phone_num = '$phoneNum' AND password = '$password'";
	$result = $con->query($sql);

	if ($result->num_rows > 0) {
		//user has been registered.
		//return a success for authentication.
		$row = $result->fetch_assoc();
		$temp = array(
			"id" => $row["user_id"],
			"first_name" => $row["first_name"],
			"last_name" => $row["last_name"],
			"id_number" => $row["id_number"],
			"phone_num" => $row["phone_num"],
			"profileUrl" => $row["profile_image"],
			"driver" => false,
		);

		$newUrl = str_replace("/var/www/html", "http://192.168.43.56", $temp['profileUrl']); // tODO: check the path for windows
		$temp["profileUrl"] = $newUrl;

		$response['payload'] = $temp;
		return true;
	} else {
		//user has not been registered.
		//return a false for authentication.
		return false;
	}
}

function checkRegistrationDriver($con, $phoneNum, $password)
{

	global $temp;

	$sql = "SELECT * FROM driver_data WHERE phone_num = '$phoneNum' AND password = '$password'";
	$result = $con->query($sql);

	if ($result->num_rows > 0) {
		//driver has been registered.
		//return success for authentication.
		$row = $result->fetch_assoc();
		$temp = array(
			"id" => $row['driver_id'],
			"first_name" => $row['first_name'],
			"last_name" => $row['last_name'],
			"id_number" => $row['identity_number'],
			"phone_num" => $row['phone_num'],
			"profileUrl" => $row['profile_image']
		);

		return true;
	} else {
		//driver has not been registered
		return false;
	}
}

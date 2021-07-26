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
$responser = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die($con->error);
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['user_id'])) {
	$userId = $json['user_id'];
	$image = $json['image'];

	//image meta-data
	if ($image != null) {
		$ext = $json['extension'];
		$imageName = md5(time()) . ".png";
		$serverUrl = ""; //todo: put path url for profile images  //"https://vybe.ashio.me/images/userProfilePics/$imageName";

		$sql = "UPDATE user_data SET profile_image = '$serverUrl' WHERE user_id = '$userId'";

		if ($con->query($sql)) {

			//$fh = fopen("ftp://fanele@ashio.me:Iminlove2404@ftp.ashio.me/images/userProfilePics/$imageName", 'w');
			$fh = fopen("", 'w'); // TODO : input path
			fwrite($fh, base64_decode($image));
			fclose($fh);

			//successful update
			$response["success"] = 1;
			$response["message"] = "Successfully updated your profile picture.";
			$response["payload"] = array('imageUrl' => $serverUrl);
		} else {
			$response["success"] = 0;
			$response["message"] = "Could not update user profile image";
		}
	} else {
		$response["success"] = 0;
		$response["message"] = "No image has been selected.";
	}
} else {
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

<?php

require_once __DIR__ . '/db_config.php';

$responser = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'),true);

if(isset($_POST['user_id'])){
	$userId = $_POST['user_id'];
	$image = $_POST['image'];

	//image meta-data
	if($image != null){
		$ext = $_POST['extension'];
		$imageName = md5(time()).".png";
		$serverUrl = "https://vybe.ashio.me/images/userProfilePics/$imageName";

		$sql = "UPDATE user_data SET profile_image = '$serverUrl' WHERE user_id = '$userId'";

		if($con->query($sql)){

			$fh = fopen("ftp://fanele@ashio.me:Iminlove2404@ftp.ashio.me/images/userProfilePics/$imageName", 'w');
			fwrite($fh, base64_decode($image));
			fclose($fh);

			//successful update
			$response["success"] = 1;
			$response["message"] = "Successfully updated your profile picture.";
			$response["imageUrl"] = $serverUrl;
		}else{
			$response["success"] = 0;
			$response["message"] = "Could not update user profile image";
		}
	}else{
		$response["success"] = 0;
		$response["message"] = "No image has been selected.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";	
}

ob_clean();
echo json_encode($response);
<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$_POST = json_decode(file_get_contents('php://input'),true);

if(isset($_POST['driver_phone'])){
	$phone = $_POST['driver_phone'];

	$sql = "SELECT * FROM driver_data WHERE phone_num = '$phone'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//send to text message handler to send the user a confirmation message
		//try md5 for generating 
		$code = md5(time());
		echo $code;

		//add the users number and the confirmation code to a relation. 
		$new = "INSERT INTO confirmations_data() VALUES('$phone', '$code', CURRENT_TIMESTAMP)";
		if($con->query($new)){
			//successfully uploaded
			$response["success"] = 1;
			$response["message"] = "Sent code to message handler and saved metadata successfully.";
		}else{
			$response["success"] = 2;
			$response["message"] = "Could not insert into metadata relation.";
		}
	}else{
		//driver is not registered in the database.
		$response["success"] = 0;
		$response["message"] = "Driver is not registered in database.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

if(isset($_POST['user_phone'])){
	$phone = $_POST['user_phone'];

	$sql = "SELECT * FROM user_data WHERE phone_num = '$phone'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//send to text message handler to send the user a confirmation message
		//try md5 for generating 
		$code = md5(time());
		echo $code;

		//add the users number and the confirmation code to a relation. 
		$new = "INSERT INTO confirmations_data(phone, confirmation_code, request_time) VALUES('$phone', '$code', CURRENT_TIMESTAMP)";
		if($con->query($new)){
			//successfully uploaded
			$response["success"] = 1;
			$response["message"] = "Sent code to message handler and saved metadata successfully.";
		}else{
			$response["success"] = 2;
			$response["message"] = "Could not insert into metadata relation.";
		}
	}else{
		//driver is not registered in the database.
		$response["success"] = 0;
		$response["message"] = "User is not registered in database.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
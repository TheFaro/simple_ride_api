<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['driver_phone'])){
	$phone = $_POST['driver_phone'];
	$password = $_POST['new_password'];

	$sql = "UPDATE driver_data SET password = '$password' WHERE phone_num = '$phone'";
	if($con->query($sql)){
		//update of password successful.
		//retrieve the rest of the user information to facilitate login success.
		$sql1 = "SELECT * FROM driver_data WHERE phone_num = '$phone'";
		$result = $con->query($sql1);

		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$response["id"] = $row["user_id"];
			$response["name"] = $row["first_name"];
			$response["surname"] = $row["last_name"];
			$response["idNum"] = $row["identity_number"];
			$response["phone"] = $row["phone_num"];
			$response["success"] = 1;
			$response["message"] = "Update driver password successful.";
		}else{
			$response["success"] = 0;
			$response["message"] = "Driver retrieve information not successful.";
		}
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not update the driver password";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

if(isset($_POST['user_phone'])){
	$phone = $_POST['user_phone'];
	$password = $_POST['new_password'];

	$sql = "UPDATE user_data SET password = '$password' WHERE phone_num = '$phone'";
	if($con->query($sql)){
		//update of password successful.
		//retrieve the rest of the user information to facilitate login success.
		$sql1 = "SELECT * FROM user_data WHERE phone_num = '$phone'";
		$result = $con->query($sql1);

		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$response["id"] = $row["user_id"];
			$response["name"] = $row["first_name"];
			$response["surname"] = $row["last_name"];
			$response["email"] = $row["email"];
			$response["phone"] = $row["phone_num"];
			$response["success"] = 1;
			$response["message"] = "Update user password successful.";
		}else{
			$response["success"] = 0;
			$response["message"] = "User retrieve information not successful.";
		}
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not update the user password";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);
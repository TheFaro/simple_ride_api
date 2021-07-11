<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'),true);

if(isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['phone_num'])){

	$firstName = $_POST['first_name'];
	$lastName = $_POST['last_name'];
	$email = $_POST['email'];
	$phoneNum = $_POST['phone_num'];
	$password = $_POST['password'];

	if(checkUserRegistration($con, $email, $phoneNum)){
		//user is already registered.
		$response["success"] = 0;
		$response["message"] = "User has already been registered. Login instead.";
	}else{
		//user has not been registered yet.
		if(registerUser($con ,$firstName, $lastName, $email, $phoneNum, $password)){
			//user has been registered successfully.
			$response["id"] = $con->insert_id;
			$response["firstName"] = $firstName;
			$response["lastName"] = $lastName;
			$response["email"] = $email;
			$response["phoneNum"] = $phoneNum;
			$response["driver"] = false;
		}else{
			$response["success"] = 0;
			$response["message"] = "Could not register user.";
		}
	}

}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);


function checkUserRegistration($con, $email, $phoneNum){

	$sql = "SELECT * FROM user_data WHERE email = '$email' AND phone_num = '$phoneNum'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//user is already registered
		return true;
	}else{
		//user has not yet been registered
		return false;
	}
}

function registerUser($con, $firstName, $lastName, $email, $phoneNum, $password){
	$sql = "INSERT INTO user_data(first_name, last_name, email, phone_num, password) VALUES('$firstName', '$lastName', '$email', '$phoneNum', '$password')";

	if($con->query($sql)){
		//inserted successfully.
		return true;
	}else{
		//could not insert successfully.
		return false;
	}
}

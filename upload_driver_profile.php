<?php

require_once __DIR__ . '/db_config.php';
$response = array();

$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['profile_image']) && isset($_POST['car_image']) && isset($_POST['number_plate'])){

	$id = $_POST['id'];
	$profileImage = $_POST['profile_image'];
	$carName = $_POST['car_name'];
	$numberPlate = $_POST['number_plate'];
	$seats = $_POST['seats'];
	$carImage = $_POST['car_image'];
	$profileServer;
	$carServer;
	$carId;

	if($profileImage != null){
		$ext = $_POST['profile_ext'];
		$imageName = md5(time()).".png";
		$profileServer = "https://vybe.ashio.me/images/driverProfilePics/$imageName";
	}

	if($carImage != null){
		$ext = $_POST['car_ext'];
		$imageName = md5(time()).".png";
		$carServer = "https://vybe.ashio.me/images/vehicleImages/$imageName";
	}

	//update driver profile first
	$sql = "UPDATE driver_data SET profile_image = '$profileServer' WHERE driver_id = '$id'";
	if($con->query($sql)){
		//update successful.
		$fh = fopen("ftp://fanele@ashio.me:Iminlove2404@ftp.ashio.me/images/driverProfilePics/$imageName", 'w');
		fwrite($fh, base64_decode($profileImage));
		fclose($fh);

		//proceed with execution.
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not update driver data relation.";
	
	}

	//insert into vehicle_data
	$sql1 = "INSERT INTO vehicle_data(vehicle_model, vehicle_num_plate, vehicle_profile_pic, number_of_seats) VALUES('$carName', '$numberPlate', '$carServer', '$seats')";
	if($con->query($sql1)){
		//insert successful.
		$fh = fopen("ftp://fanele@ashio.me:Iminlove2404204@ftp.ashio.me/images/vehicleImages/$imageName", 'w');
		fwrite($fh, base64_decode($carImage));
		fclose($fh);

		$carId = $con->insert_id;

		//proceed.
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not insert vehicle data.";
	}

	//insert into joined table
	$sql2 = "INSERT INTO driver_data_has_vehicle_data(driver_data_driver_id, vehicle_data_vehicle_id) VALUES('$id', '$carId')";
	if($con->query($sql2)){
		//insert successful.
		$response["success"] = 1;
		$response["message"] = "Upload successul.";
	}else{
		$response["success"] = 0;
		$response["message"] ="Could not update joined relation.";
	}
}

echo json_encode($response);
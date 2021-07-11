<?php

require_once __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['user_id'])){
	$id = $_POST['user_id'];

	getTransaction($con, $id);
	getUserInfo($con);
	getDriverInfo($con);
	getCarId($con);
	getCarInformation($con);

}elseif(isset($_POST['driver_id'])){

	$driverId = $_POST['driver_id'];

	getDriverTransaction($con, $driverId);
	getUserInfo($con);
	getDriverInfo($con);
	getCarId($con);
	getCarInformation($con);

}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

//function to get the transaction from the server database
function getTransaction($con, $userId){

	global $response;

	$sql = "SELECT * FROM on_going_requests WHERE user_data_user_id = '$userId'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$temp = array(
				"requestId" => $row["id"],
				"userId" => $row["user_data_user_id"],
				"driverId" => $row["driver_data_driver_id"],
				"destinationName" => $row["destination_name"],
				"distance" => $row["distance_km"],
				"price" => $row["distance_km"] * 6.5,
				"requestStatus" => $row["request_status"],
				"sourceLatitude" => $row["source_latitude"],
				"sourceLongitude" => $row["source_longitude"],
				"destLatitude" => $row["destination_latitude"],
				"destLongitude" => $row["destination_longitude"],
				"quantity" => $row["quantity"]
			);

			array_push($response, $temp);
		}
	}
}

function getDriverTransaction($con, $driverId){

	global $response;

	$sql = "SELECT * FROM on_going_requests WHERE driver_data_driver_id = '$driverId'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$temp = array(
				"requestId" => $row["id"],
				"userId" => $row["user_data_user_id"],
				"driverId" => $row["driver_data_driver_id"],
				"destinationName" => $row["destination_name"],
				"distance" => $row["distance_km"],
				"price" => $row["distance_km"] * 6.5,
				"requestStatus" => $row["request_status"],
				"sourceLatitude" => $row["source_latitude"],
				"sourceLongitude" => $row["source_longitude"],
				"destLatitude" => $row["destination_latitude"],
				"destLongitude" => $row["destination_longitude"],
				"quantity" => $row["quantity"]
			);

			array_push($response, $temp);
		}
	}
}

//function to get the user information from server database.
function getUserInfo($con){

	global $response;

	for($i = 0; $i < sizeof($response); $i++){
		$transaction = $response[$i];
		$id = $transaction["userId"];

		$sql = "SELECT * FROM user_data WHERE user_id = '$id'";
		$result = $con->query($sql);

		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$transaction["username"] = $row["first_name"];
			$transaction["usersurname"] = $row["last_name"];
			$transaction["userphone"] = $row["phone_num"];
			$transaction["profileUrl"] = $row["profile_image"];

			$response[$i] = $transaction;
		}
	}
}

//function to get the driver information from the server database.
function getDriverInfo($con){

	global $response;

	for($i=0; $i < sizeof($response); $i++){
		$transaction = $response[$i];
		$driverId = $transaction["driverId"];
;
		$sql = "SELECT * FROM driver_data WHERE driver_id = '$driverId'";
		$result = $con->query($sql);

		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$transaction["driverName"] = $row["first_name"];
			$transaction["driverSurname"] = $row["last_name"];
			$transaction["driverPhone"] = $row["phone_num"];
			$transaction["driverProfileUrl"] = $row["profile_image"];

			$response[$i] = $transaction;
		}
	}
}

//function to get the drivers car information from the server database.
function getCarId($con){
	global $response;

	for($i=0; $i < sizeof($response); $i++){
		$transaction = $response[$i];
		$driverId = $transaction["driverId"];

		$sql = "SELECT * FROM driver_data_has_vehicle_data WHERE driver_data_driver_id = '$driverId'";
		$result = $con->query($sql);
		
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$transaction["carId"] = $row['vehicle_data_vehicle_id'];

			$response[$i] = $transaction;
		}
	}
}

function getCarInformation($con){
	global $response;

	for($i=0; $i < sizeof($response); $i++){
		$transaction = $response[$i];
		$carId = $transaction["carId"];

		$sql = "SELECT * FROM vehicle_data WHERE vehicle_id = '$carId'";
		$result = $con->query($sql);

		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			$transaction["carName"] = $row["vehicle_model"];
			$transaction["carPlate"] = $row["vehicle_num_plate"];
			$transaction["carProfileUrl"] =$row["vehicle_profile_pic"];

			$response[$i] = $transaction;
		}
	}
}
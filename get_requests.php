<?php

require_once __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['driver_id'])){

	$id = $_POST['driver_id'];
	$requestsList = array();

	getRequestInfo($con, $id);

	if(!empty($requestsList) || isset($requestsList)){

		getUserInformation($con);
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

//function to handle getting request from server database.
function getRequestInfo($con, $driverId){

	global $requestsList;

	$sql = "SELECT * FROM on_going_requests WHERE driver_data_driver_id = '$driverId' AND request_status = 'pending' || request_status = 'Accepted' limit 1";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			$temp = array(
				"requestId" => $row['id'],
				"userId" => $row['user_data_user_id'],
				"destinationName" => $row['destination_name'],
				"paymentMethod" => $row['user_payment_method'],
				"distance" => $row['distance_km'],
				"quantity" => $row['quantity'],
				"price" => $row['distance'] * 6.5,
				"sourceLatitude" => $row['source_latitude'],
				"sourceLongitude" => $row['source_longitude'],
				"destLatitude" => $row['destination_latitude'],
				"destLongitude" => $row['destination_longitude'],
				"userLatitude" => $row['current_latitude'],
				"userLongitude" => $row['current_longitude']
			);

			array_push($requestsList, $temp);
		}
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not get any requests at this time.";
	}
}

//function to get user data from serve database.
function getUserInformation($con){

	global $requestsList, $response;

	for($i = 0; $i < sizeof($requestsList); $i++){
		$item = $requestsList[$i];

		$id = $item['userId'];

		$sql = "SELECT * FROM user_data WHERE user_id = '$id'";	
		$result = $con->query($sql);

		if($result->num_rows > 0){
			$row = $result->fetch_assoc();

			$item["username"] = $row['first_name'];
			$item["userSurname"] = $row['last_name'];
			$item["phoneNum"] = $row['phone_num'];

			$response = $item;
		}
	}
}
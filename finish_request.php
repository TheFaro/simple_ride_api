<?php

require_once __DIR__ . '/db_config.php';

$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

$_POST = json_decode(file_get_contents('php://input'),true);

if(isset($_POST['method']) && isset($_POST['id'])){
	$id = $_POST['id'];
	$method = $_POST['method'];


	if($method == 0){
		
		$sql = "UPDATE on_going_requests SET user_payment_method = 'Mobile Money', request_status = 'pending' WHERE id = '$id'";
	}elseif($method == 1){
		$sql = "UPDATE on_going_requests SET user_payment_method = 'EWallet', request_status = 'pending' WHERE id = '$id'";
	}elseif($method == 2){
		$sql = "UPDATE on_going_requests SET user_payment_method = 'EMali', request_status = 'pending' WHERE id = '$id'";
	}else{
		$sql = "UPDATE on_going_requests SET user_payment_method = 'Cash', request_status = 'pending' WHERE id = '$id'";
	}


	if($con->query($sql)){
		//update successful.
		$response["success"] = 1;
		$response["message"] = "Success.";
	}else{
		$response["success"] = 0;
		$response["message"] = "Not successful.";
	}
}elseif(isset($_POST['driver_id'])){

	$id = $_POST['driver_id'];
	$requestId = $_POST['request_id'];
	$ratingFromDriver = 3;//$_POST['rating_driver'];

	$sql = "UPDATE `on_going_requests` SET `request_status` = 'Finished' WHERE `on_going_requests`.`id` = '$requestId';
";
	if($con->query($sql)){
			$response["success"] = 1;
			$response["message"] = "Finished request";
		/*$res = updateUserRating($con, $requestId);
		if($res == 1){
			$response["success"] = 1;
			$response["message"] = "Finished request";
		}elseif($res == 2){
			$response["success"] = 0;
			$response["message"] = "Error updating user rating.";
		}elseif($res == 3){
			$response["success"] = 0;
			$response["message"] = "Could not get users from requests list.";
		}else{
			$response["success"] = 0; 
			$response["message"] = "Error finishing request.";
		}*/
		
	}else{
		$response["success"] = 0;
		$response["message"] = "Could not finish request.";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

//function to update the rating of the driver after the update
function updateDriverRating($con, $driverId){

	$sql = "SELECT * FROM on_going_requests WHERE driver_data_driver_id = '$driverId'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		$total = 0;

		while($row = $result->fetch_assoc()){
			$total = $total + $row["rating_from_user"];
		}

		$average = $total / $result->num_rows;

		//insert new average into driver data relation 
		$sql = "UPDATE driver_data SET rating = '$average' WHERE driver_id = '$driver'";
		if($con->query($sql)){
			//update successful.
			return 1;
		}else{
			return 2;
		}
	}else{
		return 3;
	}
}

//function to update user rating after update
function updateUserRating($con, $requestId){

	$sql = "SELECT * FROM on_going_requests WHERE id = '$requestId'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		//get the user id from the request id
		$row = $result->fetch_assoc();
		$userId = $row["user_data_user_id"];

		$sql1 = "SELECT * FROM on_going_requests WHERE user_data_user_id = '$userId'";
		$result1 = $con->query($sql1);

		if($result1->num_rows > 0){
			$total = 0;
			$average = 0;
			while($row1 = $result1->fetch_assoc()){
				$total = $total + $row1["rating_from_driver"];
			}

			$average = $total / $result->num_rows;
			$sql2 = "UPDATE user_data SET rating = '$average' WHERE user_id = $'userId'";

			if($con->query($slq2)){
				//update successful.
				return 1;
			}else{
				return 2;
			}
		}else{
			return 3;
		}
	}else{
		return 4;
	}
}
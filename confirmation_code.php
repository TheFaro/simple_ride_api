<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['phone'])){
	$phone = $_POST['phone'];
	$first = $_POST['first'];
	$second = $_POST['second'];
	$third = $_POST['third'];
	$fourth = $_POST['fourth'];
	$fifth = $_POST['fifth'];

	$sql = "SELECT * FROM confirmations_data WHERE confirmation_code = '".$first.$second.$third.$fourth.$fifth."'";
	$result = $con->query($sql);

	if($result->num_rows > 0){
		$response["success"] = 1;
		$response["message"] = "Confirmation success";
	}else{
		$response["success"] = 0;
		$response["message"] = "Confirmation failure";
	}
}else{
	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}
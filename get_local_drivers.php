<?php

require_once __DIR__ . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);

function twopoints_on_earth($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
{

	$long1 = deg2rad($longitudeFrom);
	$long2 = deg2rad($longitudeTo);
	$lat1 = deg2rad($latitudeFrom);
	$lat2 = deg2rad($latitudeTo);

	//Harvesine formular
	$dlong = $long2 - $long1;
	$dlat = $lat2 - $lat1;

	$val = pow(sin($dlat / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($dlong / 2), 2);
	$res = 2 * atan2(sqrt($val), sqrt(1 - $val));
	$radius = 6372.797;

	return ($res * $radius);
}

//function to get all the drivers in the database
function getDrivers($con)
{
	global $driverList, $response;

	$sql = "SELECT * FROM driver_data";
	$result = $con->query($sql);

	if ($result->num_rows > 0) {
		//list of drivers retrieved, store in list
		while ($row = $result->fetch_assoc()) {
			$temp = array(
				"driver_id" => $row['driver_id'],
				"firstName" => $row['first_name'],
				"lastName" 	=> $row['last_name'],
				"phoneNum"	=> $row['phone_num'],
				"profileUrl" => $row['profile_image'],
				"latitude" 	=> $row['latitude'],
				"longitude"	=> $row['longitude'],
				"rating" => $row['rating']
			);
			array_push($driverList, $temp);
		}
	} else {
		//nothing was retrieved
		$response["success"] = 0;
		$response["message"] = "No driver is online at the moment, please try again later";
	}
}

//function to handle the fetching of vehicles based on the number of seats.
function getVehicles($con, $seats)
{

	global $vehicleList;

	$sql = "";
	if ($seats <= 4) {
		$sql = "SELECT * FROM vehicle_data WHERE number_of_seats = 4";
	} elseif ($seats >= 5 && $seats <= 8) {
		$sql = "SELECT * FROM vehicle_data WHERE number_of_seats >= 5 AND number_of_seats <=8";
	}

	$results = $con->query($sql);

	if ($results->num_rows > 0) {
		while ($row = $results->fetch_assoc()) {
			$temp = array(
				"vehicleId" => $row['vehicle_id'],
				"vehicleModel" => $row['vehicle_model'],
				"vehicleProfileUrl" => $row['vehicle_profile_pic'],
				"seats" => $row['number_of_seats']
			);
			array_push($vehicleList, $temp);
		}
	} else {
		$response["success"] = 0;
		$response["message"] = "No vehicles were retrieved. Please try again.";
	}
}

//function to filter the correct drivers from the driver list
function filterDrivers($con)
{

	global $vehicleList, $driverList, $finalList;
	$tempList = array();
	for ($i = 0; $i < sizeof($vehicleList); $i++) {
		$car = $vehicleList[$i];

		$id = $car['vehicleId'];
		$sql = "SELECT * FROM driver_data_has_vehicle_data WHERE vehicle_data_vehicle_id = '$id'";
		$result = $con->query($sql);

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$driverId = $row["driver_data_driver_id"];

			for ($j = 0; $j < sizeof($driverList); $j++) {
				$driverData = $driverList[$j];

				$tempId = $driverData['driver_id'];
				if ($tempId == $driverId) {
					$driverData['carProfileUrl'] = $car['vehicleProfileUrl'];
					$driverData['carModel'] = $car['vehicleModel'];
					$driverData['carId'] = $car['vehicleId'];
					$driverData['seats'] = $car['seats'];
					array_push($finalList, $driverData);
				}
			}
		} else {
			$response["success"] = 0;
			$response["message"] = "No driver id found.";
		}
	}
}

//function to retrieve the user's location
function getUserLocation($con, $userId)
{
	global $latitudeFrom, $longitudeFrom;

	$sql = "SELECT latitude, longitude FROM user_data WHERE user_id = '$userId'";
	$result = $con->query($sql);

	if ($result->num_rows > 0) {
		//save the location coordinates to the global variables.
		$row = $result->fetch_assoc();
		$latitudeFrom = $row["latitude"];
		$longitudeFrom = $row["longitude"];
	}
}


$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['user_id']) && isset($_POST['radius']) && isset($_POST['quantity'])) {
	$radius = $_POST['radius'];	//to convert the radius to kilometers.
	$userId = $_POST['user_id'];
	$seats = $_POST['quantity'];
	$latitudeFrom;
	$longitudeFrom;
	$driverList = array();
	$vehicleList = array();
	$finalList = array();

	//first get the user location.
	getUserLocation($con, $userId);

	if ($latitudeFrom != null && $longitudeFrom != null) {
		//user location has been recieved  successfully.
		//next, get the available drivers in the driver list.
		getDrivers($con);
		getVehicles($con, $seats);
		filterDrivers($con);

		if (sizeof($finalList) > 0) {
			//retrieved drivers successfully.
			for ($i = 0; $i < sizeof($finalList); $i++) {
				$driver = $finalList[$i];
				$latitudeTo = $driver['latitude'];
				$longitudeTo = $driver['longitude'];

				$displacement = twopoints_on_earth($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo);

				if ($displacement < $radius) {
					//this driver is within the specified radius.
					array_push($response, $driver);
				}
			}
		}
	} else {
		$response["success"] = 0;
		$response["message"] = "Could not get user location.";
	}
} else {

	$response["success"] = 0;
	$response["message"] = "Required fields missing.";
}

echo json_encode($response);

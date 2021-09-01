<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// array holding allowed Origin domains
$allowedOrigins = array(
    '(http(s)://)?(www\.)?my\-domain\.com'
);

if (!empty($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
    foreach ($allowedOrigins as $allowedOrigin) {
        if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
            header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            header('Access-Control-Max-Age: 1000');
            header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
            break;
        }
    }
}

require_once dirname(__DIR__, 1) . '/db_config.php';
$response = array();
$con = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE) or die($con->error);
$json = json_decode(file_get_contents('php://input'), true);

if (!empty($json['user_id']) && !empty($json['current_location']) && !empty($json['destination']) && !empty($json['passengers'])) {

    $id = $json['user_id'];
    $currentLocation = $json['current_location'];
    $destination = $json['destination'];
    $passengers = $json['passengers'];

    // compute current time
    $dt = new DateTime('NOW');
    $time = $dt->format('h:i:s A');
    print('Current time: ' . $time);

    $sql = "SELECT d.`driver_id`, d.`first_name`, d.`last_name`, d.`phone_num`, d.`frequent_location`, d.`profile_image`, v.`vehicle_id`, v.`vehicle_profile_pic`, v.`vehicle_model`, v.`number_of_seats`, d.`rating` FROM `driver_data` d, `vehicle_data` v, `driver_data_has_vehicle_data` dv WHERE v.`number_of_seats` = '$passengers' AND d.`frequent_location` LIKE '%$currentLocation%' AND TIME_FORMAT(d.`work_time_start`, '%H:%i') < '$time' AND TIME_FORMAT(d.`work_time_end`, '%H:%i') > '$time' dv.`driver_data_driver_id` = d.`driver_id` AND dv.`vehicle_data_vehicle_id` = v.`vehicle_id` ORDER BY d.`rating` DESC";

    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // drivers found 
        $response['payload'] = array();

        while ($row = $result->fetch_assoc()) {
            $temp = $row;

            array_push($response['payload'], $temp);
        }

        $response['success'] = 1;
        $response['message'] = "Success.";
    } else {
        // no drivers found
        // TODO: search for drivers without the location.

        $sql = "SELECT d.`driver_id`, d.`first_name`, d.`last_name`, d.`phone_num`, d.`frequent_location`, d.`profile_image`, v.`vehicle_id`, v.`vehicle_profile_pic`, v.`vehicle_model`, v.`number_of_seats`, d.`rating` FROM `driver_data` d, `vehicle_data` v, `driver_data_has_vehicle_data` dv WHERE v.`number_of_seats` = '$passengers' AND TIME_FORMAT(d.`work_time_start`, '%H:%i') < '$time' AND TIME_FORMAT(d.`work_time_end`, '%H:%i') > '$time' dv.`driver_data_driver_id` = d.`driver_id` AND dv.`vehicle_data_vehicle_id` = v.`vehicle_id` ORDER BY d.`rating` DESC";

        $result = $con->query($sql);

        if ($result->num_rows > 0) {

            // found a different set of drivers 
            $response['payload'] = array();

            while ($row = $result->fetch_assoc()) {
                $temp = $row;

                array_push($response['payload'], $temp);
            }

            $response['success'] = 2;
            $response['message'] = "Success.";
        } else {
            // could not find drivers 
            // please try again later
            $response['success'] = 0;
            $response['message'] = "Could not find drivers matching your details. Please try again.";
        }
    }
} else {
    $response['success'] = 0;
    $response['message'] = "Required fields missing.";
}

echo json_encode($response);

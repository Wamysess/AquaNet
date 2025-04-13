<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aquanet1";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in or device_number not set."]);
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// Query to get sensor data for the logged user's device_number
$sql = "SELECT * FROM sensor_data WHERE device_number = '$device_number' ORDER BY timestamp DESC LIMIT 100"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sensor_data = array();

    while ($row = $result->fetch_assoc()) {
        $sensor_data[] = $row;
    }

    // Return the sensor data in JSON format
    header('Content-Type: application/json');
    echo json_encode($sensor_data);
} else {
    echo json_encode(["status" => "error", "message" => "No data available for this device."]);
}

$conn->close();
?>

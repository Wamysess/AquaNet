<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aquanet1";

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    die("User not logged in or device_number not set.");
}

$device_number = $_SESSION['device_number'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement to retrieve sensor data for the logged user's device_number
$sql = "SELECT * FROM sensor_data WHERE device_number = ? ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $device_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $sensor_data = array();

    while($row = $result->fetch_assoc()) {
        $sensor_data[] = $row;
    }

    echo json_encode($sensor_data);
} else {
    echo json_encode([]); // Return an empty JSON array for no data
}

$stmt->close();
$conn->close();
?>

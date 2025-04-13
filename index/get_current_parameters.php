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
$device_number = $_SESSION['device_number'];

// Prepare the SQL statement to get active parameters
$stmt = $conn->prepare("SELECT * FROM sensor_parameters WHERE is_active = 1 AND device_number = ?");
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit();
}

// Bind parameters
$stmt->bind_param("s", $device_number); // 's' for string

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Prepare the parameters array
$parameters = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parameters[] = $row; // Collect each row into the parameters array
    }
}

// Return the parameters in JSON format
header('Content-Type: application/json');
echo json_encode($parameters);

// Close the statement and connection
$stmt->close();
$conn->close();
?>

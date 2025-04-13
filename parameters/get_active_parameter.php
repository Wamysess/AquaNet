<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aquanet1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    echo "User not logged in or device_number not set.";
    $conn->close();
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// Query to get the active parameter for the logged user's device_number
$query = "SELECT parameterName FROM sensor_parameters WHERE is_active = 1 AND device_number = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $device_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    echo $row['parameterName'];
} else {
    echo '';
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

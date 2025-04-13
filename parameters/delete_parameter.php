<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aquanet1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$response = array();

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    echo json_encode(["error" => "User not logged in or device_number not set."]);
    $conn->close();
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// Get and sanitize the ID
$id = intval($_POST['id']);

// Prepare the SQL query with device_number condition
$sql = "DELETE FROM sensor_parameters WHERE id = ? AND device_number = ?";

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $id, $device_number);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = "Record deleted successfully";
} else {
    $response['success'] = false;
    $response['message'] = "Error: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Set content type to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
?>

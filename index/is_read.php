<?php
session_start(); // Start the session to access session variables

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in or device_number not set."]);
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// SQL query to fetch unread notifications for the logged user's device_number
$sql = "SELECT id, message, created_at FROM notifications WHERE is_read = 0 AND device_number = '$device_number'";

// Execute the query
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    die("Error: " . $conn->error);
}

// Fetch all unread notifications
$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Set content type to JSON
header('Content-Type: application/json');

// Output the notifications in JSON format
echo json_encode($notifications);

// Close connection
$conn->close();
?>

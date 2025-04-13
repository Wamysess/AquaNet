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

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in or device_number not set."]);
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// SQL query to fetch unread notifications for the logged user's device_number
$sql = "SELECT * FROM notifications WHERE device_number = '$device_number' ORDER BY created_at DESC";
$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    // Fetch all rows and add to notifications array
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Encode the notifications array to JSON
header('Content-Type: application/json');
echo json_encode($notifications);

// Close connection
$conn->close();
?>

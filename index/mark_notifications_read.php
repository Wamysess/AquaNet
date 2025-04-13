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
    echo json_encode(["error" => "User not logged in or device_number not set."]);
    $conn->close();
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// Retrieve and decode notification IDs from POST request
$notification_ids_json = isset($_POST['ids']) ? $_POST['ids'] : '[]';
$notification_ids = json_decode($notification_ids_json, true);

// Debugging: Output received IDs
file_put_contents('debug.log', print_r($notification_ids, true), FILE_APPEND);

// Validate and sanitize IDs
if (!is_array($notification_ids) || empty($notification_ids)) {
    echo json_encode(['error' => 'No notification IDs provided']);
    $conn->close();
    exit();
}

// Prepare the SQL statement with placeholders and device_number condition
$placeholders = implode(',', array_fill(0, count($notification_ids), '?'));
$sql = "UPDATE notifications SET is_read = 1 WHERE id IN ($placeholders) AND device_number = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

// Add the device_number to the parameters
$params = array_merge($notification_ids, [$device_number]);

// Bind parameters dynamically
$stmt->bind_param(str_repeat('i', count($notification_ids)) . 's', ...$params);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => 'Notifications marked as read.']);
} else {
    echo json_encode(['error' => 'Error updating records: ' . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>

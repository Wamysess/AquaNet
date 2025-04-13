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

// Check if data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you store device_number in session upon user login
    if (!isset($_SESSION['device_number'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in."]);
        exit();
    }

    // Get the logged user's device_number from the session
    $device_number = $conn->real_escape_string($_SESSION['device_number']);
    $message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : '';
    $graphType = isset($_POST['graphType']) ? $conn->real_escape_string($_POST['graphType']) : '';

    // Validate inputs
    if (empty($message) || empty($graphType)) {
        echo json_encode(["status" => "error", "message" => "Missing parameters."]);
        exit();
    }

    // Include graphType in the message
    $messageWithGraphType = $message . ' (Type: ' . $graphType . ')';

    // Insert notification into database with device_number filtering
    $sql = "INSERT INTO notifications (message, device_number, is_read, created_at) VALUES ('$messageWithGraphType', '$device_number', 0, NOW())";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Notification added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
<?php
session_start(); // Start the session to access session variables
header('Content-Type: application/json');

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
    echo json_encode(['error' => 'User not logged in or device_number not set.']);
    $conn->close();
    exit();
}

// Get the logged user's device_number from the session
$device_number = $_SESSION['device_number'];

// SQL query to fetch parameter names for the logged user's device_number
$sql = "SELECT parameterName FROM sensor_parameters WHERE device_number = ?"; // Adjust this if needed

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $device_number);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$stmt->close();
$conn->close();
echo json_encode($data);
?>

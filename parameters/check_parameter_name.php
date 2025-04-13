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
    echo json_encode(["error" => "User not logged in or device_number not set."]);
    $conn->close();
    exit();
}

// Get the logged user's device_number from the session
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// Get POST parameters
$name = $_POST['parameterName'];
$id = $_POST['parameterId'];

// Prepare SQL query with device_number condition
$sql = 'SELECT COUNT(*) AS count FROM sensor_parameters WHERE parameterName = ? AND device_number = ?';
if (!empty($id)) {
    $sql .= ' AND id != ?';
}

// Prepare and bind parameters
$stmt = $conn->prepare($sql);
if (!empty($id)) {
    $stmt->bind_param('ssi', $name, $device_number, $id);
} else {
    $stmt->bind_param('ss', $name, $device_number);
}

// Execute the query and fetch result
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Output whether the parameter name exists for the specific device_number
$response = ['exists' => $result['count'] > 0];
echo json_encode($response);

// Close the connection
$conn->close();
?>

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
    die("User not logged in or device_number not set.");
}

$device_number = $_SESSION['device_number'];

if (isset($_POST['parameterName'])) {
    $parameter_name = $_POST['parameterName'];

    // Set all parameters for the logged user's device_number to inactive
    $query = "UPDATE sensor_parameters SET is_active = 0 WHERE device_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $device_number);
    
    if (!$stmt->execute()) {
        die("Error deactivating parameters: " . $stmt->error);
    }

    // Set the selected parameter as active for the logged user's device_number
    $query = "UPDATE sensor_parameters SET is_active = 1 WHERE parameterName = ? AND device_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $parameter_name, $device_number);
    
    if ($stmt->execute()) {
        echo "Parameter set as active successfully!";
    } else {
        echo "Error updating parameter: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "No parameter name provided.";
}
?>

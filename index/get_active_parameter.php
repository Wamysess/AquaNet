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
$device_number = $conn->real_escape_string($_SESSION['device_number']);

// Query to get current active parameters for the logged user's device_number
$sql = "SELECT minTemperature, maxTemperature, minTurbidity, maxTurbidity, minPh, maxPh, minNh3, maxNh3 
        FROM sensor_parameters 
        WHERE is_active = 1 AND device_number = '$device_number'";
        
$result = $conn->query($sql);

// Prepare the response
if ($result->num_rows > 0) {
    // Fetch the parameters
    $parameters = $result->fetch_assoc(); // Assuming only one set of active parameters is needed
    echo json_encode($parameters);
} else {
    // Return an empty JSON if no active parameters are found
    echo json_encode([]);
}

// Close the connection
$conn->close();
?>

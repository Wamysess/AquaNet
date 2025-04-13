<?php
session_start(); // Start the session to access session variables
header('Content-Type: application/json');

// Get the parameter ID from the request
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Database connection details
$host = 'localhost';
$dbname = 'aquanet1';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the logged-in user's device_number is available
    if (!isset($_SESSION['device_number'])) {
        echo json_encode(['error' => 'User not logged in or device_number not set.']);
        exit();
    }

    // Get the logged user's device_number from the session
    $device_number = $_SESSION['device_number'];

    // Query to get parameter details for the specified id and logged user’s device_number
    $stmt = $pdo->prepare('SELECT * FROM sensor_parameters WHERE id = :id AND device_number = :device_number');
    $stmt->execute(['id' => $id, 'device_number' => $device_number]);
    $parameter = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($parameter);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

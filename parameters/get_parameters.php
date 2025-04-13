<?php
session_start(); // Start the session to access session variables
header('Content-Type: application/json');

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

    // Query to get sensor parameters for the logged user's device_number
    $stmt = $pdo->prepare('SELECT * FROM sensor_parameters WHERE device_number = :device_number');
    $stmt->execute(['device_number' => $device_number]);
    $parameters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($parameters);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>

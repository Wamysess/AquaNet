<?php
session_start(); // Start the session to access session variables

$parameterId = isset($_POST['parameterId']) ? (int)$_POST['parameterId'] : 0;
$parameterName = isset($_POST['parameterName']) ? $_POST['parameterName'] : '';
$minTemperature = isset($_POST['minTemperature']) ? $_POST['minTemperature'] : '';
$maxTemperature = isset($_POST['maxTemperature']) ? $_POST['maxTemperature'] : '';
$minTurbidity = isset($_POST['minTurbidity']) ? $_POST['minTurbidity'] : '';
$maxTurbidity = isset($_POST['maxTurbidity']) ? $_POST['maxTurbidity'] : '';
$minPh = isset($_POST['minPh']) ? $_POST['minPh'] : '';
$maxPh = isset($_POST['maxPh']) ? $_POST['maxPh'] : '';
$minNh3 = isset($_POST['minNh3']) ? $_POST['minNh3'] : '';
$maxNh3 = isset($_POST['maxNh3']) ? $_POST['maxNh3'] : '';

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    die("User not logged in or device_number not set.");
}

$device_number = $_SESSION['device_number'];

$host = 'localhost';
$dbname = 'aquanet1';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the update statement to include device_number in the WHERE clause
    $stmt = $pdo->prepare('UPDATE sensor_parameters SET parameterName = :parameterName, minTemperature = :minTemperature, maxTemperature = :maxTemperature, minTurbidity = :minTurbidity, maxTurbidity = :maxTurbidity, minPh = :minPh, maxPh = :maxPh, minNh3 = :minNh3, maxNh3 = :maxNh3 WHERE id = :id AND device_number = :device_number');
    $stmt->execute([
        'id' => $parameterId,
        'parameterName' => $parameterName,
        'minTemperature' => $minTemperature,
        'maxTemperature' => $maxTemperature,
        'minTurbidity' => $minTurbidity,
        'maxTurbidity' => $maxTurbidity,
        'minPh' => $minPh,
        'maxPh' => $maxPh,
        'minNh3' => $minNh3,
        'maxNh3' => $maxNh3,
        'device_number' => $device_number, // Add device_number to the execute array
    ]);

    if ($stmt->rowCount() > 0) {
        echo 'Parameter updated successfully';
    } else {
        echo 'No parameter found for the specified ID and device number';
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

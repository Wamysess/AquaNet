<?php
session_start(); // Start the session to access session variables

header('Content-Type: application/json');

// Connect to your database
$host = 'localhost'; // Database host
$user = 'root'; // Database username
$password = ''; // Database password
$dbname = 'aquanet1'; // Database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if the logged-in user's device_number is available
if (!isset($_SESSION['device_number'])) {
    die(json_encode(['error' => 'User not logged in or device_number not set.']));
}

$device_number = $_SESSION['device_number'];

// Query to fetch sensor data for the logged user's device_number
$sql = "SELECT * FROM sensor_data WHERE device_number = ?"; // Use prepared statements for security
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $device_number);
$stmt->execute();
$result = $stmt->get_result();

$readingsByDate = [];

// Process the sensor readings
while ($row = $result->fetch_assoc()) {
    $date = (new DateTime($row['timestamp']))->format('Y-m-d'); // Group by date
    if (!isset($readingsByDate[$date])) {
        $readingsByDate[$date] = [];
    }
    $readingsByDate[$date][] = $row;
}

$summary = [];

// Calculate the summary for each date
foreach ($readingsByDate as $date => $readings) {
    $totalTemp = 0;
    $totalTurbidity = 0;
    $totalPH = 0;
    $totalNH3 = 0;

    foreach ($readings as $reading) {
        $totalTemp += $reading['Temperature'];
        $totalTurbidity += $reading['Turbidity'];
        $totalPH += $reading['pH'];
        $totalNH3 += $reading['NH3_concentration'];
    }

    $count = count($readings);

    $summary[] = [
        'date' => $date,
        'total_entries' => $count,
        'latest_timestamp' => end($readings)['timestamp'], // Get the latest timestamp
        'average_temperature' => ($count > 0) ? round($totalTemp / $count, 2) : 'NaN',
        'average_turbidity' => ($count > 0) ? round($totalTurbidity / $count, 2) : 'NaN',
        'average_ph' => ($count > 0) ? round($totalPH / $count, 2) : 'NaN',
        'average_nh3_concentration' => ($count > 0) ? round($totalNH3 / $count, 2) : 'NaN',
    ];
}

// Output the summary as JSON
echo json_encode($summary);

$stmt->close();
$conn->close();
?>

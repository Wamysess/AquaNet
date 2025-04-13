<?php
// Database connection parameters
$servername = "localhost"; // Change this if your MySQL server is hosted elsewhere
$username = "id22116392_wamythegreat";
$password = "@AquaTest23";
$dbname = "id22116392_aquatestingdatabase";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to select sensor data
$sql = "SELECT * FROM sensor_data ORDER BY timestamp DESC"; // Limiting to 100 most recent entries

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Initialize an array to store sensor data
    $sensor_data = array();

    // Fetch data from each row
    while($row = $result->fetch_assoc()) {
        // Add each row of data to the sensor_data array
        $sensor_data[] = $row;
    }

    // Convert the array to JSON format
    echo json_encode($sensor_data);
} else {
    echo "No data available";
}

// Close connection
$conn->close();
?>

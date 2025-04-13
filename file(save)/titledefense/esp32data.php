<?php

// Set timezone to your desired timezone
date_default_timezone_set('Asia/Manila');

$servername = "localhost"; // Change this if your MySQL server is hosted elsewhere
$username = "id22116392_wamythegreat";
$password = "@AquaTest23";
$dbname = "id22116392_aquatestingdatabase";

// Keep this API Key value to be compatible with the ESP32 code provided in the project page. 
// If you change this value, the ESP32 sketch needs to match
$api_key_value = "tPmAT5Ab3j7F9";

$api_key = $temperature = $turbidity = $phlevel = $nh3 = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        $temperature = test_input($_POST["temperature"]);
        $turbidity = test_input($_POST["turbidity"]);
        $phlevel = test_input($_POST["phlevel"]);
        $nh3 = test_input($_POST["nh3"]);
        
        // Get the current timestamp
        $timestamp = date("Y-m-d H:i:s");
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        } 
        
        $sql = "INSERT INTO sensor_data (temperature, turbidity, pH, NH3_concentration, timestamp)
        VALUES ('" . $temperature . "', '" . $turbidity . "', '" . $phlevel . "', '" . $nh3 . "', '" . $timestamp . "')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } 
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
        $conn->close();
    }
    else {
        echo "Wrong API Key provided.";
    }

}
else {
    echo "No data posted with HTTP POST.";
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<?php

// Set timezone to your desired timezone
date_default_timezone_set('Asia/Manila');

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aquanet1";

// API Key for validation
$api_key_value = "tPmAT5Ab3j7F9";

// Initialize variables for sensor data
$api_key = $temperature = $turbidity = $phlevel = $nh3 = $device_number = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate API Key
    $api_key = test_input($_POST["api_key"]);
    if ($api_key == $api_key_value) {
        // Retrieve sensor readings
        $temperature = test_input($_POST["temperature"]);
        $turbidity = test_input($_POST["turbidity"]);
        $phlevel = test_input($_POST["phlevel"]);
        $nh3 = test_input($_POST["nh3"]);
        $device_number = test_input($_POST['device_number']);
        $timestamp = date("Y-m-d H:i:s");

        // Create a connection to the database
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to get current active parameters for the given device_number
        $sql = "SELECT minTemperature, maxTemperature, minTurbidity, maxTurbidity, minPh, maxPh, minNh3, maxNh3 
                FROM sensor_parameters 
                WHERE is_active = 1 AND device_number = ?";
        
        // Prepare statement to avoid SQL injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $device_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the parameters
            $active_param = $result->fetch_assoc(); // Assuming only one set of active parameters is needed

            // Insert the sensor data without converting turbidity
            $sql_insert = "INSERT INTO sensor_data (timestamp, Temperature, Turbidity, pH, NH3_concentration, device_number)
                           VALUES (?, ?, ?, ?, ?, ?)";

            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssssss", $timestamp, $temperature, $turbidity, $phlevel, $nh3, $device_number);

            if ($stmt_insert->execute()) {
                // Check if data is out of bounds
                $isOutOfBounds = false;
                $message = "";
                $sms_message = "";

                // Convert turbidity to integer for bounds checking
                $turbidity_int = preprocessTurbidity($turbidity);

                // Check bounds for each parameter
                $messages = [];
                $sms_messages = [];

                // Temperature
                if ($temperature < $active_param['minTemperature']) {
                    $messages[] = 'Temperature is too low.';
                    $sms_messages[] = "ALERT: Temperature too low!";
                } elseif ($temperature > $active_param['maxTemperature']) {
                    $messages[] = 'Temperature is too high.';
                    $sms_messages[] = "ALERT: Temperature too high!";
                }

                // Turbidity
                if ($turbidity_int < $active_param['minTurbidity']) {
                    $messages[] = 'Turbidity is too low.';
                    $sms_messages[] = "ALERT: Water Clarity is too clear!";
                } elseif ($turbidity_int > $active_param['maxTurbidity']) {
                    $messages[] = 'Turbidity is too high.';
                    $sms_messages[] = "ALERT: Water Clarity is not clear!";
                }

                // pH Level
                if ($phlevel < $active_param['minPh']) {
                    $messages[] = 'pH level is too low.';
                    $sms_messages[] = "ALERT: pH level too low!";
                } elseif ($phlevel > $active_param['maxPh']) {
                    $messages[] = 'pH level is too high.';
                    $sms_messages[] = "ALERT: pH level too high!";
                }

                // NH3 Concentration
                if ($nh3 < $active_param['minNh3']) {
                    $messages[] = 'NH3 concentration is too low.';
                    $sms_messages[] = "ALERT: NH3 concentration too low!";
                } elseif ($nh3 > $active_param['maxNh3']) {
                    $messages[] = 'NH3 concentration is too high.';
                    $sms_messages[] = "ALERT: NH3 concentration too high!";
                }

                // If any reading is out of bounds, insert notification into the database
                if (!empty($messages)) {
                    $full_message = implode(' ', $messages);
                    $full_sms_message = implode(' ', $sms_messages);

                    // Prepare and insert notification
                    $device_number_escaped = $conn->real_escape_string($device_number);
                    $message_escaped = $conn->real_escape_string($full_message);
                    $sql_insert_notification = "INSERT INTO notifications (message, device_number, is_read, created_at) 
                                                VALUES (?, ?, 0, NOW())";

                    $stmt_insert_notification = $conn->prepare($sql_insert_notification);
                    $stmt_insert_notification->bind_param("ss", $message_escaped, $device_number_escaped);
                    if ($stmt_insert_notification->execute()) {
                        // Send SMS with a brief message
                        send_sms_to_all($full_sms_message, $device_number);  // Pass both sms_message and device_number

                        // Return JSON response to indicate the error and message
                        echo json_encode(["status" => "error", "message" => $full_message]);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Error inserting notification: " . $conn->error]);
                    }
                } else {
                    // No out of bounds, return a success message if needed
                    echo json_encode(["status" => "success", "message" => "Data inserted successfully"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Error inserting sensor data: " . $conn->error]);
            }

            // Check if it's the end of the day and send a summary email
            $current_time = date("H:i");
            if ($current_time === "00:00") { // 12 am
                include 'summaryemail.php';
            }
            if ($current_time === "18:00") { // 6 pm
                include 'summaryemail.php';
            }
        } else {
            // Return an empty JSON if no active parameters are found
            echo json_encode(["status" => "error", "message" => "No active parameters found"]);
        }

    } else {
        echo json_encode(["status" => "error", "message" => "Wrong API Key provided."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No data posted with HTTP POST."]);
}


// Function to send SMS to all devices
function send_sms_to_all($sms_message, $device_number) {
    // Twilio API credentials
    $sid = 'ACd5f3e3e0bc2ea5622c0c7ed9f14116b2';  // Replace with your Twilio Account SID
    $token = '681e515f7b0ae4270ba7228cfe3bc8db'; // Replace with your Twilio Auth Token
    $twilioNumber = '+12542218740'; // Replace with your Twilio phone number

    // Include Twilio SDK
    require 'twillio\twilio-php-main\src\Twilio\autoload.php'; // Adjust this path if needed

    // Create a new Twilio client
    $client = new \Twilio\Rest\Client($sid, $token);

    // Query to get the device_number and phone number for the users table
    global $conn;
    $sql = "SELECT device_number, contact_number FROM users WHERE device_number = '$device_number'";  // Check if the device_number matches and user is active
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Loop through all matching users and send an SMS to each one
        while ($row = $result->fetch_assoc()) {
            // Use the user's phone number
            $contactNumber = '+63' . substr($row['contact_number'], 1); // Ensure proper phone format (assuming Philippine numbers)

            // Send the message
            try {
                $client->messages->create(
                    $contactNumber, // Recipient's phone number
                    [
                        'from' => $twilioNumber, // Your Twilio number
                        'body' => $sms_message // Message content
                    ]
                );
            } catch (Exception $e) {
                echo "Error sending SMS: " . $e->getMessage();
            }
        }
    }
}

// Function to convert turbidity text to numeric value (integer) for bounds checking
function preprocessTurbidity($turbidity) {
    switch ($turbidity) {
        case "Very Clear":
            return 1;
        case "Clear":
            return 2;
        case "Moderate":
            return 3;
        case "Slightly Muddy":
            return 4;
        case "Very Muddy":
            return 5;
        default:
            return 0; // Invalid turbidity
    }
}

// Simple input validation function
function test_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

?>

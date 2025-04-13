<?php
require 'PHPmailer/PHPMailer-master/src/PHPMailer.php';
require 'PHPmailer/PHPMailer-master/src/SMTP.php';
require 'PHPmailer/PHPMailer-master/src/Exception.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "aquanet1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get today's summary
function get_daily_summary($conn) {
    $today = date('Y-m-d'); // Get today's date
    
    // SQL query to fetch sensor data only for today
    $sql = "SELECT device_number, 
                   Temperature, 
                   Turbidity, 
                   pH, 
                   NH3_concentration
            FROM sensor_data
            WHERE DATE(timestamp) = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Initialize variables for calculating the averages
    $temperatureSum = 0;
    $turbiditySum = 0;
    $phSum = 0;
    $nh3Sum = 0;
    $count = 0;
    
    $summary = "Daily Summary for $today:\n";

    // If there is data for today, calculate the averages
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $temperatureSum += is_numeric($row['Temperature']) ? $row['Temperature'] : 0;
            $turbiditySum += is_numeric($row['Turbidity']) ? $row['Turbidity'] : 0;
            $phSum += is_numeric($row['pH']) ? $row['pH'] : 0;
            $nh3Sum += is_numeric($row['NH3_concentration']) ? $row['NH3_concentration'] : 0;
            $count++;
        }
        
        // Calculate the averages
        if ($count > 0) {
            $averageTemperature = round($temperatureSum / $count, 2);
            $averageTurbidity = round($turbiditySum / $count, 2);
            $averagePh = round($phSum / $count, 2);
            $averageNH3 = round($nh3Sum / $count, 2);

            // Add the averages to the summary message
            $summary .= "Average Temperature: $averageTemperature °C\n";
            $summary .= "Average Turbidity: $averageTurbidity NTU\n";
            $summary .= "Average pH: $averagePh\n";
            $summary .= "Average NH3 Concentration: $averageNH3 mg/L\n";
        }
    } else {
        $summary .= 'No data available for today.';
    }

    return $summary;
}

// Fetch the daily summary
$summary = get_daily_summary($conn);

// SQL to get all user emails
$sql = "SELECT email FROM users";
$result = $conn->query($sql);

// Set up PHPMailer to send the email
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'aquanet526@gmail.com';  
$mail->Password = 'hahrsnuqfaxxnlzb';  // Use your app-specific password if using 2FA
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

// Set email sender
$mail->setFrom('aquanet526@gmail.com', 'AquaNet');
$mail->Subject = 'Daily Sensor Data Summary';
$mail->Body = nl2br($summary); // Convert newlines to <br> for HTML formatting

// Loop through all users and send the email
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $email = $row['email'];

        // Add recipient's email address
        $mail->addAddress($email);

        // Send the email
        if(!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo . "<br>";
        } else {
            echo "Daily summary email sent to $email!<br>";
        }

        // Clear the addresses to send the next email
        $mail->clearAddresses();
    }
} else {
    echo 'No users found in the database.';
}

// Close the database connection
$conn->close();
?>

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
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get today's summary
// Function to determine turbidity description
function get_turbidity_description($turbidity) {
    if ($turbidity < 1) {
        return "Very Clear";
    } elseif ($turbidity >= 1 && $turbidity < 5) {
        return "Clear";
    } elseif ($turbidity >= 5 && $turbidity < 10) {
        return "Moderate";
    } elseif ($turbidity >= 10 && $turbidity < 20) {
        return "Slightly Muddy";
    } else {
        return "Very Muddy";
    }
}

// Modify the get_daily_summary function to include turbidity descriptions
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
    
    $summary = "<h2>Daily Summary for $today:</h2>"; // Use larger header for the title

    // If there is data for today, calculate the averages and provide descriptions
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

            // Get turbidity description based on average turbidity
            $turbidityDescription = get_turbidity_description($averageTurbidity);

            // Add the averages and turbidity description to the summary message with styling
            $summary .= "<b>Average Temperature:</b> <span style='font-size: 16px;'>$averageTemperature °C</span><br>";
            $summary .= "<b>Average Turbidity:</b> <span style='font-size: 16px;'>$turbidityDescription ($averageTurbidity NTU)</span><br>";
            $summary .= "<b>Average pH:</b> <span style='font-size: 16px;'>$averagePh</span><br>";
            $summary .= "<b>Average NH3 Concentration:</b> <span style='font-size: 16px;'>$averageNH3 mg/L</span><br>";
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

// Set a subject line with attention-grabbing text
$mail->Subject = 'Daily Sensor Data Summary';

// Set the email body in HTML format
$mail->isHTML(true);
$mail->Body = $summary; // The body is now set to the formatted HTML content

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

<?php
require 'db_config.php'; // Include database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $deviceNumber = $_POST['deviceNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $user_type = $_POST['user_type'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        header('Location: register.php?status=error&message=Passwords do not match');
        exit;
    }

    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        header('Location: register.php?status=error&message=Username already exists');
        exit;
    }

    // Hash the password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    $sql = "INSERT INTO users (username, email, contact_number, device_number, password_hash, user_type) 
            VALUES (:username, :email, :contact, :deviceNumber, :passwordHash, :user_type)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':contact', $contact);
    $stmt->bindParam(':deviceNumber', $deviceNumber);
    $stmt->bindParam(':passwordHash', $passwordHash);
    $stmt->bindParam(':user_type', $user_type); // Bind the user_type

    if ($stmt->execute()) {
        header('Location: register.php?status=success&message=Welcome, ' . urlencode($username));
    } else {
        header('Location: register.php?status=error&message=An error occurred while registering your account');
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AquaNet</title>
    <link rel="icon" href="../aquaneticon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            width: 90%;
            max-width: 800px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .register-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-row .input-field {
            margin-bottom: 15px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #f8f8f8;
            width: 100%;
            position: fixed;
            bottom: 0;
            left: 0;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 668px) {
            .form-row {
                flex-direction: column;
            }
        }

        @media (max-width: 500px) {
            .register-container {
                width: 100%;
                padding: 15px;
                margin: 0 5px;
            }

            .footer {
                font-size: 14px; /* Ensures text size remains consistent */
                position: fixed; /* Fixed positioning to keep it at the bottom */
                bottom: 0; /* Aligns it to the bottom of the viewport */
                left: 0; /* Aligns it to the left edge */
                width: 100%; /* Ensures it spans the full width of the viewport */
                background-color: #f8f8f8; /* Background color for visibility */
                padding: 10px; /* Padding for spacing inside the footer */
                text-align: center; /* Center aligns text */
                box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1); /* Optional shadow for better separation */
                z-index: 1000; /* Ensures it stays on top of other content */
                box-sizing: border-box; /* Ensures padding is included in the width calculation */
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create Profile</h2>
        <form id="registerForm" action="register.php" method="post">
            <div class="row form-row">
                <div class="col s12 m6">
                    <div class="input-field">
                        <input type="text" id="registerUsername" name="username" class="validate" required>
                        <label for="registerUsername">Username</label>
                    </div>
                    <div class="input-field">
                        <input type="email" id="registerEmail" name="email" class="validate" required>
                        <label for="registerEmail">Email address</label>
                    </div>
                    <div class="input-field">
                        <input type="tel" id="registerContact" name="contact" class="validate" required>
                        <label for="registerContact">Contact Number</label>
                    </div>
                </div>
                <div class="col s12 m6">

                    <div class="input-field">
                        <input type="text" id="registerDeviceNumber" name="deviceNumber" class="validate" required>
                        <label for="registerDeviceNumber">Device Number</label>
                    </div>
                    <div class="input-field">
                        <input type="password" id="registerPassword" name="password" class="validate" required>
                        <label for="registerPassword">Password</label>
                    </div>
                    <div class="input-field">
                        <input type="password" id="registerConfirmPassword" name="confirmPassword" class="validate" required>
                        <label for="registerConfirmPassword">Confirm Password</label>
                    </div>
                </div>
                <input type="hidden" id="registerUserType" name="user_type" value="user">
            </div>
            <div class="btn-container">
                <button type="submit" class="btn waves-effect waves-light btn-large">Create Profile</button>
            </div>
            <br>
            <div class="center-align mt-3">
                <a href="login.php">Already have an account? Login</a>
            </div>
        </form>
    </div>
    <br><br><br>

    <footer class="footer">
        <p class="card-text"><b>AquaNet Project 2024</b></p>
    </footer>

    <script>
    $(document).ready(function(){
        M.updateTextFields();

        // Check URL for status and message query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = urlParams.get('message');

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Registered',
                text: decodeURIComponent(message),
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'login.php'; // Redirect to login page
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed',
                text: decodeURIComponent(message)
            });
        }
    });
    </script>
</body>
</html>

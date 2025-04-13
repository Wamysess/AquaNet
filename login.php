<?php
require 'db_config.php'; // Include database configuration

// Initialize variables for JavaScript
$loginStatus = '';
$loginMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to select user details including user_type and device_number
    $sql = "SELECT id, username, password_hash, user_type, device_number FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);

    // Bind parameter and execute
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        // Successful login
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['device_number'] = $user['device_number']; // Store device_number in the session

        if ($user['user_type'] === 'admin') {
            // Redirect to admin dashboard if the user is an admin
            header('Location: admin/admin_dashboard.php');
        } else {
            // Redirect to user charts page if the user is not an admin
            header('Location: charts.php');
        }

        exit; // Stop further script execution after redirection
    } else {
        // Failed login
        $loginStatus = 'error';
        $loginMessage = 'Invalid username or password.';
        header('Location: login.php?status=error'); // Redirect to avoid form resubmission
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - AquaNet</title>
    <link rel="icon" href="../img/aquaneticon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        .login-container {
            width: 90%;
            max-width: 400px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 0 10px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
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
            font-size: 14px; /* Fixed font size */
            z-index: 1000; /* Ensures it stays on top of other content */
            box-sizing: border-box; /* Includes padding in the width and height calculation */
        }

        @media (max-width: 600px) {
            .login-container {
                width: 100%;
                padding: 15px;
                margin: 0 5px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Sign In</h2>
        <form id="loginForm" action="login.php" method="post">
            <div class="input-field">
                <input type="text" id="loginUsername" name="username" class="validate" required>
                <label for="loginUsername">Username</label>
            </div>
            <div class="input-field">
                <input type="password" id="loginPassword" name="password" class="validate" required>
                <label for="loginPassword">Password</label>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn waves-effect waves-light btn-large">Sign In</button>
            </div>
            <br>
            <div class="center-align mt-3">
                <a href="register.php">Don't have an account? Register</a>
            </div>
        </form>
    </div>
    <br><br><br>
    <footer class="footer">
        <p class="card-text"><b>AquaNet Project 2024</b></p>
    </footer>

    <!-- Load JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
    $(document).ready(function(){
        M.updateTextFields();

        // Check URL for status query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        const message = status === 'success' ? 'Logged In' : 'Login Failed';
        const text = status === 'success' ? 'Welcome back!' : 'Invalid username or password.';

        if (status === 'success') {
            Swal.fire({
                icon: 'success',
                title: message,
                text: text,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'index.php'; // Redirect to a secure page
            });
        } else if (status === 'error') {
            Swal.fire({
                icon: 'error',
                title: message,
                text: text
            });
        }
    });
    </script>
</body>
</html>

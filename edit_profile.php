<?php
// User authentication
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include 'db_config.php';

// Fetch current user data
$username = $_SESSION["username"];
$email = '';
$contact_number = '';
$device_number = ''; // Initialize device_number

// Prepare and execute query to get user data
try {
    $stmt = $pdo->prepare("SELECT email, contact_number, device_number FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $email = $user['email'];
        $contact_number = $user['contact_number'];
        $device_number = $user['device_number']; // Fetch device_number
    }
} catch (PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Update user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_contact_number = $_POST['contact_number'];
    $new_device_number = $_POST['device_number']; // New device_number
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords
    if ($new_password !== $confirm_password) {
        $_SESSION['toastr_message'] = 'Passwords do not match.';
        $_SESSION['toastr_type'] = 'error';
    } else {
        // Prepare and execute update query
        try {
            // Only update the password if it's not empty
            $update_query = "UPDATE users SET username = :username, email = :email, contact_number = :contact_number, device_number = :device_number"; // Include device_number
            if (!empty($new_password)) {
                $update_query .= ", password = :password"; // Add password to update query
            }
            $update_query .= " WHERE username = :old_username";

            $stmt = $pdo->prepare($update_query);
            $params = [
                'username' => $new_username,
                'email' => $new_email,
                'contact_number' => $new_contact_number,
                'device_number' => $new_device_number, // Add to params
                'old_username' => $username,
            ];

            // Only add the password to params if it's not empty
            if (!empty($new_password)) {
                $params['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }

            $stmt->execute($params);

            // Update session username
            $_SESSION['username'] = $new_username;
            $_SESSION['toastr_message'] = 'Profile updated successfully.';
            $_SESSION['toastr_type'] = 'success';
            header("Refresh:0"); // Refresh to update displayed username
            exit(); // Exit after redirect
        } catch (PDOException $e) {
            $_SESSION['toastr_message'] = "Error updating profile: " . $e->getMessage();
            $_SESSION['toastr_type'] = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f4f7f9; /* Soft background color */
            color: #333;
            line-height: 1.6; /* Better text readability */
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand img {
            border-radius: 50%;
        }
        .navbar-nav .nav-link {
            font-size: 16px;
        }
        .navbar-nav .nav-link.active {
            font-weight: bold;
        }
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        /* Add a style for the Toastr container */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050; /* Ensure it appears above other content */
        }
        footer {
            margin-top: 30px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        .footer .card-text {
            font-size: 14px;
        }
        .notification-container {
            position: relative;
        }

        #notification-bell {
            position: relative;
            font-size: 19px;
        }

        #notification-dropdown {
            position: absolute;
            top: 100%; /* Position it below the button */
            width: 300px; /* Default width */
            max-height: 400px;
            background-color: white;
            box-shadow: 0 10px 16px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000; /* Ensure dropdown appears above other content */
            margin: 0 auto; /* Center dropdown horizontally */
        }

        /* Adjust for smaller screens */
        @media (max-width: 768px) {
            #notification-dropdown {
                left: auto; /* Lock it to the left side with a 20px margin */
                right: -20px; /* Remove any right positioning */
                width: 300; /* Adjust width to fit within the screen with a margin */
                z-index: 1000; /* Ensure dropdown appears above other content */
            }
        }

        .badge {
            background-color: #dc3545;
            color: white;
            font-size: -8px;
            border-radius: 50%;
            padding: 2px 6px; /* Adjust the size of the badge */
            top: -5px; /* Adjust this value to move the badge up */
            right: -5px; /* Adjust this value to move the badge to the right */
            /* You can add additional styles if needed */
        }

        .dropdown-header h6 {
                margin: 0;
                font-size: 16px;
            }

        .navbar-toggler {
            margin-left: 10px;
        }

        /* Adjustments for larger screens */
        @media (min-width: 992px) {
            .notification-container {
                flex-grow: 1;
                justify-content: flex-end;
            }
            .navbar-toggler {
                margin-left: 20px;
            }
        }

        /* Hide the navbar-toggler on medium screens and larger */
        @media (min-width: 768px) {
            .navbar-toggler {
                display: none;
            }
        }

        /* Additional styles for handling the navbar visibility */
        .navbar-collapse.collapse.show {
            display: block !important;
        }

        #notification-list {
            max-height: 300px; /* Adjust height as needed */
            overflow-y: auto; /* Add vertical scrolling */
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .notification {
            padding: 11px;
            margin: 10px 0;
        }

        .notification-separator {
            border: none;
            border-top: 1px solid #ddd;
            margin: 5px 0;
        }

        .day-separator {
            background-color: #f5f5f5;
            border: none;
            text-align: center;
            margin: 10px 0;
        }

        .day-separator-text {
            padding-top: 10px;
            font-weight: bold;
            color: #555;
            text-align: center;
            font-size: 16px;
        }

        .notification-date {
            font-size: 0.8em;
            color: #888;
            float: right;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="charts.php">
        <img src="img/aquanet.png" width="30" height="30" alt="aquanet"> AquaNet
    </a>

    <div class="d-flex align-items-center ml-auto">
        <!-- Notification Button -->
        <div class="notification-container mr-3">
            <button id="notification-bell" class="btn btn-link position-relative">
                <i class="fas fa-bell"></i>
                <span class="badge badge-danger position-absolute" style="display: none;">0</span>
            </button>
            <!-- Notification Dropdown -->
            <div id="notification-dropdown" style="display: none;">
                <div class="dropdown-header">
                    <h6>Notifications</h6>
                </div>
                <ul id="notification-list"></ul>
            </div>
        </div>

        <!-- Navbar Toggler -->
        <button class="navbar-toggler ml-2" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="charts.php"><b>Dashboard</b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="parameter.php"><b>Parameters</b></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="readings.php"><b>History</b></a>
            </li>
        </ul>
    </div>
    <!-- Logged-in Username with Dropdown -->
    <div class="dropdown push-right">
        <button class="btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </i> <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="edit_profile.php">Edit Profile</a>
            <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="form-container">
    <h2>Edit Profile</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?php echo htmlspecialchars($contact_number); ?>" required>
        </div>
        <div class="form-group">
            <label for="device_number">Device Number</label>
            <input type="text" class="form-control" id="device_number" name="device_number" value="<?php echo htmlspecialchars($device_number); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<footer class="footer">
    <div class="card-body text-center">
        <p class="card-text">AquaNet &copy; 2024</p>
    </div>
</footer>

<script>
    // Toastr configuration
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<script>
    $(document).ready(function() {
        var toastrMessage = "<?php echo isset($_SESSION['toastr_message']) ? addslashes($_SESSION['toastr_message']) : ''; ?>";
        var toastrType = "<?php echo isset($_SESSION['toastr_type']) ? $_SESSION['toastr_type'] : ''; ?>";

        // Debugging output
        console.log("Toastr Message: ", toastrMessage);
        console.log("Toastr Type: ", toastrType);

        if (toastrMessage) {
            if (toastrType && typeof toastr[toastrType] === "function") {
                toastr[toastrType](toastrMessage);
            }
            // Clear the session variables
            <?php unset($_SESSION['toastr_message']); unset($_SESSION['toastr_type']); ?>
        }

        function fetchNotifications() {
                $.ajax({
                    url: '../index/fetch_notifications.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {

                        var notificationsList = $('#notification-list');
                        var limitedData = data.slice(0, 200); // Limit to 200 notifications

                        // Clear existing notifications
                        notificationsList.empty();

                        // Initialize variables to keep track of the current day
                        var currentDay = '';

                        // Dynamic CSS for day separator and notifications
                        var style = `
                            <style>
                                .notification {
                                    display: flex;
                                    align-items: center;
                                    padding: 10px;
                                    border-radius: 4px;
                                    margin: 5px 0;
                                    cursor: pointer;
                                    position: relative;
                                }
                                .notification-unread {
                                    background-color: #f8f9fa;
                                    font-weight: bold;
                                }
                                .notification-read {
                                    background-color: #e9ecef;
                                }
                                .notification-date {
                                    color: #6c757d;
                                    font-size: 0.8em;
                                    margin-left: 10px;
                                }
                                .day-separator-text {
                                    font-weight: bold;
                                    margin: 10px 0;
                                }
                                .notification-separator {
                                    border: none;
                                    border-top: 1px solid #dee2e6;
                                    margin: 5px 0;
                                }
                                .day-separator {
                                    border: none;
                                    border-top: 2px solid #adb5bd;
                                    margin: 10px 0;
                                }
                                .notification-unread::before {
                                    content: '';
                                    display: block;
                                    width: 8px;
                                    height: 8px;
                                    background-color: red;
                                    border-radius: 50%;
                                    position: absolute;
                                    left: 10px;
                                    top: 50%;
                                    transform: translateY(-50%);
                                }
                            </style>
                        `;
                        // Append style to head
                        $('head').append(style);
                        // Create an array to keep track of unread notifications
                        var unreadNotifications = [];
                        limitedData.forEach(function(notification, index) {
                            // Get the notification date
                            var notificationDate = new Date(notification.created_at);
                            var notificationDay = notificationDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

                            // Check if the day has changed
                            if (notificationDay !== currentDay) {
                                // Add a day separator if the day has changed
                                if (currentDay !== '') {
                                    notificationsList.append('<hr class="day-separator">');
                                }
                                currentDay = notificationDay;

                                // Add the day separator text
                                var daySeparatorElement = $('<li class="day-separator-text"></li>')
                                    .text(notificationDay);
                                notificationsList.append(daySeparatorElement);
                            }
                            // Create a list item for each notification
                            var notificationElement = $('<li></li>')
                                .addClass('notification')
                                .addClass(notification.is_read === 0 ? 'notification-unread' : 'notification-read') // Highlight unread notifications
                                .text(notification.message)
                                .append('<span class="notification-date">' + notificationDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) + '</span>');

                            notificationsList.append(notificationElement);

                            // Add a separator line after each notification except the last one
                            if (index < limitedData.length - 1) {
                                var separator = $('<hr class="notification-separator">');
                                notificationsList.append(separator);
                            }

                            // Add the notification ID to the unread notifications array if it's unread
                            if (notification.is_read === 0) {
                                unreadNotifications.push(notification.id);
                            }
                        });
                        // Update notification count badge
                        updateUnreadBadgeCount();

                        // Handle marking notifications as read when the user exits the dropdown
                        $('#notification-dropdown').mouseleave(function() {
                            if (unreadNotifications.length > 0) {
                                markNotificationsAsRead(unreadNotifications);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching notifications:', error);
                    }
                });
            }
            // Function to fetch unread notifications
            function fetchUnreadNotifications() {
                return $.ajax({
                    url: '../index/is_read.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Log the data to check what is being received
                        console.log('Fetched unread notifications:', data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching unread notifications:', error);
                    }
                });
            }
            // Optionally, close the dropdown if clicked outside
                $(document).click(function(event) {
                    if (!$(event.target).closest('#notification-bell, #notification-dropdown').length) {
                        $('#notification-dropdown').hide();
                        $('#notification-bell').removeClass('active').addClass('inactive');
                    }
                });
                // Automatically mark all notifications as read when leaving the dropdown
                $('#notification-dropdown').mouseleave(function() {
                    fetchUnreadNotifications().done(function(unreadData) {
                        var unreadIds = unreadData.map(function(notification) { return notification.id; });
                        if (unreadIds.length > 0) {
                            markNotificationsAsRead(unreadIds);
                        }
                    });
                });
                // Fetch notifications initially and set interval
                fetchNotifications();
                setInterval(fetchNotifications, 1000); // Adjust interval as needed
            // Function to update unread badge count
            function updateUnreadBadgeCount() {
                fetchUnreadNotifications().done(function(unreadData) {
                    var unreadCount = unreadData.length;
                    var badge = $('#notification-bell .badge');
                    badge.text(unreadCount); // Set badge text to unread count

                    // Show or hide the badge
                    badge.toggle(unreadCount > 0);

                    // Log unread count for debugging
                    console.log('Unread notification count:', unreadCount);
                });
            }
            // Function to toggle the notification dropdown
            function toggleNotificationDropdown() {
                var dropdown = $('#notification-dropdown');
                var bell = $('#notification-bell');
                
                if (dropdown.is(':visible')) {
                    dropdown.hide();
                    bell.removeClass('active').addClass('inactive');
                } else {
                    dropdown.show();
                    bell.removeClass('inactive').addClass('active');
                    
                    // Fetch and display notifications when the dropdown is opened
                    fetchNotifications();
                }
            }
            // Function to mark notifications as read
            function markNotificationsAsRead(notificationIds) {
                $.ajax({
                    url: '../index/mark_notifications_read.php',
                    type: 'POST',
                    data: { ids: JSON.stringify(notificationIds) }, // Send array as JSON string
                    success: function(response) {
                        console.log('Response:', response);
                        // Refresh notifications
                        fetchNotifications();
                        // Update badge count after notifications are marked as read
                        updateUnreadBadgeCount();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking notifications as read:', error);
                    }
                });
            }
            // Event listener for the notification bell
            $('#notification-bell').click(function() {
                toggleNotificationDropdown();
            });
    });
</script>
</script>
</body>
</html>

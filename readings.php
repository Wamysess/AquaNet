<?php
//user authentication if he/she is logged-in, if not then redirect user to login page
session_start();
if (!isset($_SESSION["username"])){
header("Location: login.php");
exit(); }
?>
<?php
include 'db_config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="icon" href="../aquaneticon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
        table {
            width: 100%;
            border-collapse: collapse;
            color: #212529;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #e9ecef;
            font-weight: 600;
        }
        .data-table-container {
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .export-icons {
            margin-top: 20px;
        }
        .export-icons i {
            cursor: pointer;
            font-size: 24px;
            color: #007bff;
            margin-right: 10px;
        }
        .export-icons i:hover {
            color: #0056b3;
        }
        .export-icons i.active {
            color: #28a745;
        }
        .pagination-container {
            margin-top: 20px;
            text-align: center;
        }
        .pagination {
            display: inline-block;
            white-space: nowrap; /* Prevents line breaks within pagination */
        }
        .pagination a {
            color: #007bff;
            padding: 8px 16px;
            text-decoration: none;
            margin: 0 2px;
            border-radius: 4px;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        /* Adjusting pagination display for smaller screens */
        @media (max-width: 768px) {
            .pagination a {
                padding: 6px 12px;
                font-size: 14px;
            }

            .pagination a.active {
                padding: 6px 12px;
                font-size: 14px;
            }
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

        /* Custom Toastr CSS */
        .toast-top-right {
            top: 70px !important;
        }

        #dataTableContainer {
            max-width: 800px; /* Limit container width */
            margin: 20px auto; /* Center the container */
            padding: 20px;
            background-color: #ffffff; /* White background for the main container */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        .date-group {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #e0e0e0; /* Lighter border */
            border-radius: 10px;
            background-color: #fafafa; /* Light background for each group */
            transition: background-color 0.3s, box-shadow 0.3s; /* Smooth transition effects */
        }

        .date-group:hover {
            background-color: #e9f5ff; /* Light blue on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* More prominent shadow on hover */
        }

        .date-group h4 {
            color: #007bff; /* Bootstrap primary color */
            margin-bottom: 10px;
            font-size: 1.25em; /* Slightly larger font size */
        }

        .date-group p {
            margin: 5px 0;
            font-size: 15px; /* Slightly larger font size */
        }

        strong {
            color: #333; /* Dark color for strong emphasis */
        }

        /* Add icons for readings (using Font Awesome or similar) */
        .icon {
            font-size: 1.2em;
            margin-right: 5px;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 600px) {
            #dataTableContainer {
                padding: 15px;
            }
            .date-group {
                padding: 15px;
            }
            .date-group h4 {
                font-size: 1.1em;
            }
        }

        #pagination {
            display: flex;
            justify-content: center; /* Center the pagination */
            align-items: center; /* Center vertically */
            padding: 10px; /* Add some padding */
            margin-top: 20px; /* Space above the pagination */
        }

        .page-link {
            display: inline-block; /* Make links inline */
            margin: 0 5px; /* Space between links */
            padding: 8px 12px; /* Padding around each link */
            border: 1px solid #007bff; /* Border color */
            border-radius: 5px; /* Rounded corners */
            color: #007bff; /* Text color */
            text-decoration: none; /* Remove underline */
            transition: background-color 0.3s; /* Smooth background change */
        }

        .page-link:hover {
            background-color: #007bff; /* Background color on hover */
            color: white; /* Change text color on hover */
        }

        .page-link.active {
            background-color: #007bff; /* Active page background */
            color: white; /* Active page text color */
        }

        /* General styles for containers */
        .date-group {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .date-header {
            font-size: 1.2em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .summary {
            margin-left: 10px;
            font-size: 1em;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }

        /* Add scrollable table for small devices */
        .raw-data {
            overflow-x: auto;
        }

        /* Adjust for smaller devices */
        @media screen and (max-width: 768px) {
            .date-header {
                font-size: 1.1em;
                flex-direction: column;
                align-items: flex-start;
            }

            .summary {
                font-size: 0.9em;
                margin-left: 0;
            }

            table th, table td {
                font-size: 0.9em;
            }

            /* Make the table scrollable */
            .raw-data table {
                width: 100%; /* Full width for better scrolling */
            }

            /* Reduce padding on smaller screens */
            .date-group {
                padding: 8px;
            }

            .date-header {
                padding: 5px 0;
            }
        }

        /* Even smaller screens (e.g., phones) */
        @media screen and (max-width: 480px) {
            .date-header {
                font-size: 1em;
            }

            .summary {
                font-size: 0.85em;
            }

            table th, table td {
                font-size: 0.8em;
                padding: 6px;
            }
        }

        .custom-gap button {
            margin-right: 15px; /* Adjust spacing between buttons as needed */
        }
        .custom-gap button:last-child {
            margin-right: 0; /* Remove right margin from the last button */
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

<div class="container data-table-container">
    <h2 class="text-center mt-4 mb-4">Sensor Data Readings</h2>
    <div class="export-icons text-center mb-3 d-flex justify-content-center custom-gap">
        <!-- Button for Exporting Current Page to Excel -->
        <button class="btn btn-primary" id="exportExcel" title="Export Current Page to Excel">Export Current Page
        </button>
        
        <!-- Button for Exporting All Data to Excel -->
        <button class="btn btn-primary" id="exportAllExcel" title="Export All Data to Excel">Export All Data
        </button>
    </div>
    <div id="dataTableContainer" class="table-responsive"></div>
    <div class="pagination-container">
        <div class="pagination" id="pagination"></div>
    </div>
</div>

    <footer class="footer">
        <div class="card-body text-center">
            <p class="card-text"><b>AquaNet Project 2024</b></p>
        </div>
    </footer>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    var autoReloadInterval;
    var sensorData = [];

    function fetchData() {
        console.log('Fetching data...');
        $.ajax({
            url: 'sensors/get_data_chart.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log('Data fetched:', data);
                if (data.length > 0) {
                    sensorData = data;
                    renderTable(); // Render the table without pagination
                } else {
                    $('#dataTableContainer').html('<p class="text-center">No data available.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    function renderTable() {
        var dataTableContainer = '';

        // Group readings by date
        var readingsByDate = {};
        const turbidityMapping = {
            'Very Clear': 1,
            'Clear': 2,
            'Moderate': 3,
            'Slightly Muddy': 4,
            'Very Muddy': 5
        };

        sensorData.forEach(function(reading) {
            var date = new Date(reading.timestamp).toLocaleDateString();
            if (!readingsByDate[date]) {
                readingsByDate[date] = [];
            }
            readingsByDate[date].push(reading);
        });

        var sortedDates = Object.keys(readingsByDate).sort((a, b) => new Date(b) - new Date(a));

        sortedDates.forEach(function(date) {
            var readings = readingsByDate[date];

            dataTableContainer += `
                <div class="date-group">
                    <h4 class="date-header" data-date="${date}" style="cursor: pointer;">
                        <i class="fas fa-calendar-alt icon"></i> ${date}
                    </h4>
                    <div class="summary">
                        <p><strong><i class="fas fa-thermometer-half icon"></i> Average Temperature:</strong> ${calculateAverage(readings, 'Temperature')} °C</p>
                        <p><strong><i class="fas fa-water icon"></i> Average Turbidity:</strong> ${calculateTurbidityDescription(readings, turbidityMapping)}</p>
                        <p><strong><i class="fas fa-flask icon"></i> Average pH:</strong> ${calculateAverage(readings, 'pH')}</p>
                        <p><strong><i class="fas fa-tint icon"></i> Average NH3 Concentration:</strong> ${calculateAverage(readings, 'NH3_concentration')}</p>
                    </div>
                    <div class="raw-data" style="display: none;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Temperature (°C)</th>
                                    <th>Turbidity</th>
                                    <th>pH</th>
                                    <th>NH3 Concentration</th>
                                </tr>
                            </thead>
                            <tbody>`;

            readings.forEach(reading => {
                dataTableContainer += `
                    <tr>
                        <td>${new Date(reading.timestamp).toLocaleTimeString()}</td>
                        <td>${reading.Temperature}</td>
                        <td>${reading.Turbidity}</td>
                        <td>${reading.pH}</td>
                        <td>${reading.NH3_concentration}</td>
                    </tr>`;
            });

            dataTableContainer += `
                            </tbody>
                        </table>
                    </div>
                </div>`;
        });

        $('#dataTableContainer').html(dataTableContainer);

        $(document).on('click', '.date-header', function() {
            $(this).nextAll('.raw-data').slideToggle();
        });
    }

    // Helper function to calculate averages
    function calculateAverage(readings, key) {
        var total = 0;
        var validCount = 0;

        readings.forEach(function(reading) {
            var value = parseFloat(reading[key]);
            if (!isNaN(value)) {
                total += value;
                validCount++;
            }
        });

        return validCount > 0 ? (total / validCount).toFixed(2) : 'No data';
    }

    // Helper function to calculate average turbidity and return description
    function calculateTurbidityDescription(readings, turbidityMapping) {
        var totalTurbidity = 0;
        var validTurbidityCount = 0;

        readings.forEach(function(reading) {
            var turbidity = turbidityMapping[reading.Turbidity];
            if (turbidity) {
                totalTurbidity += turbidity;
                validTurbidityCount++;
            }
        });

        var averageTurbidity = validTurbidityCount > 0 ? (totalTurbidity / validTurbidityCount).toFixed(0) : 'No data';
        return averageTurbidity === 'No data' ? 'No data' : Object.keys(turbidityMapping).find(key => turbidityMapping[key] == averageTurbidity);
    }


        function exportTableToExcel(tableID, filename) {
            var wb = XLSX.utils.table_to_book(document.getElementById(tableID), {sheet: "Sheet1"});
            XLSX.writeFile(wb, filename + ".xlsx");
        }

        function exportAllDataToExcel(filename) {
            var ws = XLSX.utils.json_to_sheet(sensorData);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Sensor Data");
            XLSX.writeFile(wb, filename + ".xlsx");
        }

        function showToast(message, type = 'success') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
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
            toastr[type](message);
        }

        $(document).ready(function() {
            fetchData();
            $('#exportExcel').click(function() {
                exportTableToExcel('dataTable', 'current_page_data');
                showToast('Current page data exported to Excel.');
            });

            $('#exportAllExcel').click(function() {
                exportAllDataToExcel('all_data');
                showToast('All data exported to Excel.');
            });

            $('#pagination').on('click', 'a.page-link', function(event) {
                event.preventDefault();
                var page = $(this).data('page');
                currentPage = page;
                renderTable(currentPage);
                renderPagination();
            });


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
</body>
</html>

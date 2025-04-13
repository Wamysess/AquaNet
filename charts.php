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
    <title>AquaNet</title>
    <link rel="icon" href="aquaneticon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f0f4f8;
            color: #333;
        }

        .status-message {
            font-size: 20px;
            font-weight: bold;
        }

        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center; /* Ensure vertical alignment */
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

        .chart-toggle-btn {
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .chart-toggle-btn.active {
            background-color: #28a745; /* Green for active state when chart is shown */
            color: #fff;
        }

        .chart-toggle-btn:hover {
            background-color: #007bff; /* Slightly darker blue on hover */
            color: #fff;
        }

        .chart-container {
            width: 100%;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .legend-container {
            margin-top: 10px;
        }

        .legend-item {
            display: inline-block;
            margin: 5px;
            padding: 5px;
            font-size: 12px;
        }

        .square-chart {
            position: relative;
            width: 100%;
            padding-bottom: 100%; /* Makes the container square */
        }
        .square-chart canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .legend-container {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }

        .legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }

        .legend-item {
            display: inline-block;
            margin-right: 20px;
            font-size: 12px;
        }

        .legend-color {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        /* Media query for smaller screens */
        @media (max-width: 767px) {
            .chart-container {
                height: 100%;
                width: 100%;
            }
            
            .legend-container {
                margin-top: 10px;
                margin-bottom: 10px;
            }
        }


        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #ffffff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 80%;
            max-width: 800px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: 600;
        }

        .footer {
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .footer .card-text {
            font-size: 15px;
            margin: 0;
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

        .parameter-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .parameter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.2);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
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



<div class="container-fluid">
    <div class="row">
        <main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-4">
            <h2 class="text-center mt-4 mb-4 title animated-title">AquaNet Aquarium Water Monitor</h2>
            <div id="currentParameters" class="text-center mb-4 parameters">
                <div class="card-group">
                    <div class="card parameter-card shadow-lg">
                        <div class="card-body">
                            <h5 class="card-title">Temperature</h5>
                            <p class="card-text" id="temperatureValue">-- °C</p>
                        </div>
                    </div>
                    <div class="card parameter-card shadow-lg">
                        <div class="card-body">
                            <h5 class="card-title">Turbidity</h5>
                            <p class="card-text" id="turbidityValue">-- NTU</p>
                        </div>
                    </div>
                    <div class="card parameter-card shadow-lg">
                        <div class="card-body">
                            <h5 class="card-title">pH Level</h5>
                            <p class="card-text" id="phValue">--</p>
                        </div>
                    </div>
                    <div class="card parameter-card shadow-lg">
                        <div class="card-body">
                            <h5 class="card-title">NH3 Concentration</h5>
                            <p class="card-text" id="nh3Value">-- mg/L</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-4">
                <p id="overallStatus" class="status-message animated-status"></p>
            </div>
            
            <!-- Toggle Buttons -->
            <div class="row mb-3">
                <div class="col-3">
                    <button id="toggleTemperatureChart" onclick="toggleChart(this, 'temperatureChart')" class="btn btn-primary w-100 chart-toggle-btn" style="background-color: #007bff; color: white;">Toggle Temperature Chart</button>
                </div>
                <div class="col-3">
                    <button id="toggleTurbidityChart" onclick="toggleChart(this, 'turbidityChart')" class="btn btn-primary w-100 chart-toggle-btn" style="background-color: #007bff; color: white;">Toggle Turbidity Chart</button>
                </div>
                <div class="col-3">
                    <button id="togglePhChart" onclick="toggleChart(this, 'phChart')" class="btn btn-primary w-100 chart-toggle-btn" style="background-color: #007bff; color: white;">Toggle pH Chart</button>
                </div>
                <div class="col-3">
                    <button id="toggleNh3Chart" onclick="toggleChart(this, 'nh3Chart')" class="btn btn-primary w-100 chart-toggle-btn" style="background-color: #007bff; color: white;">Toggle NH3 Chart</button>
                </div>
            </div>


            <!-- Chart Containers -->
            <div class="row chart-row d-flex flex-wrap" id="chartRow">

                <div class="chart-container col-md-6" id="temperatureChartContainer" style="display: none;">
                    <canvas id="temperatureChart"></canvas>
                    <div class="legend-container">
                        <div class="legend">
                            <!-- Optionally add legend items here -->
                        </div>
                    </div>
                </div>

                <div class="chart-container col-md-6" id="turbidityChartContainer" style="display: none;">
                    <canvas id="turbidityChart"></canvas>
                    <div class="legend-container">
                        <div class="legend">
                            <div class="legend-item text-center">1<br>Very Clear</div>
                            <div class="legend-item text-center">2<br>Clear</div>
                            <div class="legend-item text-center">3<br>Moderate</div>
                            <div class="legend-item text-center">4<br>Slightly Muddy</div>
                            <div class="legend-item text-center">5<br>Very Muddy</div>
                        </div>
                    </div>
                </div>

                <div class="chart-container col-md-6" id="phChartContainer" style="display: none;">
                    <canvas id="phChart"></canvas>
                    <div class="legend-container">
                        <div class="legend">
                            <!-- Optionally add legend items here -->
                        </div>
                    </div>
                </div>

                <div class="chart-container col-md-6" id="nh3ChartContainer" style="display: none;">
                    <canvas id="nh3Chart"></canvas>
                    <div class="legend-container">
                        <div class="legend">
                            <!-- Optionally add legend items here -->
                        </div>
                    </div>
                </div>
            </div>

            </div>
        </main>
    </div>
</div>

<!--
<div id="charts">
    <canvas id="temperatureChart"></canvas>
    <canvas id="turbidityChart"></canvas>
    <canvas id="phChart"></canvas>
    <canvas id="nh3Chart"></canvas>
</div>
    -->

<footer class="footer">
    <p class="card-text"><b>AquaNet Project 2024</b></p>
</footer>

<script>

window.onload = function() {
    // Get all the toggle buttons using the updated ids
    var toggleButtons = document.querySelectorAll('[id^="toggle"]'); // Select all buttons with ids starting with "toggle"
    
    // Loop through each button and simulate a click
    toggleButtons.forEach(function(button) {
        button.click(); // Simulate clicking the button
    });
};


function toggleChart(button, chartId) {
    var chartContainer = document.getElementById(chartId + 'Container');
    var isHidden = chartContainer.style.display === 'none';
    
    // Toggle chart visibility
    if (isHidden) {
        chartContainer.style.display = 'block'; // Show the chart
        // Change the button color to green when the chart is visible
        button.style.backgroundColor = '#28a745';
        button.style.color = 'white';
    } else {
        chartContainer.style.display = 'none'; // Hide the chart
        // Revert the button color to the original state (blue)
        button.style.backgroundColor = '#007bff';
        button.style.color = 'white';
    }

    adjustChartLayout(); // Adjust layout based on visible charts
}

function adjustChartLayout() {
    var chartContainers = document.querySelectorAll('.chart-container');
    var visibleCharts = Array.from(chartContainers).filter(chart => chart.style.display !== 'none');
    var columnSize = (visibleCharts.length === 1) ? 'col-md-12' :
                     (visibleCharts.length === 2) ? 'col-md-6' :
                     (visibleCharts.length === 3) ? 'col-md-4' : 'col-md-6';

    chartContainers.forEach(container => {
        container.classList.remove('col-md-12', 'col-md-6', 'col-md-4');
        if (container.style.display !== 'none') {
            container.classList.add(columnSize);
        }
    });
}

document.addEventListener("DOMContentLoaded", adjustChartLayout);

$(document).ready(function() {
    var temperatureChart, turbidityChart, phChart, nh3Chart;
    
    var lastValidReadings = {
        temperature: null,
        turbidity: null,
        ph: null,
        nh3: null
    };

    var alertShown = {
        temperature: false,
        turbidity: false,
        ph: false,
        nh3: false
    };
    
    var currentLimits = {};

    function fetchActiveParameters() {
        $.ajax({
            url: 'index/get_active_parameter.php', // Ensure the path is correct
            type: 'GET',
            dataType: 'json',
            success: function(parameters) {
                if (parameters.length === 0) {
                    console.error('No active parameters found.');
                    return;
                }

                // Update the current limits with the fetched parameters
                currentLimits = {
                    minTemperature: parseFloat(parameters.minTemperature),
                    maxTemperature: parseFloat(parameters.maxTemperature),
                    minTurbidity: parseFloat(parameters.minTurbidity),
                    maxTurbidity: parseFloat(parameters.maxTurbidity),
                    minPh: parseFloat(parameters.minPh),
                    maxPh: parseFloat(parameters.maxPh),
                    minNh3: parseFloat(parameters.minNh3),
                    maxNh3: parseFloat(parameters.maxNh3)
                };

                // Log the updated current limits for debugging
                console.log('Updated Current Limits:', currentLimits);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching active parameters:', error);
            }
        });
    }

    // Call this function to fetch active parameters when needed
    fetchActiveParameters();

    $(document).ready(function() {
        startPolling(); // Start polling for new data
    });

    // Function to start polling for data at set intervals
    function startPolling() {
        setInterval(fetchData, 1000); // Fetch data every 5 seconds
    }

    function updateStatusLabels(data) {
        if (data.length === 0) return; // Exit if no data

        // Get the latest values for each parameter (newest is first due to the reverse)
        var latestTemperature = data[0].Temperature; 
        var latestTurbidity = preprocessTurbidity(data[0].Turbidity);
        var latestPh = data[0].pH;
        var latestNh3 = data[0].NH3_concentration;

        // Log latest values for debugging
        console.log('Latest Values for Status Labels:', latestTemperature, latestTurbidity, latestPh, latestNh3);

        // Check overall water quality and display a message
        checkOverallWaterQuality(latestTemperature, latestTurbidity, latestPh, latestNh3);
    }

    function checkOverallWaterQuality(latestTemperature, latestTurbidity, latestPh, latestNh3) {
        var limits = currentLimits;

        // Log the values for debugging
        console.log('Latest Values: ', latestTemperature, latestTurbidity, latestPh, latestNh3);
        console.log('Current Limits: ', limits);

        var temperatureOutOfRange = (latestTemperature < limits.minTemperature || latestTemperature > limits.maxTemperature);
        var turbidityOutOfRange = (latestTurbidity < limits.minTurbidity || latestTurbidity > limits.maxTurbidity);
        var phOutOfRange = (latestPh < limits.minPh || latestPh > limits.maxPh);
        var nh3OutOfRange = (latestNh3 < limits.minNh3 || latestNh3 > limits.maxNh3);

        // Log out-of-range statuses
        console.log('Temperature Out of Range: ', temperatureOutOfRange);
        console.log('Turbidity Out of Range: ', turbidityOutOfRange);
        console.log('pH Out of Range: ', phOutOfRange);
        console.log('NH3 Out of Range: ', nh3OutOfRange);

        var overallStatusElement = $('#overallStatus');
        var statusMessage = '';
        var statusClass = '';

        if (temperatureOutOfRange && turbidityOutOfRange && phOutOfRange && nh3OutOfRange) {
            statusMessage = 'All parameters are out of range! Water quality is extremely bad. (×_×;）';
            statusClass = 'extremely-bad-status';
        } else if (temperatureOutOfRange && turbidityOutOfRange && phOutOfRange) {
            statusMessage = 'Temperature, Turbidity, and pH are out of range! Water quality is very bad! (｡•́︿•̀｡)';
            statusClass = 'very-bad-status';
        } else if (temperatureOutOfRange && turbidityOutOfRange && nh3OutOfRange) {
            statusMessage = 'Temperature, Turbidity, and NH3 levels are out of range! Water quality is very bad! (｡•́︿•̀｡)';
            statusClass = 'very-bad-status';
        } else if (temperatureOutOfRange && phOutOfRange && nh3OutOfRange) {
            statusMessage = 'Temperature, pH, and NH3 levels are out of range! Water quality is very bad! (｡•́︿•̀｡)';
            statusClass = 'very-bad-status';
        } else if (turbidityOutOfRange && phOutOfRange && nh3OutOfRange) {
            statusMessage = 'Turbidity, pH, and NH3 levels are out of range! Water quality is very bad! (｡•́︿•̀｡)';
            statusClass = 'very-bad-status';
        } else if (temperatureOutOfRange && turbidityOutOfRange) {
            statusMessage = 'Temperature and Turbidity are out of range! Water quality is bad! (°△°|||)';
            statusClass = 'bad-status';
        } else if (temperatureOutOfRange && phOutOfRange) {
            statusMessage = 'Temperature and pH are out of range! Water quality is bad! (°△°|||)';
            statusClass = 'bad-status';
        } else if (temperatureOutOfRange && nh3OutOfRange) {
            statusMessage = 'Temperature and NH3 levels are out of range! Water quality is bad! (°△°|||)';
            statusClass = 'bad-status';
        } else if (turbidityOutOfRange && phOutOfRange) {
            statusMessage = 'Turbidity and pH are out of range! Water quality is bad! (°△°|||)';
            statusClass = 'bad-status';
        } else if (turbidityOutOfRange && nh3OutOfRange) {
            statusMessage = 'Turbidity and NH3 levels are out of range! Water quality is bad! (°△°|||)';
            statusClass = 'bad-status';
        } else if (phOutOfRange && nh3OutOfRange) {
            statusMessage = 'pH and NH3 levels are out of range! Water quality is bad! (°△°|||)';
            statusClass = 'bad-status';
        } else if (temperatureOutOfRange) {
            statusMessage = 'Temperature is out of range! Water quality is not optimal. (´･_･`)';
            statusClass = 'warning-status';
        } else if (turbidityOutOfRange) {
            statusMessage = 'Turbidity is out of range! Water quality is not optimal. (´･_･`)';
            statusClass = 'warning-status';
        } else if (phOutOfRange) {
            statusMessage = 'pH is out of range! Water quality is not optimal. (´･_･`)';
            statusClass = 'warning-status';
        } else if (nh3OutOfRange) {
            statusMessage = 'NH3 levels are out of range! Water quality is not optimal. (´･_･`)';
            statusClass = 'warning-status';
        } else {
            statusMessage = 'Water quality is Good ٩(＾◡＾)۶';
            statusClass = 'good-status';
        }


        // Update the overall status text and class
        overallStatusElement.text(statusMessage);
        overallStatusElement.removeClass('good-status bad-status').addClass(statusClass);
    }

    function fetchData() {
        $.ajax({
            url: 'index/get_sensor_data.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                updateCharts(data);
                updateStatusLabels(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    }

    function preprocessTurbidity(turbidity) {
        switch (turbidity) {
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

    function updateCharts(data) {
        var timestampBuffer = data.map(function(entry) {
            return entry.timestamp;
        });

        // Use the latest readings
        var latestTemperatureReading = data[data.length - 1].Temperature;
        var latestTurbidityReading = preprocessTurbidity(data[data.length - 1].Turbidity);
        var latestPhReading = data[data.length - 1].pH;
        var latestNh3Reading = data[data.length - 1].NH3_concentration;

        var temperatureDataBuffer = data.map(function(entry) {
            return entry.Temperature;
        }).reverse();

        var turbidityDataBuffer = data.map(function(entry) {
            return preprocessTurbidity(entry.Turbidity);
        }).reverse();

        var phDataBuffer = data.map(function(entry) {
            return entry.pH;
        }).reverse();

        var nh3DataBuffer = data.map(function(entry) {
            return entry.NH3_concentration;
        }).reverse();

        // Set labels and data for charts
        temperatureChart.data.labels = timestampBuffer.slice(-100).reverse();
        temperatureChart.data.datasets[0].data = temperatureDataBuffer.slice(-100);

        turbidityChart.data.labels = timestampBuffer.slice(-100).reverse();
        turbidityChart.data.datasets[0].data = turbidityDataBuffer.slice(-100);

        phChart.data.labels = timestampBuffer.slice(-100).reverse();
        phChart.data.datasets[0].data = phDataBuffer.slice(-100);

        nh3Chart.data.labels = timestampBuffer.slice(-100).reverse();
        nh3Chart.data.datasets[0].data = nh3DataBuffer.slice(-100);

        // Check parameter limits for the latest readings
        checkParameterLimits('temperature', latestTemperatureReading);
        checkParameterLimits('turbidity', latestTurbidityReading);
        checkParameterLimits('ph', latestPhReading);
        checkParameterLimits('nh3', latestNh3Reading);

        // Update the charts
        temperatureChart.update();
        turbidityChart.update();
        phChart.update();
        nh3Chart.update();
    }

    function checkParameterLimits(chartType) {
        var chart = {
            temperature: temperatureChart,
            turbidity: turbidityChart,
            ph: phChart,
            nh3: nh3Chart
        }[chartType];

        // Get the latest reading from the last data point in the dataset
        var latestReading = chart.data.datasets[0].data[chart.data.datasets[0].data.length - 1];

        $.ajax({
            url: 'index/get_current_parameters.php',
            type: 'GET',
            dataType: 'json',
            success: function(parameters) {
                console.log('Fetched parameters:', parameters);

                if (!Array.isArray(parameters) || parameters.length === 0) {
                    console.error('Received parameters are not valid:', parameters);
                    return;
                }

                var limits = parameters[0];

                // Convert limits to numbers
                var minTemperature = Number(limits.minTemperature);
                var maxTemperature = Number(limits.maxTemperature);
                var minTurbidity = Number(limits.minTurbidity);
                var maxTurbidity = Number(limits.maxTurbidity);
                var minPh = Number(limits.minPh);
                var maxPh = Number(limits.maxPh);
                var minNh3 = Number(limits.minNh3);
                var maxNh3 = Number(limits.maxNh3);

                var isOutOfBounds = false;
                var message = '';

                console.log('Latest Reading:', latestReading);
                console.log('Limits:', limits);

                switch (chartType) {
                    case 'temperature':
                        console.log('Checking Temperature:', latestReading, minTemperature, maxTemperature);
                        if (latestReading < minTemperature) {
                            isOutOfBounds = true;
                            message = 'Temperature reading is too low.';
                        } else if (latestReading > maxTemperature) {
                            isOutOfBounds = true;
                            message = 'Temperature reading is too high.';
                        }
                        break;

                    case 'turbidity':
                        console.log('Checking Turbidity:', latestReading, minTurbidity, maxTurbidity);
                        if (latestReading < minTurbidity) {
                            isOutOfBounds = true;
                            message = 'Turbidity reading is too low.';
                        } else if (latestReading > maxTurbidity) {
                            isOutOfBounds = true;
                            message = 'Turbidity reading is too high.';
                        }
                        break;

                    case 'ph':
                        console.log('Checking pH:', latestReading, minPh, maxPh);
                        if (latestReading < minPh) {
                            isOutOfBounds = true;
                            message = 'pH reading is too low.';
                        } else if (latestReading > maxPh) {
                            isOutOfBounds = true;
                            message = 'pH reading is too high.';
                        }
                        break;

                    case 'nh3':
                        console.log('Checking NH3:', latestReading, minNh3, maxNh3);
                        if (latestReading < minNh3) {
                            isOutOfBounds = true;
                            message = 'NH3 reading is too low.';
                        } else if (latestReading > maxNh3) {
                            isOutOfBounds = true;
                            message = 'NH3 reading is too high.';
                        }
                        break;
                }

                console.log('Is Out of Bounds:', isOutOfBounds);

                if (isOutOfBounds) {
                    chart.data.datasets[0].borderColor = 'red'; // Set border color to red
                    console.log('Setting border to red for:', chartType);
                    if (!alertShown[chartType]) {
                        console.log('Triggering alert for:', chartType);
                        console.log('Alert message:', message);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Parameter Out of Range',
                            text: message,
                        });

                        alertShown[chartType] = true; // Mark alert as shown
                    }
                } else {
                    chart.data.datasets[0].borderColor = 'rgba(54, 162, 235, 1)'; // Reset to default color
                    alertShown[chartType] = false; // Reset alert flag
                }

                chart.update(); // Apply changes to the chart
            },
            error: function(xhr, status, error) {
                console.error('Error fetching current parameters:', error);
            }
        });
    }







    // Initialize charts
    temperatureChart = new Chart(document.getElementById('temperatureChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Temperature',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: false
                },
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    turbidityChart = new Chart(document.getElementById('turbidityChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Turbidity (1 = Very Clear - 5 = Very Muddy)',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: false
                },
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    phChart = new Chart(document.getElementById('phChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'pH',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: false
                },
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    nh3Chart = new Chart(document.getElementById('nh3Chart').getContext('2d'), {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'NH3 Concentration',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                x: {
                    display: false
                },
                y: {
                    beginAtZero: false
                }
            }
        }
    });

    fetchData();
    setInterval(fetchData, 1000);

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.navbar').length) {
            $('.navbar-collapse').collapse('hide');
        }
    });


    fetchCurrentParameters();

    function fetchCurrentParameters() {
        $.ajax({
            url: 'index/get_current_parameters.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    // Assume we are displaying the first parameter in the data array
                    var param = data[0];  // Select the first parameter
                    
                    var parametersHtml = '<h5>Current Parameter: ' + param.parameterName + '</h5>';
                    parametersHtml += '<ul>';
                    parametersHtml += '<b>Temperature</b>: ' + param.minTemperature + ' - ' + param.maxTemperature;
                    parametersHtml += '<b> | Turbidity</b>: ' + param.minTurbidity + ' - ' + param.maxTurbidity;
                    parametersHtml += '<b> | pH</b>: ' + param.minPh + ' - ' + param.maxPh;
                    parametersHtml += '<b> | NH3</b>: ' + param.minNh3 + ' - ' + param.maxNh3;
                    parametersHtml += '</ul>';
                    
                    $('#currentParameters').html(parametersHtml);
                } else {
                    $('#currentParameters').html('<p>No current parameters available.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching current parameters:', error);
            }
        });
    }
    // Function to fetch and display notifications

    function fetchNotifications() {
        $.ajax({
            url: 'index/fetch_notifications.php',
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
            url: 'index/is_read.php',
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
            url: 'index/mark_notifications_read.php',
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
        });

</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

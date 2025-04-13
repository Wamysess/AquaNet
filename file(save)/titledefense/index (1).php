<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquanet</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 800px;
            height: 400px;
            margin: auto;
            margin-bottom: 80px; /* Add margin-bottom to create space for the legend */
        }
        .legend {
            text-align: center;
            margin-top: 20px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 20px;
        }
        .legend-color {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 5px;
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
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
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
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-4 mb-4">Aquanet Aquarium Water Monitor</h1>
        <div class="row justify-content-center align-items-center">
            <div class="col-lg-12 text-center">
                <div class="chart-container">
                    <canvas id="temperatureChart"></canvas>
                </div>
            </div>
            <div class="col-lg-12 text-center">
                <div class="chart-container">
                    <canvas id="turbidityChart"></canvas>
                    <div class="legend">
                        <div class="legend-item">
                             1<br>Very Clear
                        </div>
                        <div class="legend-item">
                            2<br>Clear
                        </div>
                        <div class="legend-item">
                            3<br>Moderate
                        </div>
                        <div class="legend-item">
                            4<br>Slightly Muddy
                        </div>
                        <div class="legend-item">
                            5<br>Very Muddy
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 text-center">
                <div class="chart-container">
                    <canvas id="phChart"></canvas>
                </div>
            </div>
            <div class="col-lg-12 text-center">
                <div class="chart-container">
                    <canvas id="nh3Chart"></canvas>
                </div>
            </div>
        </div>
        <div class="text-center mt-4 mb-4">
            <button id="showData" class="btn btn-primary">Show Data</button>
        </div>
    </div>

    <div id="dataModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Data</h2>
            <div id="dataTable"></div>
        </div>
    </div>

    <script>
    var data = []; // Declare data variable globally
        $(document).ready(function() {
            var temperatureChart, turbidityChart, phChart, nh3Chart;

            function fetchData() {
                $.ajax({
                    url: 'get_sensor_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        updateCharts(data);
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
                        return 0;
                }
            }

            function updateCharts(data) {
                var timestampBuffer = data.map(function(entry) {
                    return entry.timestamp;
                });

                var temperatureDataBuffer = data.map(function(entry) {
                    return entry.Temperature;
                }).reverse(); // Reverse the array

                var turbidityDataBuffer = data.map(function(entry) {
                    return preprocessTurbidity(entry.Turbidity);
                }).reverse(); // Reverse the array

                var phDataBuffer = data.map(function(entry) {
                    return entry.pH;
                }).reverse(); // Reverse the array

                var nh3DataBuffer = data.map(function(entry) {
                    return entry.NH3_concentration;
                }).reverse(); // Reverse the array

                // Update temperature chart
                temperatureChart.data.labels = timestampBuffer.slice(-100).reverse(); // Reverse the labels array
                temperatureChart.data.datasets[0].data = temperatureDataBuffer.slice(-100);

                // Update turbidity chart
                turbidityChart.data.labels = timestampBuffer.slice(-100).reverse(); // Reverse the labels array
                turbidityChart.data.datasets[0].data = turbidityDataBuffer.slice(-100);

                // Update pH chart
                phChart.data.labels = timestampBuffer.slice(-100).reverse(); // Reverse the labels array
                phChart.data.datasets[0].data = phDataBuffer.slice(-100);

                // Update NH3 concentration chart
                nh3Chart.data.labels = timestampBuffer.slice(-100).reverse(); // Reverse the labels array
                nh3Chart.data.datasets[0].data = nh3DataBuffer.slice(-100);

                // Update all charts
                temperatureChart.update();
                turbidityChart.update();
                phChart.update();
                nh3Chart.update();
            }

            temperatureChart = new Chart(document.getElementById('temperatureChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Temperature',
                        data: [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
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
                        label: 'Turbidity',
                        data: [],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
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
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
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
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });

            // Fetch initial data
            fetchData();

            // Fetch data every 10 seconds
            setInterval(fetchData, 1000);
            
            $('#showData').click(function() {
            $.ajax({
                url: 'get_data_chart.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Check if data is available
                    if (data.length > 0) {
                        // Generate and display the table using the fetched data
                        var dataTable = '<table>';
                        // Add table rows and cells
                        // Loop through each data entry and append table rows
                        data.forEach(function(entry) {
                            dataTable += '<tr>';
                            dataTable += '<td>' + entry.timestamp + '</td>';
                            dataTable += '<td>' + entry.Temperature + '</td>';
                            dataTable += '<td>' + entry.Turbidity + '</td>';
                            dataTable += '<td>' + entry.pH + '</td>';
                            dataTable += '<td>' + entry.NH3_concentration + '</td>';
                            dataTable += '</tr>';
                        });
                        dataTable += '</table>';
                        $('#dataTable').html(dataTable);
                        $('#dataModal').show(); // Show the modal
                    } else {
                        console.log('No data available.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        // Close modal click event
        $('.close').click(function() {
            $('#dataModal').hide(); // Hide the modal
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target == document.getElementById('dataModal')) {
                $('#dataModal').hide(); // Hide the modal
            }
        };
        });
    </script>
</body>
</html>
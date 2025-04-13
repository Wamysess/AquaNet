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
    <title>Parameters</title>
    <link rel="icon" href="../img/aquaneticon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        body {
            background-color: #f0f2f5;
            color: #333;
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
        .data-table-container {
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
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
        .action-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .action-buttons i {
            cursor: pointer;
            font-size: 18px;
            color: #007bff;
            margin: 0 5px;
            transition: color 0.3s;
        }
        .action-buttons i:hover {
            color: #0056b3;
        }
        .footer {
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        .footer .card-text {
            font-size: 14px;
        }
        .form-container {
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: none;
        }
        .form-container h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group label {
            font-weight: 500;
            color: #333;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 12px;
            font-size: 14px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.12);
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }
        .form-group input {
            display: block;
            width: 100%;
        }
        .form-group input[type="text"] {
            text-align: center;
        }
        .form-row {
            margin-bottom: 15px;
        }
        .form-row .form-group {
            margin-bottom: 0;
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

        .selectparameter {
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        #selectParameter {
            width: 100%; /* Makes the select element take the full width of its container */
            height: 50px; /* Adjust as needed */
            font-size: 16px; /* Optional: Increase the font size for better readability */
        }

        /* Custom Toastr CSS */
            .toast-top-right {
            top: 70px !important;
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

<div class="container mt-4 selectparameter">
    <h2 class="text-center mb-4">Select Parameter</h2>
    <div class="form-group">
        <label for="selectParameter">Choose a Parameter:</label>
        <select id="selectParameter" class="form-control">
            <option value="">Select a parameter</option>
            <!-- Options will be dynamically populated here -->
        </select>
        <small id="selectedParameter" class="form-text text-muted"></small>
    </div>
    <div class="text-center">
        <button type="button" class="btn btn-primary" onclick="setActiveParameter()">Set Active</button>
    </div>
    <div id="currentActiveParameter" class="text-center mt-4">
        <p>Current Active Parameter: <span id="activeParameterName">Loading...</span></p>
    </div>
</div>


    <div class="container data-table-container">
        <h2 class="text-center mt-4 mb-4">Sensor Parameters</h2>
        <div class="action-buttons text-center mb-3">
        <button class="btn btn-primary" id="addParameter" title="Add New Parameter" onclick="scrollToAddParameter()">Add New Parameter</button>
        </div>
        <div id="dataTableContainer" class="table-responsive"></div>
    </div>

    <div class="container form-container" id="parameterFormContainer">
        <h2 id="formHeader">Set Sensor Parameters</h2>
        <form id="parameterForm">
            <input type="hidden" id="parameterId" name="parameterId">
            <div class="form-group text-center">
                <label for="parameterName">Parameter Name:</label>
                <input type="text" class="form-control mx-auto" id="parameterName" name="parameterName" required style="max-width: 400px;">
                <small id="parameterNameError" class="form-text text-danger"></small>
            </div>

            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="minTemperature">Temperature (Min):</label>
                        <input type="number" class="form-control" id="minTemperature" name="minTemperature" placeholder="Min Temperature" step="1" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="maxTemperature">Temperature (Max):</label>
                        <input type="number" class="form-control" id="maxTemperature" name="maxTemperature" placeholder="Max Temperature" step="1" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="minTurbidity">Turbidity (Min: 1):</label>
                        <input type="number" class="form-control" id="minTurbidity" name="minTurbidity" placeholder="Min turbidity" min="1" max="5" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="maxTurbidity">Turbidity (Max: 5):</label>
                        <input type="number" class="form-control" id="maxTurbidity" name="maxTurbidity" placeholder="Max turbidity" min="1" max="5" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="minPh">pH Level (Min):</label>
                        <input type="number" step="any" class="form-control" id="minPh" name="minPh" placeholder="Min pH Level" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="maxPh">pH Level (Max):</label>
                        <input type="number" step="any" class="form-control" id="maxPh" name="maxPh" placeholder="Max pH Level" required>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="minNh3">NH3 Concentration (Min):</label>
                        <input type="number" step="any" class="form-control" id="minNh3" name="minNh3" placeholder="Min NH3 Concentration" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="maxNh3">NH3 Concentration (Max):</label>
                        <input type="number" step="any" class="form-control" id="maxNh3" name="maxNh3" placeholder="Max NH3 Concentration" required>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" id="cancelForm" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>

    <footer class="footer">
        <div class="card-body text-center">
            <p class="card-text"><b>AquaNet Project 2024</b></p>
        </div>
    </footer>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>

        function scrollToAddParameter() {
            // Show form, hide table, and reset form fields
            $('#parameterFormContainer').show();
            $('#dataTableContainer').hide();
            $('#parameterForm')[0].reset();
            $('#parameterId').val('');  // Ensure the hidden field is cleared
            $('#parameterNameError').text(''); // Clear any previous error messages
            $('#formHeader').text('Set Sensor Parameters');

            // Scroll smoothly to the form container
            document.getElementById('parameterFormContainer').scrollIntoView({ behavior: 'smooth' });
        }

        // Attach click event to trigger the function
        $('#addParameter').click(scrollToAddParameter);

        function showToast(message, type = 'info') {
            const toastrOptions = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 5000,
                extendedTimeOut: 1000,
                hideDuration: 1000,
                showDuration: 300,
                newestOnTop: true
            };

            if (type === 'error') {
                toastrOptions.timeOut = 7000; // Longer time for errors
                toastr.error(message, 'Error', toastrOptions);
            } else {
                toastr.success(message, 'Info', toastrOptions);
            }
        }


        document.getElementById('minTemperature').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('maxTemperature').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('minTurbidity').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('maxTurbidity').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('minPh').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('maxPh').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('minNh3').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.getElementById('maxNh3').addEventListener('input', function() {
            // Remove non-numeric characters except for decimals
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $(document).ready(function() {

            $('#parameterForm').submit(function(e) {
                e.preventDefault();

                var minTemperature = parseFloat($('#minTemperature').val());
                var maxTemperature = parseFloat($('#maxTemperature').val());
                var minTurbidity = parseFloat($('#minTurbidity').val());
                var maxTurbidity = parseFloat($('#maxTurbidity').val());
                var minPh = parseFloat($('#minPh').val());
                var maxPh = parseFloat($('#maxPh').val());
                var minNh3 = parseFloat($('#minNh3').val());
                var maxNh3 = parseFloat($('#maxNh3').val());

                if (minTemperature > maxTemperature) {
                    showToast('Min Temperature cannot be greater than Max Temperature');
                    return;
                }

                if (minTurbidity < 1 || minTurbidity > 9) {
                    showToast('Min Turbidity must be between 1 and 9');
                    return;
                }

                if (maxTurbidity < 1 || maxTurbidity > 9) {
                    showToast('Max Turbidity must be between 1 and 9');
                    return;
                }

                if (minTurbidity > maxTurbidity) {
                    showToast('Min Turbidity cannot be greater than Max Turbidity');
                    return;
                }
            

                if (minPh > maxPh) {
                    showToast('Min pH Level cannot be greater than Max pH Level');
                    return;
                }

                if (minNh3 > maxNh3) {
                    showToast('Min NH3 Concentration cannot be greater than Max NH3 Concentration');
                    return;
                }

                var formData = $(this).serialize();
                var parameterName = $('#parameterName').val();
                var parameterId = $('#parameterId').val();

                // Disable the submit button to prevent duplicate submissions
                $('button[type="submit"]').prop('disabled', true);

                checkParameterName(parameterName, parameterId, function(exists) {
                    if (exists) {
                        $('#parameterNameError').text('Parameter name already exists.');
                        $('button[type="submit"]').prop('disabled', false); // Re-enable the submit button
                    } else {
                        $('#parameterNameError').text('');
                        var isUpdate = parameterId !== '';
                        var url = isUpdate ? 'parameters/update_parameter.php' : 'parameters/save_parameter.php';

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                showToast(isUpdate ? 'Parameter updated successfully' : 'Parameter saved successfully');
                                $('#parameterFormContainer').hide();
                                $('#dataTableContainer').show();
                                fetchParameters();
                            },
                            error: function(xhr, status, error) {
                                console.error('Failed to save parameter:', status, error);
                            },
                            complete: function() {
                                $('button[type="submit"]').prop('disabled', false); // Re-enable the submit button after processing
                            }
                        });
                    }
                });

            });  
            function fetchParameters() {
                $.ajax({
                    url: 'parameters/get_parameters.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log('Parameters fetched:', data);
                        if (data.length > 0) {
                            var dataTable = '<table class="table table-striped">';
                            dataTable += '<thead><tr><th>Parameter Name</th><th>Temperature (Min)</th><th>Temperature (Max)</th><th>Turbidity (Min)</th><th>Turbidity (Max)</th><th>pH (Min)</th><th>pH (Max)</th><th>NH3 (Min)</th><th>NH3 (Max)</th><th>Actions</th></tr></thead>';
                            dataTable += '<tbody>';
                            data.forEach(function(entry) {
                                dataTable += '<tr>';
                                dataTable += '<td>' + entry.parameterName + '</td>';
                                dataTable += '<td>' + entry.minTemperature + '</td>';
                                dataTable += '<td>' + entry.maxTemperature + '</td>';
                                dataTable += '<td>' + entry.minTurbidity + '</td>';
                                dataTable += '<td>' + entry.maxTurbidity + '</td>';
                                dataTable += '<td>' + entry.minPh + '</td>';
                                dataTable += '<td>' + entry.maxPh + '</td>';
                                dataTable += '<td>' + entry.minNh3 + '</td>';
                                dataTable += '<td>' + entry.maxNh3 + '</td>';
                                dataTable += '<td class="action-buttons">';
                                dataTable += '<i class="fas fa-edit editParameter" data-id="' + entry.id + '"></i>';
                                dataTable += '<i class="fas fa-trash-alt deleteParameter" data-id="' + entry.id + '"></i>';
                                dataTable += '</td>';
                                dataTable += '</tr>';
                            });
                            dataTable += '</tbody></table>';
                            $('#dataTableContainer').html(dataTable);

                            // Bind click event to delete icons
                            $('.deleteParameter').on('click', function() {
                                var parameterId = $(this).data('id');
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: 'This action cannot be undone!',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, delete it!',
                                    cancelButtonText: 'No, cancel!'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Perform the delete action here
                                        $.ajax({
                                            url: 'parameters/delete_parameter.php',
                                            type: 'POST',
                                            data: { id: parameterId },
                                            success: function(response) {
                                                if (response.success) {
                                                    Swal.fire(
                                                        'Deleted!',
                                                        'The parameter has been deleted.',
                                                        'success'
                                                    );
                                                    fetchParameters(); // Refresh the table
                                                } else {
                                                    Swal.fire(
                                                        'Error!',
                                                        'There was a problem deleting the parameter.',
                                                        'error'
                                                    );
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                Swal.fire(
                                                    'Error!',
                                                    'There was a problem deleting the parameter.',
                                                    'error'
                                                );
                                            }
                                        });
                                    }
                                });
                            });
                        } else {
                            $('#dataTableContainer').html('<p>No parameters found.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to fetch parameters:', status, error);
                    }
                });
            }


            fetchParameters();

            $('#cancelForm').click(function() {
                $('#parameterFormContainer').hide();
                $('#dataTableContainer').show();
            });

                

            function checkParameterName(name, id, callback) {
                $.ajax({
                    url: 'parameters/check_parameter_name.php',
                    type: 'POST',
                    data: { name: name, id: id },
                    success: function(response) {
                        callback(response.exists);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to check parameter name:', status, error);
                    }
                });
            }
            
            $(document).on('click', '.editParameter', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'parameters/get_parameter.php',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function(data) {
                        $('#parameterId').val(data.id);
                        $('#parameterName').val(data.parameterName);
                        $('#minTemperature').val(data.minTemperature);
                        $('#maxTemperature').val(data.maxTemperature);
                        $('#minTurbidity').val(data.minTurbidity);
                        $('#maxTurbidity').val(data.maxTurbidity);
                        $('#minPh').val(data.minPh);
                        $('#maxPh').val(data.maxPh);
                        $('#minNh3').val(data.minNh3);
                        $('#maxNh3').val(data.maxNh3);

                        $('#parameterFormContainer').show();
                        $('#dataTableContainer').hide();
                        $('#formHeader').text('Edit Parameter');
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to fetch parameter:', status, error);
                    }
                });
            });

            $(document).on('input', '#minTurbidity, #maxTurbidity', function() {
                let value = $(this).val();
                if (value === '') {
                    return; // Allow clearing the input
                }
                value = Number(value);
                if (value < 1) {
                    $(this).val(1);
                } else if (value > 5) {
                    $(this).val(5);
                }
            });

            $.ajax({
                url: 'parameters/parameter_name.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var $select = $('#selectParameter');
                    $select.empty().append('<option value="">Select a parameter</option>');
                    data.forEach(function(parameter) {
                        // Ensure parameter is an object with a property 'parameterName'
                        $select.append('<option value="' + parameter.parameterName + '">' + parameter.parameterName + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch parameters:', status, error);
                }
            });


            function checkParameterName(name, id, callback) {
                $.ajax({
                    url: 'parameters/check_parameter_name.php',
                    type: 'POST',
                    data: { parameterName: name, parameterId: id },
                    dataType: 'json',
                    success: function(response) {
                        callback(response.exists);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to check parameter name:', status, error);
                        callback(false);
                    }
                });
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

        function setActiveParameter() {
            const selectedParameter = document.getElementById('selectParameter').value;

            if (!selectedParameter) {
                showToast('Please select a parameter to activate.');
                return;
            }

            $.ajax({
                url: 'parameters/set_active.php',
                method: 'POST',
                data: { parameterName: selectedParameter },
                success: function(response) {
                    showToast('Parameter has been set as active.');
                    // Reload the page after a short delay to ensure the toast message is shown
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", status, error); // Debugging
                    showToast('An error occurred while setting the parameter.');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
    // Fetch the current active parameter on page load
    $.ajax({
        url: 'parameters/get_active_parameter.php',
        method: 'GET',
        success: function(response) {
            // Assuming the response is just the parameter name
            $('#activeParameterName').text(response || 'None');
        },
        error: function() {
            $('#activeParameterName').text('Error fetching data');
        }
    });

    $(document).on('click', function(event) {
        if (!$(event.target).closest('.navbar').length) {
            $('.navbar-collapse').collapse('hide');
        }
    });
});
    </script>
</body>
</html>

<?php
//user authentication if he/she is logged-in, if not then redirect user to login page
session_start();
if (!isset($_SESSION["username"])){
header("Location: ../login.php");
exit(); }
?>
<?php
include '../db_config.php';

try {
    $sql = "SELECT COUNT(*) as total_users FROM users";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_users = $row['total_users'];
} catch (PDOException $e) {
    echo "Error fetching user count: " . $e->getMessage();
}

$query = "SELECT DATE(created_at) AS date, COUNT(*) AS user_count 
          FROM users 
          GROUP BY DATE(created_at)
          ORDER BY DATE(created_at) ASC";

$stmt = $pdo->query($query);
$user_growth_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the chart
$dates = [];
$user_counts = [];

foreach ($user_growth_data as $row) {
    $dates[] = $row['date'];
    $user_counts[] = $row['user_count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
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

        /* Custom Toastr CSS */
            .toast-top-right {
            top: 70px !important;
        }

        .data-table-container {
            margin-top: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center; /* Center align text */
            position: relative; /* For the confetti */
            overflow: hidden; /* Hide overflowing confetti */
        }
        .user-count {
            font-size: 36px; /* Increase font size */
            font-weight: bold;
            margin: 20px 0;
            color: #007bff; /* Bootstrap primary color */
            animation: bounce 2s; /* Add bounce animation */
        }
        .confetti {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .statistics {
            margin-top: 15px; /* Reduced space between sections */
            border: 1px solid #dedede; /* Light border for separation */
            border-radius: 5px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
            padding: 10px; /* Reduced padding inside the card */
        }

        .statistics h3 {
            font-weight: bold; /* Make the title bold */
            color: #007bff; /* Bootstrap primary color */
            font-size: 1.2rem; /* Reduced font size */
        }

        .card {
            background-color: #f8f9fa; /* Light background for the card */
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="admin_dashboard.php">
        <img src="../img/aquanet.png" width="30" height="30" alt="aquanet"> AquaNet
    </a>

    <div class="d-flex align-items-center ml-auto">
        <!-- Notification Button -->
        <div class="notification-container mr-3">
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
                <a class="nav-link" href="users.php"><b>User Acc</b></a>
            </li>
        </ul>
    </div>
    <!-- Logged-in Username with Dropdown -->
    <div class="dropdown push-right">
        <button class="btn dropdown-toggle" id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            </i> <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="../edit_profile.php">Edit Profile</a>
            <a class="dropdown-item" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>



<div class="data-table-container">
    <div class="confetti" id="confetti"></div>
    <div class="user-count animate__animated animate__fadeIn">
        🎉 Total Registered Users: <?php echo $total_users; ?> 🎉
    </div>
</div>

<div class="statistics card">
    <div class="card-body">
        <h3 class="card-title">User Growth Over Time</h3>
        <canvas id="userGrowthChart"></canvas>
    </div>
</div>


<footer class="footer">
        <div class="card-body text-center">
            <p class="card-text"><b>AquaNet Project 2024</b></p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Confetti effect
    function createConfetti() {
        const confetti = document.getElementById('confetti');
        const colors = ['#ff0', '#0f0', '#f00', '#00f', '#ff0', '#0ff'];
        
        for (let i = 0; i < 100; i++) {
            const piece = document.createElement('div');
            piece.style.position = 'absolute';
            piece.style.width = '10px';
            piece.style.height = '10px';
            piece.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            piece.style.opacity = Math.random();
            piece.style.borderRadius = '50%';
            piece.style.top = `${Math.random() * 100}vh`;
            piece.style.left = `${Math.random() * 100}vw`;
            piece.style.transition = 'all 2s ease-in';
            piece.style.transform = `translateY(-${Math.random() * 100}px) rotate(${Math.random() * 360}deg)`;
            confetti.appendChild(piece);
            setTimeout(() => {
                piece.style.transform = `translateY(${window.innerHeight}px)`;
            }, Math.random() * 1000);
        }
    }

    const labels = <?php echo json_encode($dates); ?>; // Dates for x-axis
    const data = {
        labels: labels,
        datasets: [{
            label: 'Registered Users',
            data: <?php echo json_encode($user_counts); ?>, // User counts for y-axis
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 1,
            fill: true,
        }]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Users'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'User Growth Over Time'
                }
            }
        },
    };

    const userGrowthChart = new Chart(
        document.getElementById('userGrowthChart'),
        config
    );

    createConfetti(); // Call the confetti function
</script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>

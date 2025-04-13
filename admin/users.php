<?php
// User authentication
session_start();
if (!isset($_SESSION["username"])){
    header("Location: ../login.php");
    exit();
}

// Include database configuration
include '../db_config.php';

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users"); // Assuming the table is named 'users'
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Account</title>
    <link rel="icon" href="../img/aquaneticon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        body {
            background-color: #f0f2f5;
            color: #333;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="admin_dashboard.php">
        <img src="../img/aquanet.png" width="30" height="30" alt="aquanet"> AquaNet
    </a>
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
            <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>
        </button>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="../edit_profile.php">Edit Profile</a>
            <a class="dropdown-item" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="data-table-container">
    <h2 class="text-center">User Details</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Contact Number</th>
                <th>User Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_type']); ?></td>
                    <td class="action-buttons">
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="text-success">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_user.php" class="text-danger delete-user" data-id="<?php echo $user['id']; ?>">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>


<footer class="footer">
    <div class="card-body text-center">
        <p class="card-text"><b>AquaNet Project 2024</b></p>
    </div>
</footer>

<script>
    $(document).ready(function() {
        $('.delete-user').on('click', function(e) {
            e.preventDefault();
            const userId = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_user.php', // Create this PHP file to handle deletion
                        type: 'POST',
                        data: { id: userId },
                        success: function(response) {
                            // You can handle the response from the server here
                            Swal.fire('Deleted!', 'User has been deleted.', 'success');
                            location.reload(); // Reload the page to see changes
                        },
                        error: function() {
                            Swal.fire('Error!', 'There was an error deleting the user.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>

</body>
</html>

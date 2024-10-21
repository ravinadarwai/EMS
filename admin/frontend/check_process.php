<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php'; // Include your database configuration

// Fetch employee check-in records with concatenated first_name and last_name
$query = "
    SELECT ec.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name
    FROM employee_checkin ec
    JOIN employee e ON ec.employee_id = e.employee_id
";
$result = $conn->query($query);

// Check if there are any records
if ($result->num_rows > 0) {
    $checkins = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
} else {
    $checkins = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Information Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
        }

        .table {
            margin-top: 20px;
        }
    </style>
    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'layouts/aside.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 ms-sm-auto px-4">
                <?php include 'layouts/header.php'; ?>
                
                <div class="form-container">
                    <h2 class="text-center">Employee Check-in Records</h2>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Employee Name</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Check-in Time</th>
                                <th>Check-out Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                             $sno = 1;
                             if (!empty($checkins)): ?>
                                <?php foreach ($checkins as $checkin): ?>
                                    <tr>
                                        <td><?php echo $sno++; ?></td>
                                        <td><?php echo htmlspecialchars($checkin['employee_name']); ?></td>
                                        <td><?php echo htmlspecialchars($checkin['action']); ?></td>
                                        <td><?php echo htmlspecialchars($checkin['status']); ?></td>
                                        <td><?php echo htmlspecialchars($checkin['check_in_time']); ?></td>
                                        <td><?php echo htmlspecialchars($checkin['check_out_time']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No records found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

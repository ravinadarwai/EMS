<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';

// Get the employee ID from the query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid employee ID");
}

$employee_id = intval($_GET['id']);

// Fetch employee data
$queryEmployee = "SELECT * FROM employee WHERE id = ?";
$stmtEmployee = $conn->prepare($queryEmployee);
$stmtEmployee->bind_param("i", $employee_id);
$stmtEmployee->execute();
$resultEmployee = $stmtEmployee->get_result();

if ($resultEmployee->num_rows === 0) {
    die("Employee not found");
}

$employee = $resultEmployee->fetch_assoc();

// Fetch daily reports
$queryReports = "SELECT * FROM dailyreport WHERE employee_id = ? ORDER BY datetime DESC";
$stmtReports = $conn->prepare($queryReports);
$stmtReports->bind_param("i", $employee_id);
$stmtReports->execute();
$resultReports = $stmtReports->get_result();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Daily Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Daily Reports for <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></h1>
    <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>

    <table class="table table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Project ID</th>
                <th>Time Duration (Hours)</th>
                <th>Date and Time</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultReports->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['project_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_duration']); ?></td>
                    <td><?php echo htmlspecialchars($row['datetime']); ?></td>
                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

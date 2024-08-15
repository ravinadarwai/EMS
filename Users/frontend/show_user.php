<?php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if the user ID is set
if (!isset($_SESSION['employee_id'])) {
    header("Location: error.php?message=User ID not set");
    exit();
}

$userid = $_SESSION['employee_id'];

include '../../config.php';

$previous_data = [];
$filter_date = '';

// Handle form submission for filtering
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_date'])) {
    $filter_date = $_POST['filter_date'];

    // Query to fetch data based on the filter date and user ID
    $sql = "SELECT projects.project_name, dailyreport.time_duration, dailyreport.datetime, dailyreport.comment 
            FROM dailyreport
            INNER JOIN projects ON dailyreport.project_id = projects.id
            WHERE dailyreport.employee_id = ? AND DATE(dailyreport.datetime) = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $userid, $filter_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $previous_data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
} else {
    // Query to fetch all data if no filter is applied
    $sql = "SELECT projects.project_name, dailyreport.time_duration, dailyreport.datetime, dailyreport.comment 
            FROM dailyreport
            INNER JOIN projects ON dailyreport.project_id = projects.id
            WHERE dailyreport.employee_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $previous_data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        echo "Error preparing the statement: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <!-- Date Filter Form -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="filter_date">Filter by Date</label>
            <input type="date" id="filter_date" name="filter_date" class="form-control" value="<?php echo htmlspecialchars($filter_date); ?>">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Filter</button>
    </form>

    <?php if ($previous_data): ?>
        <h2 class="mb-4 mt-4">Previous Reports</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Duration (Hours)</th>
                    <th>Date and Time</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($previous_data as $data): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($data['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($data['time_duration']); ?></td>
                        <td><?php echo htmlspecialchars($data['datetime']); ?></td>
                        <td><?php echo htmlspecialchars($data['comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-5">
            <p>No data found.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';

$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

// Fetch employee details
$employee_query = "SELECT * FROM users WHERE employee_id = ?";
$employee_stmt = $conn->prepare($employee_query);

if (!$employee_stmt) {
    die("Error preparing statement: " . $conn->error);
}

$employee_stmt->bind_param("i", $employee_id);
$employee_stmt->execute();
$employee_result = $employee_stmt->get_result();

if ($employee_result->num_rows === 0) {
    die("No employee found with the given ID.");
}

$employee_data = $employee_result->fetch_assoc();
$employee_stmt->close();

// Fetch all report data
$report_query = "SELECT projects.project_name, dailyreport.time_duration, dailyreport.datetime, dailyreport.comment 
                 FROM dailyreport
                 INNER JOIN projects ON dailyreport.project_id = projects.id
                 WHERE dailyreport.employee_id = ?";

$report_stmt = $conn->prepare($report_query);

if (!$report_stmt) {
    die("Error preparing statement: " . $conn->error);
}

$report_stmt->bind_param("i", $employee_id);
$report_stmt->execute();
$report_result = $report_stmt->get_result();
$report_data = $report_result->fetch_all(MYSQLI_ASSOC);
$report_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link href="css/style.css" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-section {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .form-section h5 {
            margin-bottom: 15px;
            font-size: 1.2em;
            color: #333;
        }
        .form-group img {
            max-width: 100px;
            height: auto;
            display: block;
        }
    </style>
</head>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
       <?php include 'layouts/aside.php';?>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-4">
           
        <?php include 'layouts/header.php';?>

<div class="container mt-5">
    <h2 class="mb-4">Employee Details</h2>

    <div class="form-section">
        <h5>Personal Information</h5>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="employee_id">Employee ID</label>
                <input type="text" class="form-control" id="employee_id" value="<?php echo htmlspecialchars($employee_data['employee_id']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" value="<?php echo htmlspecialchars($employee_data['first_name']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" value="<?php echo htmlspecialchars($employee_data['last_name']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($employee_data['email']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="local_address">Local Address</label>
                <input type="text" class="form-control" id="local_address" value="<?php echo htmlspecialchars($employee_data['local_address']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="permanent_address">Permanent Address</label>
                <input type="text" class="form-control" id="permanent_address" value="<?php echo htmlspecialchars($employee_data['permanent_address']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="family_number">Family Number</label>
                <input type="text" class="form-control" id="family_number" value="<?php echo htmlspecialchars($employee_data['family_number']); ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="zip_code">Zip Code</label>
                <input type="text" class="form-control" id="zip_code" value="<?php echo htmlspecialchars($employee_data['zip_code']); ?>" readonly>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h5>Documents</h5>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="aadhaar_image">Aadhaar Image</label><br>
                <?php if ($employee_data['aadhaar_image']): ?>
                    <img src="../../Users/uploads/aadhaar/<?php echo htmlspecialchars($employee_data['aadhaar_image']); ?>" alt="Aadhaar Image" class="img-fluid">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>
            <div class="form-group col-md-4">
                <label for="pan_image">Pan Image</label><br>
                <?php if ($employee_data['pan_image']): ?>
                    <img src="../../Users/uploads/pan/<?php echo htmlspecialchars($employee_data['pan_image']); ?>" alt="Pan Image" class="img-fluid">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>
            <div class="form-group col-md-4">
                <label for="user_photo">User Photo</label><br>
                <?php if ($employee_data['user_photo']): ?>
                    <img src="../../Users/uploads/photos/<?php echo htmlspecialchars($employee_data['user_photo']); ?>" alt="User Photo" class="img-fluid">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>
        </div>

    <div class="form-section">
        <h5>Other Details</h5>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="cv">CV</label><br>
                <?php if ($employee_data['cv']): ?>
                    <a href="../../Users/uploads/cv/<?php echo htmlspecialchars($employee_data['cv']); ?>" target="_blank">View CV</a>
                <?php else: ?>
                    <p>No CV uploaded</p>
                <?php endif; ?>
            </div>
            <div class="form-group col-md-6">
                <label for="created_at">Created At</label>
                <input type="text" class="form-control" id="created_at" value="<?php echo !empty($employee_data['created_at']) ? (new DateTime($employee_data['created_at']))->format('d-m-y H:i:s') : 'N/A'; ?>" readonly>
            </div>
        </div>
    </div>

</div>
    <h2 class="mt-5">Daily Report</h2>
    <?php if ($report_data): ?>
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
                <?php foreach ($report_data as $data): ?>
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
            <p>No report data found.</p>
        </div>
    <?php endif; ?>
</div>
</div>
        </main>
    </div>
</div>
</body>
</html>

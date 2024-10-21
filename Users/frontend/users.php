<?php 
session_start(); 

// Check if user is logged in 
if (!isset($_SESSION['email'])) { 
    header("Location: login.php"); 
    exit(); 
} 
if (!isset($_SESSION['employee_id'])) { 
    header("Location: error.php?message=User ID not set"); 
    exit(); 
} 

$userid = $_SESSION['employee_id']; 
include '../../config.php'; 

// Validate the employee ID 
$query_check_id = "SELECT employee_id FROM employee WHERE employee_id = ?"; 
$stmt_check_id = $conn->prepare($query_check_id); 
if ($stmt_check_id === false) { 
    die("Error preparing the statement: " . $conn->error); 
} 
$stmt_check_id->bind_param("i", $userid); 
$stmt_check_id->execute(); 
$result_check_id = $stmt_check_id->get_result(); 
if ($result_check_id->num_rows === 0) { 
    header("Location: error.php?message=Invalid Employee ID"); 
    exit(); 
} 

// Fetch projects from the database 
$projects = []; 
$query_projects = "SELECT id, project_name FROM projects"; 
$result_projects = $conn->query($query_projects); 
if ($result_projects && $result_projects->num_rows > 0) { 
    while ($row = $result_projects->fetch_assoc()) { 
        $projects[] = $row; 
    } 
} 

$submitted_data = null; 
$data_submitted = false; 

// Handle form submission for project reporting 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_project'])) { 
    $project_id = $_POST['project']; 
    $time_duration = $_POST['time_duration']; 
    $comment = $_POST['comment']; 

    // Handle "Other" project selection 
    if ($project_id === 'other') { 
        $other_project_name = $_POST['other_project_name']; 
        // Insert new project 
        $query_insert_project = "INSERT INTO projects (project_name) VALUES (?)"; 
        $stmt_insert_project = $conn->prepare($query_insert_project); 
        if ($stmt_insert_project === false) { 
            die("Error preparing the insert project statement: " . $conn->error); 
        } 
        $stmt_insert_project->bind_param("s", $other_project_name); 
        $stmt_insert_project->execute(); 
        $project_id = $stmt_insert_project->insert_id; 
        $stmt_insert_project->close(); 
    } 

    // Insert daily report data 
    $query_insert = "INSERT INTO dailyreport (employee_id, project_id, time_duration, datetime, comment) VALUES (?, ?, ?, NOW(), ?)"; 
    $stmt_insert = $conn->prepare($query_insert); 
    if ($stmt_insert === false) { 
        die("Error preparing the insert daily report statement: " . $conn->error); 
    } 
    $stmt_insert->bind_param("iiss", $userid, $project_id, $time_duration, $comment); 
    if ($stmt_insert->execute()) { 
        $submitted_data = [ 
            'project_id' => $project_id, 
            'time_duration' => $time_duration, 
            'datetime' => date('Y-m-d H:i:s'), 
            'comment' => $comment 
        ]; 
        $_SESSION['data_submitted'] = true; 
    } else { 
        error_log("Error inserting data: (" . $conn->errno . ") " . $conn->error, 3, "/path/to/your/error.log"); 
        die("Error inserting data. Please try again."); 
    } 
    $stmt_insert->close(); 
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit(); 
} 

// Handle check-in/check-out submissions 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkin_action'])) { 
    $checkin_action = $_POST['checkin_action']; 
    $current_time = date('Y-m-d H:i:s'); 

    // Initialize the query variable
    $query_checkin_insert = '';

    switch ($checkin_action) { 
        case 'checkin': 
            $query_checkin_insert = "INSERT INTO employee_checkin (employee_id, check_in_time, status) VALUES (?, ?, ?)"; 
            $status = 1; // Check In 
            break; 
        case 'checkout': 
            $query_checkin_insert = "UPDATE employee_checkin SET check_out_time = ?, status = 0 WHERE employee_id = ? AND check_out_time IS NULL ORDER BY id DESC LIMIT 1";
            break; 
    } 

    // Prepare statement if $query_checkin_insert is not empty
    if (!empty($query_checkin_insert)) {
        $stmt_checkin_insert = $conn->prepare($query_checkin_insert); 
        if ($stmt_checkin_insert === false) { 
            die("Error preparing the check-in statement: " . $conn->error); 
        } 

        // Switch Case for Check-in Actions
        switch ($checkin_action) {
            case 'checkin':
                $stmt_checkin_insert->bind_param("isi", $userid, $current_time, $status);
                break;
            case 'checkout':
                $stmt_checkin_insert->bind_param("si", $current_time, $userid);
                break;
        }

        if ($stmt_checkin_insert->execute()) { 
            header("Location: " . $_SERVER['PHP_SELF']); 
            exit(); 
        } else { 
            error_log("Error inserting check-in data: (" . $conn->errno . ") " . $conn->error, 3, "/path/to/your/error.log"); 
            die("Error inserting check-in data. Please try again."); 
        } 
        $stmt_checkin_insert->close(); 
    } else {
        die("No valid check-in action selected.");
    }
} 

// Fetch the latest check-in/out data for the user 
$query_checkin_data = "SELECT check_in_time, check_out_time, status FROM employee_checkin WHERE employee_id = ? AND DATE(check_in_time) = CURDATE() ORDER BY id DESC LIMIT 1"; 
$stmt_checkin_data = $conn->prepare($query_checkin_data); 
$stmt_checkin_data->bind_param("i", $userid); 
$stmt_checkin_data->execute(); 
$result_checkin_data = $stmt_checkin_data->get_result(); 
$latest_checkin_data = $result_checkin_data->fetch_assoc(); 
$stmt_checkin_data->close(); 

// Determine if buttons should be disabled
$checkin_disabled = false;
$checkout_disabled = false;

if ($latest_checkin_data) {
    if ($latest_checkin_data['check_out_time'] === null) {
        $checkout_disabled = false; // User can check out
        $checkin_disabled = true; // User cannot check in
    } else {
        $checkout_disabled = true; // User cannot check out
        $checkin_disabled = true; // User cannot check in
    }
}

// Unset the session variable for submitted data
$data_submitted = isset($_SESSION['data_submitted']) && $_SESSION['data_submitted']; 
unset($_SESSION['data_submitted']); 
?> 




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="(link unavailable)">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100%;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .image-design {
            width: 90%;
            max-width: 90%;
            margin: 20px auto;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            height: 90%;
        }

        .header {
            background-color: #f0f0f0;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .header h1 {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .content {
            display: flex;
            padding: 20px;
        }

        .left-panel {
            flex: 1;
            margin-right: 20px;
        }

        .right-panel {
            flex: 2;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 10px;
            padding: 20px;
        }

        .card h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer {
            background-color: #f0f0f0;
            padding: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        .btn {
            margin-right: 10px;
        }
        .card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    padding: 20px;
    transition: transform 0.2s;
}

.card:hover {
    transform: scale(1.02);
}

.form-label {
    font-weight: bold;
    margin-bottom: 5px;
}

.form-control {
    border-radius: 5px;
    border: 1px solid #ccc;
    padding: 10px;
    margin: 5px;
}

.btn {
    border-radius: 5px;
    padding: 10px 15px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

    </style>
</head>
<body>

<div class="image-design">
  <div class="header">
    <h1> Dashboard</h1>
  </div>
  <div class="content">
    <div class="left-panel">
      <div class="card">
        <h3>Check In/Out</h3>
        <form method="post" action="" class="border p-4 rounded bg-light">
            <button type="submit" name="checkin_action" value="checkin" class="btn btn-success" <?= $checkin_disabled ? 'disabled' : ''; ?>>Check In</button>
            <button type="button" name="checkin_action" value="checkout" class="btn btn-danger" <?= $checkout_disabled ? 'disabled' : ''; ?> onclick="confirmCheckout()">Check Out</button>
        
        
        </form>
      </div>
    </div>
    <div class="right-panel">
    <div class="card">
    <h3>Daily Report</h3>
    <?php if ($latest_checkin_data && $latest_checkin_data['check_out_time'] === null): ?>
        <form method="post" action="" class="border p-4 rounded bg-light">
            <div class="form-group m-1" >
                <label for="project" class="form-label">Select Project</label>
                <select name="project" style="width:90%; margin-left:5rem;" id="project" class="form-control m-2" required>
                    <option value="">Select a project</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?= $project['id']; ?>"><?= htmlspecialchars($project['project_name']); ?></option>
                    <?php endforeach; ?>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group m-1" id="other_project_name_div" style="display:none;">
                <label for="other_project_name" class="form-label">Other Project Name</label>
                <input type="text" style="width:90%; margin-left:5rem;"name="other_project_name" id="other_project_name" class="form-control m-2" />
            </div>
            <div class="form-group m-1">
                <label for="time_duration" class="form-label">Time Duration</label>
                <input type="text" style="width:90%; margin-left:5rem;" name="time_duration" id="time_duration" class="form-control m-1" placeholder="between 0 to 8 hours" required />
            </div>
            <div class="form-group m-1">
            <label for="comment" class="form-label">Comment</label>

                <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="enter the daily report desc..." style="width:90%; margin-left:5rem;"></textarea>
            </div>
            <button type="submit" name="submit_project" class="btn btn-primary">Submit Report</button>
        </form>
    <?php else: ?>
        <p class="alert alert-info">You cannot submit a report until you check in.</p>
    <?php endif; ?>
</div>

    </div>
  </div>
  <div class="footer">
    <p>&copy; 2024 Employee Dashboard</p>
  </div>
</div>

<script src="(link unavailable)"></script>
<script src="(link unavailable)"></script>
<script src="(link unavailable)"></script>
<script>
    // Show the other project name field when "Other" is selected
    document.getElementById('project').addEventListener('change', function() {
        var otherProjectNameDiv = document.getElementById('other_project_name_div');
        if (this.value === 'other') {
            otherProjectNameDiv.style.display = 'block';
        } else {
            otherProjectNameDiv.style.display = 'none';
        }
    });

    function confirmCheckout() {
        if (confirm("Are you sure you want to check out?")) {
            // If confirmed, submit the form by creating a hidden input and triggering submit
            var form = document.querySelector('form');
            // Assuming there's only one form, modify if necessary
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'checkin_action';
            input.value = 'checkout';
            form.appendChild(input);
            form.submit();
        }
    }
</script>
</body>
</html>

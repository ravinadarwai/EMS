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

// Error handling if prepare() fails
if ($stmt_check_id === false) {
    die("Error preparing the statement: " . $conn->error);
}

$stmt_check_id->bind_param("i", $userid);
$stmt_check_id->execute();
$result_check_id = $stmt_check_id->get_result();

// if ($result_check_id->num_rows === 0) {
//     header("Location: error.php?message=Invalid Employee ID");
//     exit();
// }

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
$data_submitted = false; // Initialize $data_submitted to false

// Handle form submission
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
        $_SESSION['data_submitted'] = true; // Set session variable
    } else {
        error_log("Error inserting data: (" . $conn->errno . ") " . $conn->error, 3, "/path/to/your/error.log");
        die("Error inserting data. Please try again.");
    }

    $stmt_insert->close();
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page to avoid form resubmission on refresh
    exit();
}

$data_submitted = isset($_SESSION['data_submitted']) && $_SESSION['data_submitted'];
unset($_SESSION['data_submitted']); // Clear the session variable after showing the modal

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Gilroy', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            max-width: 1000px;
        }
        .card {
            margin-top: 50px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.5rem;
            text-align: center;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .form-label {
            font-weight: bold;
        }
        #other_project {
            display: none;
        }
    </style>
    <script>
        function toggleOtherProjectField() {
            var projectSelect = document.getElementById('project');
            var otherProjectField = document.getElementById('other_project');
            if (projectSelect.value === 'other') {
                otherProjectField.style.display = 'block';
                otherProjectField.querySelector('input').required = true;
            } else {
                otherProjectField.style.display = 'none';
                otherProjectField.querySelector('input').required = false;
            }
        }

        function closeModalAfterTimeout() {
            setTimeout(function() {
                var modalElement = document.getElementById('submissionModal');
                var modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }, 30000); // 30 seconds
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('submissionModal')) {
                var submissionModal = new bootstrap.Modal(document.getElementById('submissionModal'));
                if (<?php echo json_encode($data_submitted); ?>) {
                    submissionModal.show();
                    closeModalAfterTimeout();
                }
            }
        });
    </script>
</head>
<body>

<?php if ($data_submitted): ?>
    <!-- Submission Success Modal -->
    <div class="modal fade" id="submissionModal" tabindex="-1" aria-labelledby="submissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submissionModalLabel">Submission Successful</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Your project submission has been recorded successfully.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="card">
        <div class="card-header">
            Project Submission Form
        </div>
        <div class="card-body">
            <h5 class="card-title">Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?> <?php echo htmlspecialchars($_SESSION['last_name']); ?>!</h5>
            <p>Your email is: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            <a href="../backend/logout.php" class="btn btn-danger mb-4">Logout</a>

            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="project" class="form-label">Select Project</label>
                        <select id="project" name="project" class="form-select" onchange="toggleOtherProjectField()" required>
                            <option value="" disabled selected>Select a project</option>
                            <?php foreach ($projects as $project): ?>
                                <option value="<?php echo htmlspecialchars($project['id']); ?>">
                                    <?php echo htmlspecialchars($project['project_name']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="time_duration" class="form-label">Time Duration (Hours)</label>
                        <input type="number" id="time_duration" name="time_duration" class="form-control" required min="1" max="24" placeholder="Enter hours worked">
                    </div>
                </div>

                <div id="other_project" class="mb-3">
                    <label for="other_project_name" class="form-label">Specify Other Project Name</label>
                    <input type="text" id="other_project_name" name="other_project_name" class="form-control" placeholder="Enter other project name">
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Comment</label>
                    <textarea id="comment" name="comment" class="form-control" rows="4" placeholder="Enter your comments"></textarea>
                </div>

                <button type="submit" name="submit_project" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                                
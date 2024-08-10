<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include '../config.php'; // Adjust path if necessary

// Fetch projects from the database
$query = "SELECT id, project_name FROM projects";
$result = mysqli_query($conn, $query);
$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Set default values
$default_datetime = date('Y-m-d\TH:i'); // Default to current date and time

// Initialize submitted data variable
$submitted_data = null;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_project = $_POST['project'];
    $datetime = $_POST['datetime'];
    $comment = $_POST['comment'];

    // Store submitted data
    $submitted_data = [
        'project' => $selected_project,
        'datetime' => $datetime,
        'comment' => $comment,
    ];
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?> <?php echo htmlspecialchars($_SESSION['last_name']); ?>!</h1>
    <p>Your email is: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <a href="../backend/logout.php" class="btn btn-danger">Logout</a>

    <!-- Form for selecting project, date/time, and comment -->
    <form method="POST" action="">
        <div class="mb-3">
            <label for="project" class="form-label">Select Project</label>
            <select id="project" name="project" class="form-select" required>
                <option value="" disabled selected>Select a project</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo htmlspecialchars($project['id']); ?>">
                        <?php echo htmlspecialchars($project['project_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="datetime" class="form-label">Date and Time</label>
            <input type="datetime-local" id="datetime" name="datetime" class="form-control" value="<?php echo $default_datetime; ?>" required>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Comment</label>
            <textarea id="comment" name="comment" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <!-- Display submitted data -->
    <?php if ($submitted_data): ?>
        <div class="mt-5">
            <h2>Submitted Data</h2>
            <div class="mb-3">
                <strong>Project:</strong> <?php echo htmlspecialchars($submitted_data['project']); ?>
            </div>
            <div class="mb-3">
                <strong>Date and Time:</strong> <?php echo htmlspecialchars($submitted_data['datetime']); ?>
            </div>
            <div class="mb-3">
                <strong>Comment:</strong> <?php echo htmlspecialchars($submitted_data['comment']); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

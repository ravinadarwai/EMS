<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch existing project data
    $query = "SELECT * FROM projects WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();

    if (!$project) {
        echo "Project not found!";
        exit();
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $project_name = $_POST['project_name'];
        $project_description = $_POST['project_description'];
        $client_company_name = $_POST['client_company_name'];
        $client_name = $_POST['client_name'];
        
        $updateQuery = "UPDATE projects SET project_name = ?, project_description = ?, client_company_name = ?, client_name = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("ssssi", $project_name, $project_description, $client_company_name, $client_name, $id);

        if ($stmt->execute()) {
            header("Location: ../admin_dashboard.php"); // Redirect after successful update
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
        
        $stmt->close();
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Update Project Details</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="project_name" class="form-label">Project Name</label>
            <input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="project_description" class="form-label">Project Description</label>
            <textarea class="form-control" id="project_description" name="project_description" required><?php echo htmlspecialchars($project['project_description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="client_company_name" class="form-label">Client Company Name</label>
            <input type="text" class="form-control" id="client_company_name" name="client_company_name" value="<?php echo htmlspecialchars($project['client_company_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="client_name" class="form-label">Client Name</label>
            <input type="text" class="form-control" id="client_name" name="client_name" value="<?php echo htmlspecialchars($project['client_name']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


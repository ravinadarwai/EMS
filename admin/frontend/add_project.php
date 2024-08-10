<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';
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
    </style>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'layouts/aside.php';?>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-4">
            <?php include 'layouts/header.php';?>
            <h2 class="text-center">PROJECT INFORMATION FORM</h2>
            <form method="POST" action="../backend/add_project.php">
                <div class="form-group">
                    <label for="projectName">Project Name</label>
                    <input type="text" class="form-control" id="projectName" name="projectName" placeholder="Enter project name" required>
                </div>
                <div class="form-group">
                    <label for="projectDescription">Project Description</label>
                    <textarea class="form-control" id="projectDescription" name="projectDescription" rows="4" placeholder="Enter project description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="clientCompanyName">Client Company Name</label>
                    <input type="text" class="form-control" id="clientCompanyName" name="clientCompanyName" placeholder="Enter client company name" required>
                </div>
                <div class="form-group">
                    <label for="clientName">Client Name</label>
                    <input type="text" class="form-control" id="clientName" name="clientName" placeholder="Enter client name" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

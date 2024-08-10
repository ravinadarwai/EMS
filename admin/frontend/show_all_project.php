<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';

// Fetch completed projects from the database
$queryCompletedProjects = "SELECT * FROM projects WHERE status = 'complete'";
$resultCompletedProjects = mysqli_query($conn, $queryCompletedProjects);

// Fetch pending projects from the database
$queryPendingProjects = "SELECT * FROM projects WHERE status = 'pending'";
$resultPendingProjects = mysqli_query($conn, $queryPendingProjects);
?>

<!doctype html>
<html lang="en"> 
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
    /* Add your custom styles here */
    /* The styles you already provided */
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
       <?php include 'layouts/aside.php';?>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-4">
           
        <?php include 'layouts/header.php';?>

            <div class="tab-content">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Complete Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab2">Pending Projects</a>
                    </li>
                </ul>

                <div id="tab1" class="tab-pane fade show active mt-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Project Name</th>
                                <th>Description</th>
                                <th>Client Company</th>
                                <th>Client Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            while ($row = mysqli_fetch_assoc($resultCompletedProjects)): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['client_company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
    <?php
    // Check if created_at is not null or empty
    if (!empty($row['created_at'])) {
        // Create a DateTime object from the created_at value
        $date = new DateTime($row['created_at']);
        // Format the date and time
        echo $date->format('d-m-y H:i:s');
    } else {
        echo 'N/A';
    }
    ?>
</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div id="tab2" class="tab-pane fade mt-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Project Name</th>
                                <th>Description</th>
                                <th>Client Company</th>
                                <th>Client Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            while ($row = mysqli_fetch_assoc($resultPendingProjects)): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['client_company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
    <?php
    // Check if created_at is not null or empty
    if (!empty($row['created_at'])) {
        // Create a DateTime object from the created_at value
        $date = new DateTime($row['created_at']);
        // Format the date and time
        echo $date->format('d-m-y H:i:s');
    } else {
        echo 'N/A';
    }
    ?>
</td>
                                    <td>
                                        <a href="action/mark_complete.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to mark this project as complete?');">Mark as Complete</a>
                                        <a href="action/update_project.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Update</a>
                                        <a href="action/delete_project.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this project?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

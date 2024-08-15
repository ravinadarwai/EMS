<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';

// Fetch data from the database
$queryPersonalInfo = "SELECT * FROM users";
$resultPersonalInfo = mysqli_query($conn, $queryPersonalInfo);

// Fetch employee data from the database
$queryEmployee = "SELECT * FROM employee";
$resultEmployee = mysqli_query($conn, $queryEmployee);
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
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Employee Data</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab2"> Pending Employee Data</a>
                    </li>
                </ul>

                <div id="tab1" class="tab-pane fade show active mt-5">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Local Address</th>
                                <th>Permanent Address</th>
                                <th>Family Number</th>
                                <th>Zip Code</th>
                                <th>Aadhaar Image</th>
                                <th>Pan Image</th>
                                <th>CV</th>
                                <th>User Photo</th>
                                <th>Created At</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            while ($row = mysqli_fetch_assoc($resultEmployee)): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['local_address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['permanent_address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['family_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['zip_code']); ?></td>
                                    <td>
                                        <?php if ($row['aadhaar_image']): ?>
                                            <img src="../../Users/uploads/aadhaar/<?php echo htmlspecialchars($row['aadhaar_image']); ?>" alt="Aadhaar Image" width="100"/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['pan_image']): ?>
                                            <img src="../../Users/uploads/pan/<?php echo htmlspecialchars($row['pan_image']); ?>" alt="Pan Image" width="100"/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['cv']): ?>
                                            <a href="../../Users/uploads/cv/<?php echo htmlspecialchars($row['cv']); ?>" target="_blank">View CV</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['user_photo']): ?>
                                            <img src="../../Users/uploads/photos/<?php echo htmlspecialchars($row['user_photo']); ?>" alt="User Photo" width="100"/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
    <?php
    // Check if created_at is not null or empty
    if (!empty($row['created_at'])) {
        // Create a DateTime object from the created_at value
        $date = new DateTime($row['created_at']);
        // Format the date and time
        echo $date->format('d-m-y   H:i:s');
    } else {
        echo 'N/A';
    }
    ?>
</td>
<td>
                                        <a href="taskdata?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-success btn-sm" >Show</a>
                                        <a href="action/update.php?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-warning btn-sm">Update</a>
                                        <a href="action/delete.php?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
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
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Local Address</th>
                                <th>Permanent Address</th>
                                <th>Family Number</th>
                                <th>Zip Code</th>
                                <th>Aadhaar Image</th>
                                <th>Pan Image</th>
                                <th>CV</th>
                                <th>User Photo</th>
                                <th>Created At</th>
                                <th>Actions</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sno = 1;
                            while ($row = mysqli_fetch_assoc($resultPersonalInfo)): ?>
                                <tr>
                                    <td><?php echo $sno++; ?></td>
                                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['local_address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['permanent_address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['family_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['zip_code']); ?></td>
                                    <td>
                                        <?php if ($row['aadhaar_image']): ?>
                                            <img src="../../Users/uploads/aadhaar/<?php echo htmlspecialchars($row['aadhaar_image']); ?>" alt="Aadhaar Image" width="100"/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['pan_image']): ?>
                                            <img src="../../Users/uploads/pan/<?php echo htmlspecialchars($row['pan_image']); ?>" alt="Pan Image" width="100"/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['cv']): ?>
                                            <a href="../../Users/uploads/cv/<?php echo htmlspecialchars($row['cv']); ?>" target="_blank">View CV</a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['user_photo']): ?>
                                            <img src="../../Users/uploads/photos/<?php echo htmlspecialchars($row['user_photo']); ?>" alt="User Photo" width="100"/>
                                        <?php endif; ?>
                                    </td>
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
                                        <a href="action/verify.php?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to verify this record?');">Verify</a>
                                        <a href="action/update.php?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-warning btn-sm">Update</a>
                                        <a href="action/delete.php?employee_id=<?php echo $row['employee_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
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
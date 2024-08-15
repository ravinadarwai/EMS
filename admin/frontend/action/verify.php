<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../../config.php';

// Get the employee ID from the query string
$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;

// Fetch the user details from the users table
$query = "SELECT * FROM users WHERE employee_id = $employee_id";
$result = mysqli_query($conn, $query);

if (!$result) {
    // Display the SQL error
    die("Error in query: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

if ($user) {
    // Insert the user data into the employee table
    $insertQuery = "INSERT INTO employee (first_name, last_name, email, local_address, permanent_address, family_number, zip_code, aadhaar_image, pan_image, cv, user_photo, created_at)
                    VALUES ('" . mysqli_real_escape_string($conn, $user['first_name']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['last_name']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['email']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['local_address']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['permanent_address']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['family_number']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['zip_code']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['aadhaar_image']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['pan_image']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['cv']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['user_photo']) . "', 
                            '" . mysqli_real_escape_string($conn, $user['created_at']) . "')";

    if (mysqli_query($conn, $insertQuery)) {
        // Update the verification status in the users table
        $updateQuery = "UPDATE users SET verification_status = 'verified' WHERE id = $employee_id";
        mysqli_query($conn, $updateQuery);
        
        // Redirect to the admin dashboard after successful update
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "User not found.";
}
?>

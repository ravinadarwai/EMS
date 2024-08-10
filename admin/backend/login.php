<?php
// Include database configuration
include '../../config.php';

// Start session
session_start();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query
    $sql = "SELECT * FROM admin WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare() failed
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if admin exists
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        $_SESSION['admin_email'] = $email; // Store email in session
        $_SESSION['admin_name'] = $admin['name']; // Store name in session

        header("Location: ../frontend/dashboard"); // Redirect to admin dashboard
        exit();
    } else {
        $_SESSION['admin_error'] = "Invalid email or password";
        header("Location: ../frontend/login"); // Redirect back to login page
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
include '../../config.php';

// Start session for storing login status
session_start();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please enter both email and password.";
        header("Location: ../frontend/error.php?message=" . urlencode($_SESSION['error']));
        exit();
    }

    // Prepare SQL query to fetch user
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare() failed
    if ($stmt === false) {
        $_SESSION['error'] = "Error preparing SQL statement: " . $conn->error;
        header("Location: ../frontend/error.php?message=" . urlencode($_SESSION['error']));
        exit();
    }

    // Bind parameters and execute
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        // Redirect to users page
        header("Location: ../frontend/users");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: ../frontend/error.php?message=" . urlencode($_SESSION['error']));
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If the request is not POST, redirect to the login form or error page
    $_SESSION['error'] = "Invalid request method.";
    header("Location: ../frontend/error.php?message=" . urlencode($_SESSION['error']));
    exit();
}

<?php
// Include database configuration
include '../../config.php';

// Start session
session_start();

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL query
    $sql = "SELECT * FROM personal_info WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare() failed
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc(); // Fetch user data
        $_SESSION['email'] = $email; // Store email in session
        $_SESSION['first_name'] = $user['first_name']; // Store first name in session
        $_SESSION['last_name'] = $user['last_name']; // Store last name in session

        header("Location: ../frontend/users.php"); // Redirect to users page
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: ../frontend/login.php"); // Redirect back to login page
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $project_name = $_POST['projectName'];
    $project_description = $_POST['projectDescription'];
    $client_company_name = $_POST['clientCompanyName'];
    $client_name = $_POST['clientName'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO projects (project_name, project_description, client_company_name, client_name) VALUES (?, ?, ?, ?)");

    // Check if preparation was successful
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    if (!$stmt->bind_param("ssss", $project_name, $project_description, $client_company_name, $client_name)) {
        die("Bind failed: " . $stmt->error);
    }

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to a success page or display a success message
        header("Location: success.php");
        exit();
    } else {
        die("Execute failed: " . $stmt->error);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

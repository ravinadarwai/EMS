<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../../config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Update the project status to 'complete'
    $query = "UPDATE projects SET status = 'complete' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: ../show_all_project.php"); // Redirect to the dashboard or another appropriate page
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

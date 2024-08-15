<?php
include '../../../config.php';

if (isset($_GET['employee_id'])) {
    $id = $_GET['employee_id'];

    // Delete record
    $query = "DELETE FROM users WHERE employee_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); // Use the correct variable here
    $stmt->execute();

    // Check if the record was successfully deleted
    if ($stmt->affected_rows > 0) {
        header("Location: ../dashboard.php");
        exit();
    } else {
        echo "Error: Unable to delete the record.";
        exit();
    }
} else {
    echo "No ID provided!";
    exit();
}
?>

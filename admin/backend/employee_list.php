<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.php");
    exit();
}

include '../../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the record details
    $query = "SELECT * FROM personal_info WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();

    if ($record) {
        // Prepare to insert data into employee table
        $query = "INSERT INTO employee (first_name, last_name, email, local_address, permanent_address, family_number, zip_code, aadhaar_image, pan_image, cv, user_photo, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssssssssss",
            $record['first_name'],
            $record['last_name'],
            $record['email'],
            $record['local_address'],
            $record['permanent_address'],
            $record['family_number'],
            $record['zip_code'],
            $record['aadhaar_image'],
            $record['pan_image'],
            $record['cv'],
            $record['user_photo'],
            $record['created_at']
        );
        $stmt->execute();

        // Delete the record from personal_info
        $query = "DELETE FROM personal_info WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Record not found!";
        exit();
    }
} else {
    echo "No ID provided!";
    exit();
}
?>

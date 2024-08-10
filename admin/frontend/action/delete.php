<?php
include '../../../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete record
    $query = "DELETE FROM personal_info WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ../dashboard");
    exit();
} else {
    echo "No ID provided!";
    exit();
}
?>

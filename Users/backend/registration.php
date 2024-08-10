<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config file
include '../../config.php';
session_start(); // Start session for storing messages

// Initialize message variables
$_SESSION['message'] = '';
$_SESSION['error'] = '';

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $local_address = $_POST['local_address'];
    $permanent_address = $_POST['permanent_address'];
    $family_number = $_POST['family_number'];
    $zip_code = $_POST['zip_code'];

    // File uploads
    $aadhaar_image = uploadFile('aadhaar_image', 'aadhaar');
    $pan_image = uploadFile('pan_image', 'pan');
    $cv = uploadFile('cv', 'cv');
    $user_photo = uploadFile('user_photo', 'photos');

    if ($aadhaar_image === "Error" || $pan_image === "Error" || $cv === "Error" || $user_photo === "Error") {
        $_SESSION['error'] = "Error uploading one or more files.";
        header("Location: registration.html"); // Redirect back to registration page
        exit();
    }

    // Insert data into the database
    $sql = "INSERT INTO personal_info (first_name, last_name, email, password, local_address, permanent_address, family_number, zip_code, aadhaar_image, pan_image, cv, user_photo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssss", $first_name, $last_name, $email, $password, $local_address, $permanent_address, $family_number, $zip_code, $aadhaar_image, $pan_image, $cv, $user_photo);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Personal information saved successfully.";
        header("Location: home.php"); // Redirect to home page
    } else {
        $_SESSION['error'] = "Database Error: " . $stmt->error;
        header("Location: ../frontend/registration.php"); // Redirect back to registration page
    }

    $stmt->close();
    $conn->close();
}

// Function to handle file uploads
function uploadFile($inputName, $folder) {
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
        $file = $_FILES[$inputName];
        $targetDir = "uploads/" . $folder . "/";
        $targetFile = $targetDir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is a real image (for images)
        if ($folder == 'aadhaar' || $folder == 'pan' || $folder == 'photos') {
            $check = getimagesize($file["tmp_name"]);
            if ($check === false) {
                $uploadOk = 0;
            }
        }

        // Allow certain file formats
        if ($folder == 'cv') {
            if ($imageFileType != "pdf") {
                $uploadOk = 0;
            }
        } else if ($folder == 'photos') {
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $uploadOk = 0;
            }
        } else {
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                $uploadOk = 0;
            }
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            return "Error";
        } else {
            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                return $targetFile;
            } else {
                return "Error";
            }
        }
    }
    return "Error";
}
?>

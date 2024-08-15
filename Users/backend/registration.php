<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config file
include '../../config.php';
include 'functions.php'; // Include the file with the uploadFile function
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
    $job_profile = $_POST['job_profile'];

    // File uploads
    $aadhaar_image = uploadFile('aadhaar_image', 'aadhaar');
    $pan_image = uploadFile('pan_image', 'pan');
    $cv = uploadFile('cv', 'cv');
    $user_photo = uploadFile('user_photo', 'photo');

    if (!$aadhaar_image || !$pan_image || !$cv || ($user_photo === false && $_FILES['user_photo']['error'] != 4)) {
        $_SESSION['error'] = 'Error uploading files.';
        header("Location: ../frontend/users.php");
        exit;
    }

    // Prepare SQL insert statement
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, local_address, permanent_address, family_number, zip_code, job_profile, aadhaar_image, pan_image, cv, user_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssss", $first_name, $last_name, $email, $password, $local_address, $permanent_address, $family_number, $zip_code, $job_profile, $aadhaar_image, $pan_image, $cv, $user_photo);

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Data submitted successfully!';
    } else {
        $_SESSION['error'] = 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: ../frontend/personal_info_form.php");
    exit;
}



function uploadFile($inputName, $type) {
    $allowedTypes = [
        'aadhaar' => ['image/jpeg', 'image/png'],
        'pan' => ['image/jpeg', 'image/png'],
        'cv' => ['application/pdf'],
        'photo' => ['image/jpeg', 'image/png']
    ];

    if (!isset($_FILES[$inputName])) return false;

    $file = $_FILES[$inputName];
    $fileType = $file['type'];
    $fileTmpName = $file['tmp_name'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (!in_array($fileType, $allowedTypes[$type])) {
            return false;
        }

        $uploadDir = '../uploads/' . $type . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpName, $filePath)) {
            return $fileName;
        }
    }

    return false;
}
?>


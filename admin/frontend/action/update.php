<?php
include '../../../config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch record details
    $query = "SELECT * FROM personal_info WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $local_address = $_POST['local_address'];
        $permanent_address = $_POST['permanent_address'];
        $family_number = $_POST['family_number'];
        $zip_code = $_POST['zip_code'];

        // Handle file uploads
        $aadhaar_image = $record['aadhaar_image'];
        $pan_image = $record['pan_image'];
        $cv = $record['cv'];
        $user_photo = $record['user_photo'];

        if (isset($_FILES['aadhaar_image']) && $_FILES['aadhaar_image']['error'] == UPLOAD_ERR_OK) {
            $aadhaar_image = 'uploads/' . basename($_FILES['aadhaar_image']['name']);
            move_uploaded_file($_FILES['aadhaar_image']['tmp_name'], $aadhaar_image);
        }

        if (isset($_FILES['pan_image']) && $_FILES['pan_image']['error'] == UPLOAD_ERR_OK) {
            $pan_image = 'uploads/' . basename($_FILES['pan_image']['name']);
            move_uploaded_file($_FILES['pan_image']['tmp_name'], $pan_image);
        }

        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
            $cv = 'uploads/' . basename($_FILES['cv']['name']);
            move_uploaded_file($_FILES['cv']['tmp_name'], $cv);
        }

        if (isset($_FILES['user_photo']) && $_FILES['user_photo']['error'] == UPLOAD_ERR_OK) {
            $user_photo = 'uploads/' . basename($_FILES['user_photo']['name']);
            move_uploaded_file($_FILES['user_photo']['tmp_name'], $user_photo);
        }

        // Update record
        $query = "UPDATE personal_info SET first_name = ?, last_name = ?, email = ?, local_address = ?, permanent_address = ?, family_number = ?, zip_code = ?, aadhaar_image = ?, pan_image = ?, cv = ?, user_photo = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssii", $first_name, $last_name, $email, $local_address, $permanent_address, $family_number, $zip_code, $aadhaar_image, $pan_image, $cv, $user_photo, $id);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No ID provided!";
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Update Record</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($record['first_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($record['last_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($record['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="local_address" class="form-label">Local Address</label>
            <textarea class="form-control" id="local_address" name="local_address" required><?php echo htmlspecialchars($record['local_address']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="permanent_address" class="form-label">Permanent Address</label>
            <textarea class="form-control" id="permanent_address" name="permanent_address" required><?php echo htmlspecialchars($record['permanent_address']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="family_number" class="form-label">Family Number</label>
            <input type="text" class="form-control" id="family_number" name="family_number" value="<?php echo htmlspecialchars($record['family_number']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="zip_code" class="form-label">Zip Code</label>
            <input type="text" class="form-control" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($record['zip_code']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="aadhaar_image" class="form-label">Aadhaar Image</label>
            <input type="file" class="form-control" id="aadhaar_image" name="aadhaar_image">
            <?php if ($record['aadhaar_image']): ?>
                <img src="../../../<?php echo htmlspecialchars($record['aadhaar_image']); ?>" alt="Aadhaar Image" width="100"/>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="pan_image" class="form-label">PAN Image</label>
            <input type="file" class="form-control" id="pan_image" name="pan_image">
            <?php if ($record['pan_image']): ?>
                <img src="../../../<?php echo htmlspecialchars($record['pan_image']); ?>" alt="PAN Image" width="100"/>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="cv" class="form-label">CV</label>
            <input type="file" class="form-control" id="cv" name="cv">
            <?php if ($record['cv']): ?>
                <a href="../../../<?php echo htmlspecialchars($record['cv']); ?>" target="_blank">View CV</a>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="user_photo" class="form-label">User Photo</label>
            <input type="file" class="form-control" id="user_photo" name="user_photo">
            <?php if ($record['user_photo']): ?>
                <img src="../../../<?php echo htmlspecialchars($record['user_photo']); ?>" alt="User Photo" width="100"/>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

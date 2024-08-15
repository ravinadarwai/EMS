<?php
include '../../../config.php';

if (isset($_GET['employee_id'])) {
    $employee_id = $_GET['employee_id'];

    // Fetch record details
    $query = "SELECT * FROM users WHERE employee_id = ?";
    $stmt = $conn->prepare($query);

    // Check if the statement was prepared successfully
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $employee_id);
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

        // Set upload directory
        $upload_dir = '../../Users/uploads/';

        // Handle file uploads
        $aadhaar_image = $record['aadhaar_image'];
        $pan_image = $record['pan_image'];
        $cv = $record['cv'];
        $user_photo = $record['user_photo'];

        if (isset($_FILES['aadhaar_image']) && $_FILES['aadhaar_image']['error'] == UPLOAD_ERR_OK) {
            $aadhaar_image = $upload_dir . 'aadhaar/' . basename($_FILES['aadhaar_image']['name']);
            if (!move_uploaded_file($_FILES['aadhaar_image']['tmp_name'], $aadhaar_image)) {
                die("Error uploading Aadhaar Image");
            }
        }

        if (isset($_FILES['pan_image']) && $_FILES['pan_image']['error'] == UPLOAD_ERR_OK) {
            $pan_image = $upload_dir . 'pan/' . basename($_FILES['pan_image']['name']);
            if (!move_uploaded_file($_FILES['pan_image']['tmp_name'], $pan_image)) {
                die("Error uploading Pan Image");
            }
        }

        if (isset($_FILES['cv']) && $_FILES['cv']['error'] == UPLOAD_ERR_OK) {
            $cv = $upload_dir . 'cv/' . basename($_FILES['cv']['name']);
            if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv)) {
                die("Error uploading CV");
            }
        }

        if (isset($_FILES['user_photo']) && $_FILES['user_photo']['error'] == UPLOAD_ERR_OK) {
            $user_photo = $upload_dir . 'photos/' . basename($_FILES['user_photo']['name']);
            if (!move_uploaded_file($_FILES['user_photo']['tmp_name'], $user_photo)) {
                die("Error uploading User Photo");
            }
        }

        // Update record
        $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, local_address = ?, permanent_address = ?, family_number = ?, zip_code = ?, aadhaar_image = ?, pan_image = ?, cv = ?, user_photo = ? WHERE employee_id = ?";
        $stmt = $conn->prepare($query);

        // Check if the statement was prepared successfully
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("sssssssssssi", $first_name, $last_name, $email, $local_address, $permanent_address, $family_number, $zip_code, $aadhaar_image, $pan_image, $cv, $user_photo, $employee_id);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No ID provided!";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Information</title>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

input[type="text"],
input[type="email"],
input[type="file"] {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button[type="submit"]:hover {
    background-color: #218838;
}

</style></head>
<body>
    <div class="container">
        <h2>Update User Information</h2>
        <form action="update.php?employee_id=<?php echo $_GET['employee_id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($record['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($record['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($record['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="local_address">Local Address:</label>
                <input type="text" id="local_address" name="local_address" value="<?php echo htmlspecialchars($record['local_address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="permanent_address">Permanent Address:</label>
                <input type="text" id="permanent_address" name="permanent_address" value="<?php echo htmlspecialchars($record['permanent_address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="family_number">Family Number:</label>
                <input type="text" id="family_number" name="family_number" value="<?php echo htmlspecialchars($record['family_number']); ?>" required>
            </div>

            <div class="form-group">
                <label for="zip_code">Zip Code:</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($record['zip_code']); ?>" required>
            </div>

            <div class="form-group">
                <label for="aadhaar_image">Aadhaar Image:</label>
                <input type="file" id="aadhaar_image" name="aadhaar_image">
            </div>

            <div class="form-group">
                <label for="pan_image">Pan Image:</label>
                <input type="file" id="pan_image" name="pan_image">
            </div>

            <div class="form-group">
                <label for="cv">CV:</label>
                <input type="file" id="cv" name="cv">
            </div>

            <div class="form-group">
                <label for="user_photo">User Photo:</label>
                <input type="file" id="user_photo" name="user_photo">
            </div>

            <button type="submit">Update</button>
        </form>
    </div>
    <script>document.querySelector('form').addEventListener('submit', function(event) {
    var firstName = document.getElementById('first_name').value;
    var lastName = document.getElementById('last_name').value;
    var email = document.getElementById('email').value;

    if (!firstName || !lastName || !email) {
        alert("Please fill out all required fields.");
        event.preventDefault();
    }
});
</script>
</body>
</html>

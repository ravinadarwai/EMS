<?php
session_start();
?>



<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Personal Information Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: lightgrey;
        }
        .form-container {
            max-width: 1000px;
            margin: auto;
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .file-upload-section {
            border: 1px dashed #ccc;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .file-upload-section label {
            display: block;
            margin-bottom: 0.5rem;
        }
        .file-upload-section img {
            max-width: 100px;
            display: block;
            margin-top: 0.5rem;
        }
        .form-control {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container py-5">
    <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['message']; ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <div class="form-container">
            <h3 class="mb-4">Personal Information</h3>
            <form method="POST" action="../backend/registration" enctype="multipart/form-data">
               
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="text" id="firstName" name="first_name" class="form-control" required />
                            <label class="form-label" for="firstName">First Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="text" id="lastName" name="last_name" class="form-control" required />
                            <label class="form-label" for="lastName">Last Name</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="email" id="email" name="email" class="form-control" required />
                            <label class="form-label" for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="password" id="password" name="password" class="form-control" required />
                            <label class="form-label" for="password">Password</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="text" id="localAddress" name="local_address" class="form-control" required />
                            <label class="form-label" for="localAddress">Local Address</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="text" id="permanentAddress" name="permanent_address" class="form-control" required />
                            <label class="form-label" for="permanentAddress">Permanent Address</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="text" id="familyNumber" name="family_number" class="form-control" required />
                            <label class="form-label" for="familyNumber">Family Number</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-outline">
                            <input type="text" id="zipCode" name="zip_code" class="form-control" required />
                            <label class="form-label" for="zipCode">Zip Code</label>
                        </div>
                    </div>
                </div>

                <!-- Aadhaar Image Upload -->
                <div class="file-upload-section mb-3">
                    <label for="aadhaarImage">Upload Aadhaar Image</label>
                    <input type="file" id="aadhaarImage" name="aadhaar_image" class="form-control" onchange="showPreview(this, '#aadhaarPreview')" required />
                    <img id="aadhaarPreview" src="#" alt="Aadhaar Preview" style="display: none;">
                </div>

                <!-- PAN Image Upload -->
                <div class="file-upload-section mb-3">
                    <label for="panImage">Upload PAN Image</label>
                    <input type="file" id="panImage" name="pan_image" class="form-control" onchange="showPreview(this, '#panPreview')" required />
                    <img id="panPreview" src="#" alt="PAN Preview" style="display: none;">
                </div>

                <!-- CV Upload -->
                <div class="file-upload-section mb-3">
                    <label for="cv">Upload CV (PDF)</label>
                    <input type="file" id="cv" name="cv" class="form-control" required />
                </div>

                <!-- User Photo Upload -->
                <div class="file-upload-section mb-3">
                    <label for="userPhoto">Upload User Photo</label>
                    <input type="file" id="userPhoto" name="user_photo" class="form-control" onchange="showPreview(this, '#userPhotoPreview')" required />
                    <img id="userPhotoPreview" src="#" alt="User Photo Preview" style="display: none;">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <script>
        function showPreview(input, previewId) {
            var file = input.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector(previewId).src = e.target.result;
                    document.querySelector(previewId).style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>

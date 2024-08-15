<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>
    <link rel="stylesheet" href="login_reg.css">
    <style>
        /* Style for the login container */
        * {
            font-family: 'Gilroy', sans-serif;
        }
        .container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }
        .login {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .login h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .login input[type="email"],
        .login input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .login input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: #fff;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .login input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .login a {
            text-decoration: none;
            color: #007bff;
        }
        .login a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
        <div class="login">
            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<p class="error">' . htmlspecialchars($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            }
            ?>
            <form id="login" method="post" action="../backend/login">
                <h2>Login Page</h2>
               
                <label><b>Email</b></label>
                <input type="email" name="email" id="email" placeholder="email" required>
                <br><br>
                <label><b>Password</b></label>
                <input type="password" name="password" id="Pass" placeholder="Password" required>
                <br><br>
                <input type="submit" name="submit" id="log" value="Log In Here">
                <br><br>
                <br><br>
            </form>
        </div>
    </div>
</body>
</html>

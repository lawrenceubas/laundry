<?php include('database.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Resetting default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Container for the form */
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Heading */
        .container h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }

        /* Form group styling */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            color: #333;
            background-color: #f9f9f9;
        }

        .form-group input:focus {
            border-color: #1abc9c;
            outline: none;
        }

        /* Submit button styling */
        button {
            width: 100%;
            padding: 15px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #16a085;
        }

        /* Error message styling */
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        /* Success message styling */
        .success {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }

        /* Register link styling */
        .register-box p {
            margin-top: 20px;
            font-size: 14px;
        }

        .register-box a {
            color: #1abc9c;
            text-decoration: none;
        }

        .register-box a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Register</h1>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // Check if username already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);

            if ($stmt->fetchColumn() > 0) {
                echo "<p class='error'>Username already exists. Please choose another.</p>";
            } else {
                // Insert the new user into the database
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $password]);
                echo "<p class='success'>Registration successful. <a href='login.php'>Login here</a>.</p>";
            }
        }
        ?>

        <!-- Registration Form -->
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Register</button>
        </form>

        <!-- Link to Login page -->
        <div class="register-box">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>


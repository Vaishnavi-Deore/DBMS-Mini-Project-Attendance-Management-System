<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $rollno = trim($_POST['rollno']);
    $class = trim($_POST['class']);
    $password = trim($_POST['password']);

    if ($fullname && $rollno && $class && $password) {
        $username = $rollno; // Use roll number as username
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
        $stmt->bind_param("ss", $username, $hashedPassword);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            // Insert into students table
            $stmt2 = $conn->prepare("INSERT INTO students (user_id, full_name, roll_number, class) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("isss", $user_id, $fullname, $rollno, $class);

            if ($stmt2->execute()) {
                $message = "
                    <div class='success-msg'>
                        ✅ Student added successfully!<br><br>
                        <strong>Login Details:</strong><br>
                        <strong>Username (Roll No):</strong> <code>$username</code><br>
                        <strong>Password:</strong> <code>$password</code>
                    </div>
                ";
            } else {
                $message = "<div class='error-msg'>❌ Failed to add student details.</div>";
            }

            $stmt2->close();
        } else {
            $message = "<div class='error-msg'>❌ Username already exists or failed to create user.</div>";
        }

        $stmt->close();
        $conn->close();
    } else {
        $message = "<div class='error-msg'>❌ All fields are required.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f6fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 50px;
            background-image: url(https://cdn.wallpapersafari.com/81/60/W1M7KR.jpg);
        }

        .card {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .success-msg, .error-msg {
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
        }

        .success-msg {
            background-color: #dff0d8;
            color: #3c763d;
        }

        .error-msg {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Add New Student</h2>
        <?php echo $message; ?>
        <form method="POST" action="add_student.php">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>
            <div class="form-group">
                <input type="text" name="rollno" placeholder="Roll Number" required>
            </div>
            <div class="form-group">
                <input type="text" name="class" placeholder="Class" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Set Password" required>
            </div>
            <button type="submit" class="btn">Add Student</button>
        </form>
    </div>
</body>
</html>

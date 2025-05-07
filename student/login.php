<?php
session_start();
require 'db_config.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($role === 'teacher') {
                header("Location: teacher_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "User not found or role mismatch.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Student Attendance System</title>
    <style>
        body {
            
            background: #1a1a1a;
            background-image: url(https://cdn.wallpapersafari.com/81/60/W1M7KR.jpg);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        
        .heading {
  color: white;
  font-size: 32px;
  font-weight: bold;
  margin-bottom: 500px;
  text-shadow: 1px 1px 5px rgba(0,0,0,0.5);
  text-align: center;
}

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 350px;
            position: relative;
            
        }

        .tabs {
            display: flex;
            margin-bottom: 2rem;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .tab.active {
            background:rgb(42, 100, 181);
            color: white;
        }

        .form-container {
            position: relative;
        }

        .form {
            transition: 0.3s;
            opacity: 0;
            position: absolute;
            width: 100%;
        }

        .form.active {
            opacity: 1;
            position: relative;
        }

        input {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 1rem;
            background:rgb(42, 85, 154);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background:rgb(55, 109, 174);
        }
    </style>
</head>
<body>

    <div class="login-container">
    
        <div class="tabs">
            <div class="tab active" onclick="switchTab(event, 'student')">Student</div>
            <div class="tab" onclick="switchTab(event, 'teacher')">Teacher</div>
        </div>

        <div class="form-container">
            <form class="form active" id="studentForm" action="login.php" method="post">
                <input type="hidden" name="role" value="student">
                <input type="text" name="username" placeholder="Student ID" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <form class="form" id="teacherForm" action="login.php" method="post">
                <input type="hidden" name="role" value="teacher">
                <input type="text" name="username" placeholder="Staff Code" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <script>
        function switchTab(event, role) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.form').forEach(form => form.classList.remove('active'));

            event.target.classList.add('active');
            document.getElementById(role + 'Form').classList.add('active');
        }
    </script>

    <?php if ($error): ?>
        <script>
            alert("<?php echo $error; ?>");
        </script>
    <?php endif; ?>
</body>
</html>

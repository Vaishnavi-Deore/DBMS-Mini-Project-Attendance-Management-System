<?php
require 'db_config.php';

$error = '';
$success = '';
$student = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $original_roll = $_POST['original_roll'];
        $user_id = $_POST['user_id'];
        $full_name = $_POST['full_name'];
        $roll_number = $_POST['roll_number'];
        $class = $_POST['class'];
        
        $sql = "UPDATE students SET 
                user_id = '$user_id',
                full_name = '$full_name',
                roll_number = '$roll_number',
                class = '$class'
                WHERE roll_number = '$original_roll'";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Student record updated successfully!";
            $student = null;
        } else {
            $error = "Error updating record: " . $conn->error;
        }
    } else {
        $roll = $_POST['roll'];
        $sql = "SELECT * FROM students WHERE roll_number = '$roll'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
        } else {
            $error = "Student with Roll Number $roll not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Student</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            min-height: 100vh;
        }

        .container {
            max-width: 500px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2ecc71;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #2ecc71;
            outline: none;
        }

        button {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover {
            background: #27ae60;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Update Student</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (!$student && !$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Enter Roll Number to Update:</label>
                    <input type="text" name="roll" required>
                </div>
                <button type="submit">Search Student</button>
            </form>
        <?php elseif ($student): ?>
            <form method="POST">
                <input type="hidden" name="original_roll" value="<?= $student['roll_number'] ?>">
                
                <div class="form-group">
                    <label>User ID:</label>
                    <input type="text" name="user_id" value="<?= $student['user_id'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" name="full_name" value="<?= $student['full_name'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Roll Number:</label>
                    <input type="text" name="roll_number" value="<?= $student['roll_number'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Class:</label>
                    <input type="text" name="class" value="<?= $student['class'] ?>" required>
                </div>
                
                <button type="submit" name="update">Update Student</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
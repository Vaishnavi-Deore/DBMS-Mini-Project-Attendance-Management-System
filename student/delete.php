<?php
require 'db_config.php';

$error = '';
$success = '';
$student = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_delete'])) {
        $roll = $_POST['roll'];
        $sql = "DELETE FROM students WHERE roll_number = '$roll'";
        if ($conn->query($sql) === TRUE) {
            $success = "Student with Roll Number $roll deleted successfully!";
        } else {
            $error = "Error deleting record: " . $conn->error;
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
    <title>Delete Student</title>
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
            color: #e74c3c;
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
            border-color: #e74c3c;
            outline: none;
        }

        button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        button:hover {
            background: #c0392b;
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

        .student-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ Delete Student</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <?php if (!$student && !$success): ?>
            <form method="POST">
                <div class="form-group">
                    <label>Enter Roll Number to Delete:</label>
                    <input type="text" name="roll" required>
                </div>
                <button type="submit">Search Student</button>
            </form>
        <?php elseif ($student): ?>
            <div class="student-details">
                <h3>Student Found:</h3>
                <p><strong>Name:</strong> <?= $student['full_name'] ?></p>
                <p><strong>Roll No:</strong> <?= $student['roll_number'] ?></p>
                <p><strong>Class:</strong> <?= $student['class'] ?></p>
            </div>
            
            <form method="POST">
                <input type="hidden" name="roll" value="<?= $student['roll_number'] ?>">
                <p style="color: #e74c3c; font-weight: 500;">Are you sure you want to delete this student?</p>
                <button type="submit" name="confirm_delete">Confirm Delete</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
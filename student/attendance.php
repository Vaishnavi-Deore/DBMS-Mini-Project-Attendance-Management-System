<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch students
$students = [];
$result = $conn->query("SELECT student_id, full_name, roll_number FROM students ORDER BY roll_number ASC");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Get the teacher's ID from the session (assuming it's stored in session after login)
$teacher_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_date = $_POST['attendance_date'];
    $attendance_time = $_POST['attendance_time'];
    $present_students = isset($_POST['present']) ? $_POST['present'] : [];

    foreach ($students as $student) {
        $status = in_array($student['student_id'], $present_students) ? 'present' : 'absent';

        // Insert attendance record
        $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, time, status, marked_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $student['student_id'], $attendance_date, $attendance_time, $status, $teacher_id);
        $stmt->execute();
        $stmt->close();
    }

    $message = "<div class='success-msg'>Attendance recorded successfully for $attendance_date at $attendance_time.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --hover-color: #2980b9;
            --bg-color: #f5f6fa;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
        }

        body {
            background: var(--bg-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 50px;
            background-image: url(https://cdn.wallpapersafari.com/81/60/W1M7KR.jpg);
        }

        .card {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="date"],
        input[type="time"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--secondary-color);
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: var(--hover-color);
        }

        .success-msg {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .error-msg {
            background-color: #f2dede;
            color: #a94442;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .card {
                padding: 20px;
            }

            th, td {
                padding: 8px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Mark Attendance</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="attendance_date">Date:</label>
                <input type="date" id="attendance_date" name="attendance_date" required>
            </div>
            <div class="form-group">
                <label for="attendance_time">Time:</label>
                <input type="time" id="attendance_time" name="attendance_time" required>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Roll Number</th>
                        <th>Full Name</th>
                        <th>Present</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                        <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                        <td>
                            <input type="checkbox" name="present[]" value="<?php echo $student['student_id']; ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button type="submit" class="btn">Submit Attendance</button>
        </form>
    </div>
</body>
</html>

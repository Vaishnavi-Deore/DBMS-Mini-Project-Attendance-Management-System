<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.html");
    exit();
}
require 'db_config.php';

// Get student details
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

// Fetch attendance records
$student_id = $student['student_id'];
$attendance_stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY date DESC, time DESC");
$attendance_stmt->bind_param("i", $student_id);
$attendance_stmt->execute();
$attendance_result = $attendance_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background: #f0f4f8;
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(135deg,rgb(41, 75, 162),rgb(90, 131, 181));
            color: white;
            padding: 30px 20px;
            height: 100%;
        }

        .sidebar h2 {
            margin-bottom: 40px;
            text-align: center;
        }

        .sidebar a {
            display: block;
            padding: 12px;
            margin: 15px 0;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            padding: 40px;
            background: #f0f4f8;
        }

        .welcome-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            animation: fadeIn 0.6s ease;
        }

        .welcome-card h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .info {
            margin: 10px 0;
            font-size: 16px;
        }

        .attendance-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.8s ease;
        }

        .attendance-section h2 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #2575fc;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .logout-btn {
            margin-top: 30px;
            padding: 12px 20px;
            background:rgb(53, 133, 212);
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s ease;
        }

        .logout-btn:hover {
            background:rgb(43, 130, 192);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media(max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            .sidebar {
                height: auto;
                position: static;
            }
            .main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>🎓 My Dashboard</h2>
        <a href="student_dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="welcome-card">
            <h1>Welcome, <?php echo $student['full_name']; ?> 👋</h1>
            <p class="info">Roll Number: <strong><?php echo $student['roll_number']; ?></strong></p>
            <p class="info">Class: <strong><?php echo $student['class']; ?></strong></p>
        </div>

        <div class="attendance-section">
            <h2>📅 Attendance Record</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $attendance_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d M Y", strtotime($row['date'])); ?></td>
                        <td><?php echo date("h:i A", strtotime($row['time'])); ?></td>
                        <td style="color:<?php echo ($row['status'] === 'present') ? 'green' : 'red'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>

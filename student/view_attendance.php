<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch attendance records
$attendance_records = [];
$attendance_query = "
    SELECT a.attendance_id, a.student_id, a.date, a.status, a.time, s.full_name, s.roll_number 
    FROM attendance a 
    JOIN students s ON a.student_id = s.student_id
    ORDER BY a.date DESC, a.time DESC";
$result = $conn->query($attendance_query);
while ($row = $result->fetch_assoc()) {
    $attendance_records[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --hover-color: #2980b9;
            --bg-color: #f5f6fa;
            --present-color: #2ecc71;
            --absent-color: #e74c3c;
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
            max-width: 900px;
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

        .present {
            color: var(--present-color);
            font-weight: bold;
        }

        .absent {
            color: var(--absent-color);
            font-weight: bold;
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
            text-decoration: none;
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
        <h2>View Attendance</h2>

        <table>
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Full Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($attendance_records) > 0): ?>
                    <?php foreach ($attendance_records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['roll_number']); ?></td>
                            <td><?php echo htmlspecialchars($record['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['date']); ?></td>
                            <td><?php echo htmlspecialchars($record['time']); ?></td>
                            <td class="<?php echo $record['status'] == 'present' ? 'present' : 'absent'; ?>">
                                <?php echo ucfirst($record['status']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="teacher_dashboard.php" class="btn">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch data
$total_students_result = $conn->query("SELECT COUNT(*) as total_students FROM students");
$total_students = $total_students_result->fetch_assoc()['total_students'];

$today_date = date('Y-m-d');
$attendance_today_result = $conn->query("SELECT COUNT(*) as attendance_today FROM attendance WHERE date = '$today_date' AND status = 'present'");
$attendance_today = $attendance_today_result->fetch_assoc()['attendance_today'];

$pending_attendance_result = $conn->query("SELECT COUNT(*) as pending FROM students WHERE student_id NOT IN (SELECT student_id FROM attendance WHERE date = '$today_date')");
$pending_attendance = $pending_attendance_result->fetch_assoc()['pending'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color:rgb(42, 125, 208);
            --secondary-color:rgb(74, 121, 192);
            --accent-color: #FF7675;
            --background-color: #F8F9FE;
            --card-bg: #FFFFFF;
            --text-color:rgb(8, 10, 10);
            --success-color: #00B894;
            --warning-color: #FDCB6E;
            --danger-color:rgb(55, 121, 206);
            --shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--background-color);
            color: var(--text-color);
            overflow-x: hidden;
            /* background-image: url(https://cdn.wallpapersafari.com/81/60/W1M7KR.jpg); */
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%,rgb(46, 88, 172) 100%);
            color: white;
            padding: 2rem;
            position: fixed;
            height: 100%;
            width: 280px;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            text-align: center;
            padding: 2rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 2rem;
        }

        .sidebar-header h2 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .menu-item {
            margin: 1rem 0;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
        }

        .menu-item i {
            margin-right: 1rem;
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }

        .menu-item.active {
            background: rgba(255,255,255,0.1);
        }

        .main-content {
            padding: 2rem 3rem;
            margin-left: 280px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header .right {
            display: flex;
            align-items: right;
            gap: 1rem;
            position: absolute;
            right: 10px;
            top: 20px;
        }

        .logout-btn {
            background: var(--danger-color);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            position: absolute;
            right: 10px;
            top: 50px;
        
        }

        .theme-toggle {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .stat-box {
            background: linear-gradient(135deg, var(--primary-color),rgb(77, 128, 204));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .stat-box h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .stat-box .value {
            font-size: 2.5rem;
            font-weight: bold;
        }

        body.dark-theme {
            --background-color: #1A1A2E;
            --card-bg: #16213E;
            --text-color: #FFFFFF;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .sidebar {
                position: static;
                width: 100%;
            }
            .stats-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>Teacher Portal</h2>
            <p>Welcome, <?php echo $_SESSION['username']; ?></p>
        </div>
        <ul class="sidebar-menu">
            <li><a class="menu-item active" href="dashboard.php"><i class="fas fa-home"></i>Dashboard</a></li>
            <li><a class="menu-item" href="add_student.php"><i class="fas fa-user-plus"></i>Add Student</a></li>
            <li><a class="menu-item" href="update.php"><i class="fas fa-edit"></i>Update Student Data</a></li>
            <li><a class="menu-item" href="delete.php"><i class="fas fa-trash-alt"></i>Delete Student</a></li>
            <li><a class="menu-item" href="attendance.php"><i class="fas fa-clipboard-check"></i>Take Attendance</a></li>
            <li><a class="menu-item" href="view_attendance.php"><i class="fas fa-chart-bar"></i>Reports</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            <div class="right">
                <form action="logout.php" method="post" style="margin: 0;">
                    <button class="logout-btn" type="submit"><i class="fas fa-sign-out-alt"></i></button>
                </form>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
            </div>
        </div>

        <div class="stats-container">
            <div class="stat-box">
                <h3>Total Students</h3>
                <div class="value count"><?php echo $total_students; ?></div>
            </div>
            <div class="stat-box">
                <h3>Today's Attendance</h3>
                <div class="value count"><?php echo $attendance_today; ?></div>
            </div>
            <div class="stat-box">
                <h3>Pending Attendance</h3>
                <div class="value count"><?php echo $pending_attendance; ?></div>
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.count');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.innerText;
                const increment = Math.ceil(target / 100);
                let count = 0;
                const step = () => {
                    count += increment;
                    if (count < target) {
                        counter.innerText = count;
                        requestAnimationFrame(step);
                    } else {
                        counter.innerText = target;
                    }
                };
                step();
            };
            updateCount();
        });
    });

    function toggleTheme() {
        document.body.classList.toggle('dark-theme');
        const icon = document.querySelector('.theme-toggle i');
        icon.classList.toggle('fa-moon');
        icon.classList.toggle('fa-sun');
    }
</script>
</body>
</html>


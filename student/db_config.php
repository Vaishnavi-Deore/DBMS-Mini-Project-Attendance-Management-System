<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_attendance_system";
$port = "4306";

$conn = new mysqli("localhost", "root", "", "student_attendance_system", 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<?php
// Example: Creating a teacher account with hashed password
require 'db_config.php';

$staff_code = "sai"; // Unique staff code
$password = "sai@123"; // Raw password
$full_name = "John Doe";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Create user in users table
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'teacher')");
$stmt->bind_param("ss", $staff_code, $hashed_password);
$stmt->execute();
$user_id = $stmt->insert_id;

// Create teacher record in teachers table
$stmt = $conn->prepare("INSERT INTO teachers (user_id, full_name, staff_code) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $full_name, $staff_code);
$stmt->execute();
?>
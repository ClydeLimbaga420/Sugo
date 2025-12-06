<?php
require "/xampp2/htdocs/Sugo/config/db.php";

$name = $_POST['name'];
$email = $_POST['email'];
$pass = $_POST['password'];
$role = $_POST['role'];

$hashed = password_hash($pass, PASSWORD_DEFAULT);
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email already registered!'); window.location='register.php';</script>";
    exit;
}

$sql = "INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $hashed, $role);

if ($stmt->execute()) {
    echo "<script>alert('Registration successful! Please login.'); window.location='login.php';</script>";  
} else {
    echo "<script>alert('Error during registration. '); window.location='register.php';</script>";
}
?>
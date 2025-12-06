<?php
session_start();
require "/xampp2/htdocs/Sugo/config/db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: /Sugo/admin/dashboard.php");
        } elseif ($row['role'] === 'worker') {
            header("Location: /Sugo/worker/home.php");
        } else {
            header("Location: /Sugo/user/home.php");
        }
        exit;
    }
}
echo "<script>alert('Invalid login.'); window.location='login.php';</script>";
?>
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /xampp2/htdocs/Sugo/auth/login.php");
    exit;
}
?>
<h1>Admin Dashboard</h1>
<p>Welcome, Admin!</p>
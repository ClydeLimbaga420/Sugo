<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /xampp2/htdocs/Sugo/auth/login.php");
}
?>
<h1>User Home</h1>
<p>Welcome User!</p>
<a href="report_create.php">Create Report</a>
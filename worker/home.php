<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: /xampp2/htdocs/Sugo/auth/login.php");
}
?>
<h1>Worker Home</h1>
<p>Welcome Worker!</p>
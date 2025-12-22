<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$report_id = $_POST['id'];
$worker_id = $_POST['user_id'];

$sql = "UPDATE reports SET status = 'Completed' WHERE id = ? AND assigned_to = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $report_id, $worker_id);

if ($stmt->execute()) {
    echo "<script>alert('Report marked as completed!'); window.location='home.php';</script>";

} else {
    echo "Error: ". $conn->error;
}
?>
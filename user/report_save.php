<?php
session_start();
require "/xampp2/htdocs/Sugo/config/db.php";

$user_id = $_SESSION['user_id'];
$title = $_POST['title'];
$description = $_POST['description'];
$lat = $_POST['latitude'];
$lng = $_POST['longitude'];

$photo_path = NULL;

if (!empty($_FILES['photo']['name'])) {
    $filename = time() . "_" . $_FILES['photo']['name'];
    $target = "/xampp2/htdocs/Sugo/uploads/reports/" . $filename;
}
$sql = "INSERT INTO reports (user_id, title, description, photo_path, latitude, longitude)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssdd", $user_id, $title, $description, $photo_path, $lat, $lng);

if ($stmt->execute()) {
    echo "<script>alert('Report Submitted'); window.location='home.php';</script>";
} else {
    echo "<script>alert('Error saving report'); window.location='report_create.php';</script>";
}
?>
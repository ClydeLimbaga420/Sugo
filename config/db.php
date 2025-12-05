<?php
$host = "localhost";
$user = "root";
$pass = "clydelimbaga123";
$db = "sugodb";
$port = "3307";

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
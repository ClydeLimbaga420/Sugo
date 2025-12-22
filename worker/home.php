<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$worker_id = $_SESSION['user_id'];

$sql = "SELECT * FROM reports WHERE assigned_to = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt-> get_result();
?>

<h2>My Assigned Reports</h2>

<table border="1" cellpadding = "10">
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['title'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><a href="view_report.php?id=<?= $row['id'] ?>">View</a></td>
        </tr>
        <?php endwhile; ?>
</table>
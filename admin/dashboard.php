<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php"); 
    exit;
}

require "../config/db.php"; 

$sql = "SELECT r.*, u.name as user_name FROM reports r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Admin Dashboard</h1>
<p>Welcome, Admin!</p>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Title</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <a href="report_view.php?id=<?= $row['id'] ?>">View</a> |
                <a href="assign_worker.php?id=<?= $row['id'] ?>">Assign Worker</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>

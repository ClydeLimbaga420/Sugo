<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$sql = "SELECT r.*, u.name AS user_name FROM reports r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }
        
        h1 {
            margin-top: 0;
        }

        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }   
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f0f2f5;
        }

        tr:hover {
            background: #fafafa;
        }

        .status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .assigned {
            background: #d4edda;
            color: #155724;
        }

        .resolved {
            background: #48d7da;
            color: #721c24;
        }

        .action a {
            color: #2196f3;
            text-decoration: none;
            font-weight: bold;
            margin-right: 8px;
        }

        .action a:hover {
            text-decoration: underline;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #777;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logout-btn {
            background: #f44336;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #d32f2f;
        }
    </style>

</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Welcome Admin</p>
            </div>

            <a href="../auth/login.php" class="logout-btn">Logout</a>
        </div>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php $statusClass = strtolower($row['status']);
                ?>

                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    
                    <td>
                        <span class="status <?= $statusClass ?>">
                            <?= ucfirst(htmlspecialchars($row['status'])) ?>
                        </span>
                    </td>

                    <td>
                        <span class="action">
                            <a href="report_view.php?id=<?= $row['id'] ?>">View</a>
                            <a href="assign_worker.php?id=<?= $row['id'] ?>">Assign</a>
                        </span>
                    </td>
                </tr>

                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="empty">
                        No reports available.
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
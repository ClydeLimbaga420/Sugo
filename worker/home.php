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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Assigned Reports</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #00bcd4, #2196f3);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2196f3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #2196f3;
            color: #fff;
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f5faff;
        }

        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 18px;
            font-weight: 600;
            display: inline-block;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .assigned {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .resolved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .btn-view {
            background: #4caf50;
            color: white;
            padding: 7px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-view:hover {
            background: #43a047;
        }

        .empty {
            text-align: center;
            padding: 20px;
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

        

        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                padding: 10px;
                position: relative;
            }

            td::before {
                content: attr(data-label);
                font-weight: bold;
                display: block;
                margin-bottom: 4px;
                color: #555;
            }

            
        }
    </style>
</head>
<body>

    <div class="container">

        <div class="header">
            <h2>My Assigned Reports</h2>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="ID"><?= $row['id'] ?></td>
                            <td data-label="Title"><?= htmlspecialchars($row['title']) ?></td>
                            <td data-label="Status">
                                <span class="status <?= strtolower($row['status']) ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td data-label="Action">
                                <a class="btn-view" href="view_report.php?id=<?= $row['id'] ?>">View</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">No assigned reports yet.</div>
        <?php endif; ?>

    </div>
</body>
</html>

    

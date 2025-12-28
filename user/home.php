<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS unread FROM notifications WHERE user_id = ? AND is_read = 0"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();
$unread = $stmt->get_result()->fetch_assoc()['unread'];
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    <style>
        body {
            font-family: 'Segoe UI', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00bcd4, #2196f3);
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .dashboard {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .dashboard h1 {
            color: #219643;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .dashboard p {
            color: #555;
            margin-bottom: 25px;
            font-size: 15px;
        }
        .action-btn {
            display: block;
            width: 100%;
            padding: 14px;
            margin: 12px 0;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            color: white;
            transition: 0.3s;
        }
        .create {
            background: #4caf50;
        }
        .create:hover {
            background: #43a047;
        }
        .reports {
            background: #2196f3;
        }
        .reports:hover {
            background: #1976d2;
        }
        .logout {
            background: #f44336;
        }
        .logout:hover {
            background: #d32f2f;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
        }
        .btn:hover {
            background: #1976d2;
        }
        .logout {
            background: #f44336;
        }
        .logout:hover {
            background: #d32f2f;
        }

        .notify {
            background: #ff9800;
            position: relative;
        }

        .notify:hover {
            background: #fb8c00;
        }

        .notify .badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #f44336;
            color: white;
            font-size: 12px;
            padding: 4px 7px;
            border-radius: 50%;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Hello User</h1>
        <p>Welcome to SUGO</p>
        <a class="action-btn create" href="report_create.php">Create Report</a>
        <a class="action-btn reports" href="my_reports.php">My Reports</a>
        <a class="action-btn logout" href="../auth/login.php">Logout</a>
        <a class="action-btn notify" href="notifications.php"> 🔔 Notifications
        <?php if ($unread > 0): ?>    
            <span class="badge">!</span>
        <?php endif; ?>
        </a>
    </div>
</body>
</html>
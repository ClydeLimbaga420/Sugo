<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$user_id = $_SESSION['user_id'];


$sql = "SELECT n.*, r.title FROM notifications n JOIN reports r ON n.report_id = r.id WHERE n.user_id = ? ORDER BY n.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            padding: 20px;
            background: #2196f3;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            margin: 0;
        }

        .notif-list {
            padding: 10px 0;
        }

        .notif {
            display: flex;
            gap: 12px;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            transition: background .2s;
        }

        .notif:hover {
            background: #f9fbfd;
        }

        .notif.unread {
            background: #e3f2fd;
        }

        .icon {
            font-size: 22px;
            line-height: 1;
            color: #2196f3;
        }

        .notif-content {
            flex: 1;
        }

        .notif-content p {
            margin: 0 0 6px;
            font-size: 14px;
        }

        .notif-content small {
            color: #777;
            font-size: 12px;
        }

        .notif-actions {
            display: flex;
            align-items: center;
        }

        .btn {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            background: #2196f3;
            color: white;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        .back {
            display: inline-block;
            margin: 15px;
            text-decoration: none;
            color: #2196f3;
        }

        @media (max-width: 600px) {
            .notif {
                flex-direction: column;
            }

            .notif-actions {
                margin-top: 8px;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Notifications</h2>
            <span><?= $notifications->num_rows ?> Total</span>
        </div>

        <div class="notif-list">
            <?php if ($notifications->num_rows === 0): ?>
                <div class="empty">
                    <p>No notifications yet</p>
                </div>
            <?php endif; ?>

            <?php while ($n = $notifications->fetch_assoc()): ?>
                <div class="notif <?= !$n['is_read'] ? 'unread' : '' ?>">

                <div class="icon">
                    <?= !$n['is_read'] ? '🔔' : '📩' ?>
                </div>

                <div class="notif-content">
                    <p><?= htmlspecialchars($n['message']) ?></p>
                    <small>
                        Report: <?= htmlspecialchars($n['title']) ?> ·
                        <?= date("M d, Y h:i A", strtotime($n['created_at']) ) ?>
                    </small>
                </div>

                <div class="notif-actions">
                    <a class="btn" href="view_report_user.php?id=<?= $n['report_id'] ?>&read=<?= $n['id'] ?>"> View </a>
                </div>
            <?php endwhile; ?>
        </div>

        <a class="back" href="home.php">Back To Dashboard</a>

    </div>
</body>
</html>
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
    <title>SUGO | Notifications</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --unread-bg: #e3f2fd;
            --bg-gradient: linear-gradient(-45deg, #00bcd4, #2196f3, #3f51b5, #00bcd4);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            overflow-x:hidden;
            box-sizing: border-box;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            backdrop-filter: 0 20px 40px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        header {
            padding: 25px 30px;
            background: white;
            border-bottom: 2px solid #f0f2f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
            font-size: 22px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .notif-count {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .notif-list {
            max-height: 600px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .notif-item {
            display: flex;
            gap: 15px;
            padding: 20px 30px;
            border-bottom: 1px solid #f0f2f5;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
            position: relative;
        }

        .notif-item:hover {
            background: #f8fbff;
            transform: scale(1.01);
            z-index: 2;
        }

        .notif-item.unread {
            background: var(--unread-bg);
        }

        .notif-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary-color);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            font-size: 18px;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .unread .icon-box {
            background: var(--primary-color);
            color: white;
        }

        .content {
            flex: 1;
        }

        .message {
            margin: 0 0 5px;
            font-size: 15px;
            color: #333;
            line-height: 1.4;
        }

        .unread .message {
            font-weight: 600;
        }

        .meta {
            font-size: 12px;
            color: #888;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .report-tag {
            background: #eee;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #555;
        }

        .actions {
            display: flex;
            align-items: center;
        }

        .btn-view {
            background: white;
            border: 1px solid #ddd;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            transition: 0.2s;
        }

        .btn-view:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #aaa;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            opacity: 0.3s;
        }

        footer {
            padding: 15px 30px;
            background: #fafafa;
            text-align: center;
        }

        .back-link {
            text-decoration: none;
            color: #777;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .back-link:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    
    <div class="container">
        <header>
            <h2><i class="fas fa-bell"></i>  Notifications</h2>
            <span class="notif-count"><?= $notifications->num_rows ?>  Total</span>
        </header>

        <div class="notif-list">
            <?php if ($notifications->num_rows === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No Messages Yet.</p>
                </div>
            <?php endif; ?>

            <?php while ($n = $notifications->fetch_assoc()): ?>
                <a href="view_report_user.php?id=<?= $n['report_id'] ?>&read=<?= $n['id'] ?>"
                    class="notif-item <?= !$n['is_read'] ? 'unread' : '' ?>">

                    <div class="icon-box">
                        <i class="fas <?= !$n['is_read'] ? 'fa-envelope-open-text' : 'fa-check-double' ?>"></i>
                    </div>

                    <div class="content">
                        <p class="message"><?= htmlspecialchars($n['message']) ?></p>
                        <div class="meta">
                            <span class="report-tag"><i class="fas fa-tools"></i> <?= htmlspecialchars($n['title']) ?></span>
                            <span>•</span>
                            <span><?= date("M d, g:i A", strtotime($n['created_at'])) ?></span>
                        </div>
                    </div>

                    <div class="actions">
                        <span class="btn-view">Details</span>
                    </div>
            </a>
            <?php endwhile; ?>
        </div>

        <footer>
                <a class="back-link" href="home.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </footer>
    </div>

</body>
</html>

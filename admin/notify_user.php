<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

if (!isset($_GET['id'])) {
    die("No report ID provided!");
}

$report_id = (int)$_GET['id'];

$sql = "SELECT r.id, r.title, u.id AS user_id, u.name, u.email FROM reports r JOIN users u ON r.user_id = u.id WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$report) {
    die("Report not found!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $insert = "INSERT INTO notifications (user_id, report_id, message) VALUES (?,?,?)";
        $stmt = $conn->prepare($insert);
        $stmt->bind_param("iis", $report['user_id'], $report_id, $message);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php?notified=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notify User</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #2196f3, #673ab7);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: white;
            width: 100%;
            max-width: 520px;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        .info {
            background: #f4f6f8;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 4px;
            display: block;
        }

        textarea {
            width: 100%;
            height: 120px;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: none;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-send {
            background: #4caf50;
            color: white;
        }

        .btn-back {
            background: #9e9e9e;
            color: white;
            text-decoration: none;
            text-align: center;
            line-height: 38px;
        }

        .btn-send:hover {
            background: #43a047;
        }

        .btn-back:hover {
            background: #757575;
        }

    </style>
</head>
<body>
    <div class="card">
        <h2>Notify User</h2>

        <div class="info">
            <span class="label">Report Title</span>
            <?= htmlspecialchars($report['title']) ?>
            <br><br>
            <span class="label">User</span>
            <?= htmlspecialchars($report['name']) ?> (<?= htmlspecialchars($report['email']) ?>)
        </div>

        <form method="POST">
            <span class="label">Message</span>
            <textarea name="message" placeholder="Write a message to the user..." required></textarea>
            <div class="actions">
                <button type="submit" class="btn btn-send">Send Notification</button>
                <a href="dashboard.php" class="btn btn-back">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

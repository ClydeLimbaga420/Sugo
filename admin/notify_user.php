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
    <title>Notification | SUGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --admin-primary: #673ab7;
            --success-color: #10b981;
            --bg-gradient: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: rgba(255, 255, 255, 0.98);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            background: #ede7f6;
            color: var(--admin-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 15px;
        }

        h2 {
            margin: 0;
            font-size: 22px;
            color: #1a1a2e;
        }

        .info-preview {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .label-tag {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }

        .user-meta {
            display: flex;;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }

        textarea {
            width: 100%;
            height: 140px;
            padding: 15px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            font-family: inherit;
            font-size: 14px;
            resize: none;
            transition: 0.3s;
            box-sizing: border-box;
            background: #fff;
        }

        textarea:focus {
            outline: none;
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 4px rgba(103, 58, 183, 0.1);
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-send {
            background: var(--success-color);
            color: white;
        }

        .btn-back {
            background: transparent;
            color: #94a3b8;
        }

        .btn-back:hover {
            color: #64748b;
            background: #f1f5f9;
        }

        .shortcut-chips {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .chip {
            font-size: 11px;
            padding: 5px 10px;
            background: #f1f5f9;
            border-radius: 20px;
            cursor: pointer;
            color: #475569;
            border: 1px solid #e2e8f0;
            transition: 0.2s;
        }

        .chip:hover {
            background: var(--admin-primary);
            color: white;
            border-color: var(--admin-primary);
        }
    </style>
</head>
<body>
    
    <div class="card">
        <header>
            <div class="icon-circle">
                <i class="fas fa-paper-plane"></i>
            </div>
            <h2>Notify User</h2>
            <p style:="font-size: 13px; color: #64748b; margin-top: 5px;">Update the Respondent</p>
        </header>
    

        <div class="info-preview">
            <div>
                <span class="label-tag">Report</span>
                <strong style="color: #1e293b;"><?= htmlspecialchars($report['title']) ?></strong>
            </div>
            <div class="user-meta">
                <i class="fas=fa-user-circle" style="color: #cbd5e1; font-size: 20px;"></i>
                <div>
                    <span style="display: block; font-weight: 600; color: #334155; line-height: 1;"><?= htmlspecialchars($report['name']) ?></span>
                    <span style="font-size: 11px; color: #94a3b8;"><?= htmlspecialchars($report['email']) ?></span>
                </div>
            </div>
        </div>

        <form method="POST">
            <span class="label-tag" style="margin-bottom: 8px;">Message</span>
            <div class="shortcut-chips">
                <div class="chip" onclick="addText('We Received your Report!')">Received</div>
                <div class="chip" onclick="addText('We Sent A Worker to your Location!')">Dispatched</div>
                <div class="chip" onclick="addText('The Issue has been Resolved!')">Resolved</div>
            </div>
            <textarea id="message" name="message" placeholder="Write Message..." required></textarea>
            <div class="actions">
                <button type="submit" class="btn btn-send">
                    <i class="fas fa-bell"></i> Send Notification
                </button>
                <a href="dashboard.php" class="btn btn-back">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <script>
        function addText(text) {
            document.getElementById('message').value = text;
        }
    </script>
</body>
</html>
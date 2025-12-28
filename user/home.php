<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$user_id = $_SESSION['user_id'];

$user_query = $conn->prepare("SELECT name FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();
$name = $user_data['name'] ?? 'User';
$user_query->close();

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
    <title>SUGO | User Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --success-color: #4caf50;
            --warn-color: #ff9800;
            --danger-color: #f44336;
            --bg-gradient: linear-gradient(-45deg, #00bcd4, #2196f3, #3f51b5, #00bcd4 );
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
            align-items: center;
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .logo-area {
            width: 70px;
            height: 70px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 30px;
            box-shadow: 0 10px 20px rgba(33, 150, 243, 0.3);
        }

        .dashboard h1 {
            color: #333;
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }

        .dashboard p {
            color: #777;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .btn-container {
            display: grid;
            gap: 15px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            position: relative;
        }

        .action-btn i {
            font-size: 18px;
        }

        .create {
            background: var(--success-color);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .reports {
            background: var(--primary-color);
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0,3);
        }

        .notify {
            background: var(--warn-color);
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }

        .logout {
            background: #607d8b;
            margin-top: 10px;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            filter: brightness(1.1);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .action-btn:active {
            transform: translateY(0);
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--danger-color);
            color: white;
            font-size: 12px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(244, 67, 54, 0.7)
            }
            70% {
                transform: scale(1.1);
                box-shadow: 0 0 0 10px rgba(244, 67, 54, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(244, 67, 54,)
            }
        }

        #typing-greeting {
            display: inline-block;
            border-right: 3px solid var(--primary-color);
            white-space: nowrap;
            overflow: hidden;
            padding-right: 5px;
            animation: blinkCursor 0.8s step-end infinite;
        }

        @keyframes blinkCursor {
            from, to {
                border-color: transparent
            }
            50% {
                border-color: var(--primary-color)
            }
        }


    </style>
</head>
<body>
    <div class="dashboard">
        <div class="logo-area">
            <i class="fas fa-tools"></i>
        </div>

        <h1 id="typing-greeting"></h1>
        <p>Your SUGO Service Hub</p>

        <div class="btn-container">
            <a class="action-btn create" href="report_create.php">
                <i class="fas fa-plus-circle"></i> Create New Report
            </a>

            <a class="action-btn reports" href="my_reports.php">
                <i class="fas fa-clipboard-list"></i> View My Reports
            </a>

            <a class="action-btn notify" href="notifications.php">
                <i class="fas fa-bell"></i>Notifications
                <?php if ($unread > 0): ?>
                   <span class="badge"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>

            <hr style="width: 100%; border: 0; border-top: 1px solid #eee; margin:10px 0;">

            <a class="action-btn logout" href="../auth/login.php">
                <i class="fa fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>


    <script>
        const name = "<?php echo addslashes(htmlspecialchars($name)); ?>";
        const text = `Hello, ${name}!`;
        let index = 0;

        function typeEffect() {
            if (index < text.length) {
                document.getElementById("typing-greeting").innerHTML += text.charAt(index);
                index++;
                setTimeout(typeEffect, 100);
            }
        }

        window.onload = typeEffect;
    </script>
</body>
</html>
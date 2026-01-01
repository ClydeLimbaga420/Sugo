<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header ("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

if (!isset($_GET['id'])) {
    die("No report ID provided.");
}

$report_id = (int)$_GET['id'];

$report_sql = "SELECT * FROM reports WHERE id = ?";
$stmt = $conn->prepare($report_sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$report) {
    die("Report not found.");
}

$worker_sql = "SELECT id, name FROM users WHERE role = 'worker'";
$workers = $conn->query($worker_sql);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $worker_id = (int)$_POST['worker_id'];
    $check_worker = $conn->prepare(
        "SELECT id FROM users WHERE id = ? AND role ='worker'"
    );
    $check_worker->bind_param("i", $worker_id);
    $check_worker->execute();
    $worker_exists = $check_worker->get_result()->num_rows;
    $check_worker->close();

    if (!$worker_exists) {
        die("Invalid worker selected.");
    }

    $update_sql =  "UPDATE reports SET assigned_to = ?, status = 'Assigned' WHERE id = ?";
    $stmt2 = $conn->prepare($update_sql);
    $stmt2-> bind_param("ii", $worker_id, $report_id);

    if ($stmt2->execute()) {
        header("Location: dashboard.php?assigned=1");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Worker | SUGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --admin-primary: #673ab7;
            --admin-accent: #ede7f6;
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

        .container {
            max-width: 500px;
            width: 100%;
            background: #ffffff;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: var(--admin-accent);
            color: var(--admin-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 15px;
        }

        h2 {
            margin: 0;
            color: var(--text-main);
            font-size: 24px;
        }

        .report-preview {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .info-item {
            margin-bottom: 12px;
            font-size: 14px;
        }

        .info-label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a38b;
            font-weight: 600;
        }

        .info-value {
            color: var(--text-main);
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        select {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-family: inherit;
            font-size: 15px;
            color: var(--text-main);
            outline: none;
            transition: 0.3s;
            background: #fff;
            cursor: pointer;
        }

        select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 4px var(--admin-accent);
        }

        button {
            width: 100%;
            padding: 16px;
            background: var(--admin-primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        button:hover {
            background: #512da8;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(103, 58, 183, 0.2);
        }

        .cancel-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            transition: 0.2s;
        }

        .cancel-link:hover {
            color: var(--admin-primary);
        }
    </style>
</head>
<body>
    
    <div class="container">
        <header>
            <div class="icon-box">
                <i class="fas fa-user-plus"></i>
            </div>

            <h2>Assign Worker</h2>
            <p style="color: #64748b; font-size: 14px;">Assign a Worker</p>
        </header>

        <div class="report-preview">
            <div class="info-item">
                <span class="info-label">Report ID</span>
                <span class="info-value"<?= htmlspecialchars($report['title']) ?>></span>
            </div>
            <div class="info-item" style="margin-bottom: 0;">
                <span class="info-label">Current Status</span>
                <span class="info-value" style="color: #ffa000;"><?= ucfirst(htmlspecialchars($report['status'])) ?></span>

            </div>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="worker_id">Available Worker</label>
                <select name="worker_id" id="worker_id" required>
                   <option value="" disabled selected>Select a Worker</option>
                   <?php while ($row = $workers->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">
                    👤 <?= htmlspecialchars($row['name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit">
                <i class="fas fa-paper-plane"></i> Confirm Assignment
            </button>
        </form>

        <a href="dashboard.php" class="cancel-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>


    </div>

</body>
</html>
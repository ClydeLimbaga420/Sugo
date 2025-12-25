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
    <title>Assign Worker</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        .info {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solud #ccc;
            font-size: 14px;
        }

        button {
            margin-top: 15px;
            background: #2196f3;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #1976d2;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Assign Worker</h2>

        <div class="info">
            <strong>Report ID:</strong> <?= $report['id'] ?>
        </div>

        <div class="info">
            <strong>Title:</strong> <?= htmlspecialchars($report['title']) ?>
        </div>

        <div class="info">
            <strong>Description:</strong><br>
            <?= nl2br(htmlspecialchars($report['description'])) ?>
        </div>

        <div class="info">
            <strong>Status: </strong> <?= htmlspecialchars($report['status']) ?>
        </div>

        <form method="POST">
            <label>Select Worker</label>
            <select name="worker_id" required>
                <option value="" disabled selected>Choose Worker</option>
                <?php while ($row = $workers->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>">
                        <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Assign Worker</button>
        </form>
    </div>
</body>
</html>
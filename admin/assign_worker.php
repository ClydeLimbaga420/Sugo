<?php
session_start();

// Only admin allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

// Validate report ID
if (!isset($_GET['id'])) {
    die("No report ID provided.");
}

$report_id = $_GET['id'];

// SAFER: Prepare statement for report
$report_sql = "SELECT * FROM reports WHERE id = ?";
$stmt = $conn->prepare($report_sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();

// Get all workers
$worker_sql = "SELECT id, name FROM users WHERE role = 'worker'";
$workers = $conn->query($worker_sql);

// If form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $worker_id = $_POST['worker_id'];

    // SAFER: Prepared update
    $update_sql = "UPDATE reports SET assigned_to = ?, status = 'Assigned' WHERE id = ?";
    $stmt2 = $conn->prepare($update_sql);
    $stmt2->bind_param("ii", $worker_id, $report_id);

    if ($stmt2->execute()) {
        header("Location: dashboard.php?assigned=1");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<h2>Assign Worker to Report #<?= $report['id'] ?></h2>

<p><strong>Title:</strong> <?= $report['title'] ?></p>
<p><strong>Description:</strong> <?= $report['description'] ?></p>
<p><strong>Status:</strong> <?= $report['status'] ?></p>

<form method="POST">
    <label>Select Worker:</label>
    <select name="worker_id" required>
        <option value="" disabled selected>Choose Worker</option>

        <?php while ($row = $workers->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>">
                <?= $row['name'] ?>
            </option>
        <?php endwhile; ?>

    </select>
    <br><br>
    <button type="submit">Assign Worker</button>
</form>

<?php
session_start();
require "../config/db.php";

$report_id = $_GET['id'] ?? 0;

$workers = $conn->query("SELECT id, name FROM users WHERE role='worker'");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $worker_id = $_POST['worker_id'];
    $sql = "INSERT INTO report_assignments (report_id, worker_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $report_id, $worker_id);
    $stmt->execute();
    $conn->query("UPDATE reports SET status='assigned' WHERE id=$report_id");
    exit;
}
?>

<form method="POST">
    <select name="worker_id" required>
        <?php while($w = $workers->fetch_assoc()): ?>
            <option value="<?= $w['id'] ?>"><?= $w['name'] ?></option>
            <?php endwhile; ?>
    </select>
    <button type="submit">Assign Worker</button>
</form>
<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'worker') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

if(!isset($_GET['id'])) {
    die("No report ID provided.");
}

$report_id = $_GET['id'];
$worker_id = $_SESSION['user_id'];

$sql = "SELECT r.*, u.name AS user_name, u.email 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.id = ? AND r.assigned_to = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $report_id, $worker_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$report) {
    die("Report not found or not assigned to you.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];

    if (in_array($new_status, ['assigned', 'resolved'])) {
        $update_sql = "UPDATE reports SET status = ? WHERE id = ? AND assigned_to = ?";
        $stmt2 = $conn->prepare($update_sql);
        $stmt2->bind_param("sii", $new_status, $report_id, $worker_id);
        $stmt2->execute();
        $stmt2->close();

        header("Location: view_report.php?id=" . $report_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        img { max-width: 100%; height: auto; }
        #map { width: 100%; height: 300px; margin-top: 10px; }
    </style>
</head>
<body>
    
<h2>Report Details</h2>

<p><b>User:</b> <?= $report['user_name'] ?> (<?= $report['email'] ?>)</p>
<p><b>Title:</b> <?= $report['title'] ?> </p>
<p><b>Description:</b> <?= $report['description'] ?> </p>

<?php if (!empty($report['photo_path'])): ?>
    <p><b>Photo:</b></p>
    <img src="../<?= $report['photo_path'] ?>" alt="Report Photo">
<?php endif; ?>

<p><b>Location:</b></p>
<div id="map"></div>

<script>
function initMap() {
    const reportLocation = { lat: <?= $report['latitude'] ?>, lng: <?= $report['longitude'] ?> };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: reportLocation
    });

    new google.maps.Marker({
        position: reportLocation,
        map: map
    });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>

<form method="POST">
    <label>Update Status:</label>
    <select name="status" required>
        <option value="" disabled selected>Select Status</option>

        <?php if($report['status'] === 'pending' || $report['status'] === 'assigned'): ?>
            <option value="assigned">In Progress</option>
        <?php endif; ?>

        <?php if($report['status'] !== 'resolved'): ?>
            <option value="resolved">Completed</option>
        <?php endif; ?>
    </select>

    <br><br>
    <button type="submit">Update Status</button>
</form>

<br>
<a href="home.php">Back to Dashboard</a>
</body>
</html>

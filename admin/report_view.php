<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

if (!isset($_GET['id'])) {
    die("No report ID found.");
}

$report_id = (int)$_GET['id'];

$sql = "SELECT r.*, u.name AS user_name, u.email FROM reports r JOIN users u ON r.user_id = u.id WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$report = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$report) {
    die("Report not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Details</title>
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
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            margin-top: 0;
            text-align: center;
        }

        .section {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        img {
            max-width: 100%;
            border-radius: 6px;
            margin-top: 10px;
        }

        #map {
            width: 100%;
            height: 300px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 15px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }

        .btn-back {
            background: #607d8b;
        }

        .btn-assign {
            background: #2196f3;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 13px;
            background: #eee;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Report Details</h2>

        <div class="section">
            <div class="label">Reported By</div>
            <?= htmlspecialchars($report['user_name']) ?>
            (<?= htmlspecialchars($report['email']) ?>)
        </div>

        <div class="section">
            <div class="label">Title</div>
            <?= htmlspecialchars($report['title']) ?>
        </div>

        <div class="section">
            <div class="label">Description</div>
            <?= nl2br(htmlspecialchars($report['description'])) ?>
        </div>

        <div class="section">
            <div class="label">Status</div>
            <span class="status"><?= htmlspecialchars($report['status']) ?></span>
        </div>

        <?php if (!empty($report['photo_path'])): ?>
            <div class="section">
                <div class="label">Photo Evidence</div>
                <img src="../<?= htmlspecialchars($report['photo_path']) ?>" alt="Report Photo">
            </div>
        <?php endif; ?>

        <?php if (!empty($report['latitude']) && !empty($report['longitude'])): ?>
            <div class="section">
                <div class="label">Location</div>
                <div id="map"></div>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="dashboard.php" class="btn btn-back">Back</a>
            <a href="assign_worker.php?id=<?= $report['id'] ?>" class="btn btn-assign">Assign Worker</a>
        </div>
    </div>

    <script>
        function initMap() {
            const reportLocation = {
            lat: <?= $report['latitude'] ?>, 
            lng: <?= $report['longitude'] ?>
        };

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15, 
            center: reportLocation
        });

        new google.maps.Marker({
            position: reportLocation, map:map
        });
    }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>

</body>
</html>
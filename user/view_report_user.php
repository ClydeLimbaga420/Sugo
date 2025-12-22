<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

if (!isset($_GET['id'])) {
    die("No report ID provided.");
}

$report_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM reports WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $report_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();
$stmt->close();

if (!$report) {
    die("Report not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Report</title>
    <style>
        body {
            font-family: Arial;
            background: #f1f1f1;
            padding: 20px;
        }
        .box {
            background: white;
            padding: 20px;
            max-width: 700px;
            margin: auto;
            border-radius: 8px;
        }
        img {
            max-width: 100%;
            margin-top: 10px;
        }
        #map {
            width: 100%;
            height: 300px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>My Report Details</h2>

    <p><b>Title: </b><?= htmlspecialchars($report['title']) ?></p>
    <p><b>Description: </b><br><?= nl2br(htmlspecialchars($report['description'])) ?></p>
    <p><b>Status:</b><?= ucfirst($report['status']) ?></p>
    <?php if (!empty($report['photo_path'])): ?>
        <p><b>Photo:</b></p>
        <img src="../<?= $report['photo_path'] ?>">
    <?php endif; ?> 

    <p><b>Location:</b></p>
    <div id="map"></div>

    <script>
        function initMap() {
            const location = {
                lat: <?= $report['latitude'] ?>,
                lng: <?= $report['longitude'] ?>
            };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: location
            });

            new google.maps.Marker({
                position: location,
                map: map
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" 
        async defer>
    </script>

    <br>
    <a href="my_reports.php">Back to My Reports</a>
</div>
    
</body>
</html>
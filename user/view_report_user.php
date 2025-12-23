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
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }

        .container {
            max-width: 800px;
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

        .row {
            margin-top: 15px;
        }

        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .assigned {
            background: #d4edda;
            color: #155724;
        }

        .resolved {
            background: #f8d7da;
            color: #721c24;
        }

        img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }

        #map {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 15px;
            background: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .back-btn:hover {
            background: #1977d2;
        }
    </style>
</head>
<body>
    
    <div class="container">

        <h2>My Report Details</h2>

        <div class="row">
            <span class="label">Title</span>
            <?= htmlspecialchars($report['title']) ?>
        </div>

        <div class="row">
            <span class="label">Description</span>
            <?= nl2br(htmlspecialchars($report['description'])) ?>
        </div>

        <div class="row">
            <span class="label">Status</span>

            <?php
            $statusClass = strtolower($report['status']);
            ?>

            <span class="status <?= $statusClass ?>">
                <?= ucfirst($report['status']) ?>
            </span>
        </div>

        <?php if (!empty($report['photo_path'])): ?>
            <div class="row">
                <span class="label">Photo</span>
                <img src="../<?= htmlspecialchars($report['photo_path']) ?>">
            </div>
        <?php endif; ?>

        <div class="row">
            <span class="label">Location</span>
            <div id="map"></div>
        </div>

        <a href="my_reports.php" class="back-btn">Back to My Reports</a>
    

    </div>

    <script>
        function initMap() {
            const location = {
                lat: <?= (float)$report['latitude'] ?>,
                lng: <?= (float)$report['longitude'] ?>
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

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap"
        async defer>
    </script>

</body>
</html>
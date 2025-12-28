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

if (isset($_GET['read'])) {
    $notif_id = (int)$_GET['read'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();
}

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
    <title>SUGO | View Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --success-color: #4caf50;
            --pending-color: #ff9800;
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
            padding: 20px;
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

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .container {
            max-width: 700px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header i {
            font-size: 45px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        h2 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }

        .report-card {
            display: grid;
            gap: 20px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 12px;
            border-left: 5px solid var(-primary-color);
        }

        .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            color: #333;
            font-size: 16px;
            line-height: 1.5;
        }

        .status-pill {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .status-assigned {
            background: #cfe2ff;
            color: #084298;
            border: 1px solid #b6d4fe;
        }

        .status-resolved {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .report-photo {
            width: 100%;
            max-height: 350px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
            margin-top: 10px;
        }

        #map {
            width: 100%;
            height: 250px;
            border-radius: 15px;
            margin-top: 10px;
            border: 1px solid #ddd; 
        }

        .btn-group {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }

        .back-btn {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: #607d8b;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #455a64;
            transform: translateY(-2px);
        }

        .action-btn {
            flex: 1;
            text-align: center;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        action-btn:hover {
            background: #1976d2;
            transform: translateY(-2px);
        }
        
    </style>

</head>
<body>
    
    <div class="container">
        <div class="header">
            <i class="fas fa-clipboard-check"></i>
            <h2>Report Details</h2>
            <p style="color: #777; font-size: 14px;">Case ID: #<?= $report['id'] ?></p>
        </div>

        <div class="report-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div class="info-box" style="flex: 2; margin-right: 10px;">
                    <span class="label">Issue Title</span>
                    <span class="value"><strong><?= htmlspecialchars($report['title']) ?></strong></span>
                </div>

                <div class="info-box" style="flex: 1; border-left-color: var(--sucess-color);">
                    <span class="label">Current Status</span>
                    <span class="status-pill status-<?= strtolower($report['status']) ?>">
                        <?= ucfirst($report['status']) ?>
                    </span>

                </div>
        </div>

        <div class="info-box">
            <span class="label">Description</span>
            <span class="value"><?= nl2br(htmlspecialchars($report['description'])) ?></span>
        </div>

        <?php if (!empty($report['photo_path'])): ?>
            <div class="info-box">
                <span class="label">Evidence Photo</span>
                <img class="report-photo" src="../<?= htmlspecialchars($report['photo_path']) ?>" alt="Report Photo">
            </div>
        <?php endif; ?>

        <div class="info-box">
            <span class="label"><i class="fas fa-map-marker-alt"></i> Incident Location</span>
            <div id="map"></div>
        </div>

        <div class="btn-group">
            <a href="my_reports.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to List</a>
            <a href="home.php" class="action-btn"><i class="fas fa-home"></i> Dashboard</a>
        </div>
    </div>

    <script>
        function initMap() {
            const reportLoc = {
                lat: <?= (float)$report['latitude'] ?>,
                lng: <?= (float)$report['longitude'] ?>
            };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: reportLoc,
                disbaleDefaultUI: true,
                zoomControl: true
            });

            new google.maps.Marker({
                position: reportLoc,
                map: map,
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                animation: google.maps.Animation.BOUNCE
            });

            setTimeout(() => {
                marker.setAnimation(null);
            }, 2000);
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>
</body>
</html>
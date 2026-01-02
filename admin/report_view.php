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
    <title>View Report | SUGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --admin-primary: #673ab7;
            --admin-secondary: #9c27b0;
            --bg-gradient: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460 );
            --glass: rgba(255, 255, 255, 0.95);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            margin: 0;
            padding: 40px 20px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: var(--glass);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f2f5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .report-title h2 {
            margin: 0;
            font-size: 24px;
            color: #1a1a2e;
        }

        .badge-id {
            background: var(--admin-primary);
            color: white;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #edf2f7;
        }

        .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .value {
            font-weight: 500;
            color: #1e293b;
        }

        .media-section {
            margin-top: 30px;
        }

        .photo-frame {
            background: #000;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        img {
            width: 100%;
            display: block;
            transition: transform 0.3s;
        }

        img:hover {
            transform: scale(1.02);
        }

        #map {
            width: 100%;
            height: 350px;
            border-radius: 16px;
            border: 4px solid white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .actions {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #f0f2f5;
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-back {
            background: #f1f5f9;
            color: #64748b;
        }

        .btn-assign {
            background: var(--admin-primary);
            color: white;
        }

        .btn-notify {
            background: #10b981;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .status-pill {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background: #e2e8f0;
            color: #475569;
            text-transform: uppercase;
        }

        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <header>
            <div class="report-title">
                <span class="badge-id">CASE #<?= $report['id'] ?></span>
                <h2>Report Details</h2>
            </div>
            <span class="status-pill"><?= htmlspecialchars($report['status']) ?></span>
        </header>

        <div class="details-grid">
            <div class="info-grid">
                <div class="label"><i class="fas fa-user"></i> Reported By</div>
                <div class="value"><?= htmlspecialchars($report['user_name']) ?></div>
                <div style="font-size: 12px; color: #64748b;"><?= htmlspecialchars($report['email']) ?></div>
            </div>

            <div class="info-card">
                <div class="label"><i class="fas fa-tag"></i> Subject</div>
                <div class="value"><?= htmlspecialchars($report['title']) ?></div>
            </div>
        </div>

        <div class="info-card" style="margin-bottom: 30px;">
            <div class="label"><i class="fas-fa-align-left"></i> Description</div>
            <div class="value" style="line-height: 1.6;">
                <?= nl2br(htmlspecialchars($report['description'])) ?>
            </div>
        </div>

        <div class="media-section">
            <?php if (!empty($report['photo_path'])): ?>
                <div class="label"><i class="fas fa-camera"></i> Photo</div>
                <div class="photo-frame">
                    <img src="../<?= htmlspecialchars($report['photo_path']) ?>" alt="Report Photo">
                </div>
            <?php endif; ?>

            <?php if (!empty($report['latitude']) && !empty($report['longitude'])): ?>
                <div class="label"><i class="fas fa-map-marker-alt"></i> Exact Location</div>
                <div id="map"></div>
            <?php endif; ?>
        </div>

        <div class="actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-chevron-left"></i> Back
                </a>
                <a href="assign-worker.php?id=<?= $report['id'] ?>" class="btn btn-assign">
                    <i class="fas fa-user-hard-hat"></i> Assign Worker
                </a>
                <a href="notify_user.php?id=<?= $report['id'] ?>" class="btn btn-notify">
                    <i class="fas fa-paper-plane"></i> Notify User
                </a>
        </div>
    
    </div>

    <script>
        function initMap() {
            const reportLocation = {
                lat: <?= (float)$report['latitude'] ?>,
                lng: <?= (float)$report['longitude'] ?>
            };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 16,
                center: reportLocation
            });

            new google.maps.Marker({
                position: reportLocation,
                map: map,
                animation: google.maps.Animation.DROP
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>


</body>
</html>
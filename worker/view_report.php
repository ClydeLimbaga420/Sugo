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
        
        body {
            font-family: 'Segoi UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #00bcd4, #2196f3);
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            color: #2196f3;
            margin-bottom: 25px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .info-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            line-height: 1.6;
        }

        .badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .assigned {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .resolved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 12px;
            margin-top: 10px;
        }

        #map {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            margin-top: 10px;
        }

        form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        select, button {
            padding: 12px;
            font-size: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        button {
            background: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #43a047;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2196f3;
            font-weight: 500;
        }

        .back:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            form {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Report Details</h2>

        <div class="section">
            <div class="section-title">Reporter Information</div>
            <div class="info-box">
                <b>Name:</b> <?= htmlspecialchars($report['user_name']) ?><br>
                <b>Email:</b> <?= htmlspecialchars($report['email']) ?>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Report Information</div>
            <div class="info-box">
                <b>Title:</b> <?= htmlspecialchars($report['title']) ?><br>
                <b>Description:</b><br>
                <?= nl2br(htmlspecialchars($report['description'])) ?><br><br>
                <b>Status:</b>
                <span class="badge <?= $report['status'] ?>">
                    <?= ucfirst($report['status']) ?>
                </span>
            </div>
        </div>

        <?php if (!empty($report['photo_path'])): ?>
            <div class="section">
                <div class="section-title">Photo Evidence</div>
                <img src="../<?= $report['photo_path'] ?>" alt="Report Photo">
            </div>
        <?php endif; ?>

        <div class="section">
            <div class="section-title">Location</div>
            <div id="map"></div>
        </div>

        <div class="section">
            <div class="section-title">Update Status</div>
            <form method="POST">
                <select name="status" required>
                    <option disabled selected>Select Status</option>

                    <?php if ($report['status'] !== 'resolved'):?>
                        <option value="assigned">In Progress</option>
                        <option value="resolved">Completed</option>
                    <?php endif; ?> 
                </select>

                <button type="submit">Update</button>
            </form>
        </div>

        <a class="back" href="home.php">Back To Dashboard</a>

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
                position: reportLocation,
                map: map
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>

</body>
</html>
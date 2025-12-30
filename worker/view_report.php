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
    <title>Report Details | SUGO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary: #2196f3;
            --success: #4caf50;
            --warning: #ffa000;
            --bg-gradient: linear-gradient(135deg, #0f2027, #203a43, #2c5364 );
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 900px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .page-header {
            background: white;
            padding: 30px 40px;
            border-bottom: 2px solid #f0f2f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h2 {
            margin: 0;
            font-size: 22px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header i {
            color: var(--primary);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            padding: 40px;
        }

        .section {
            margin-bottom: 5px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            color: #aaa;
            letter-spacing: 1px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #edf2f7;
            height: 100%;
            box-sizing: border-box;
        }

        .info-card b {
            color: #555;
            font-size: 14px;
        }

        .info-card p {
            margin: 5px 0 0;
            color: #333;
            font-weight: 500;
        }

        .badge {
            padding: 6px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .pending {
            background: #fff8e1;
            color: #ffa000;
        }

        .assigned {
            background: #e3f2fd;
            color: #1976d2;
        }

        .resolved {
            background: #e8f5e9;
            color: #388e3c;
        }

        .media-section {
            padding: 0 40px 40px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .img-container, #map {
            width: 100%;
            height: 250px;
            border-radius: 16px;
            object-fit: cover;
            border: 4px solid #f8fafc;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .update-box {
            background: #f1f5f9;
            padding: 30px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .update-form {
            display: flex;
            gap: 15px;
        }

        select {
            padding: 12px 20px;
            border-radius: 12px;
            border: 2px solid #ddd;
            font-family: inherit;
            font-weight: 600;
            outline: none;
        }

        button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        button:hover {
            background: #1976d2;
            transform: translateY(-2px);
        }

        .back-link {
            padding: 20px 40px;
            text-align: center;
        }

        .back-link a {
            text-decoration: none;
            color: #888;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .back-link a:hover {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .content-grid, .media-section, .update-box {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            .update-box {
                flex-direction: column;
                gap: 20px;
            }
            .update-form {
                width: 100%;
                flex-direction: column;
            }
        }
    </style>

</head>
<body>
    
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-file-alt"> Report #<?= $report['id'] ?></i></h2>
            <span class="badge <?= $report['status'] ?>">
                <i class="fas fa-circle" style="font-size: 8px; margin-right: 5px;" ></i>
                <?= ucfirst($report['status'] === 'assigned' ? 'In Progress' : $report['status']) ?>
            </span>
        </div>

        <div class="content-grid">
            <div class="section">
                <div class="section-title"><i class="fas fa-user"></i> Respondent</div>
                <div class="info-card">
                    <b>Name</b> <p><?= htmlspecialchars($report['user_name']) ?></p>
                    <div style="margin-top: 10px;"></div>
                    <b>Email</b> <p><?= htmlspecialchars($report['email']) ?></p>
                </div>
            </div>

            <div class="section">
                <div class="section-title"><i class="fas fa-info-circle"></i> Issue Details</div>
                <div class="info-card">
                    <b>Subject</b> <p><?= htmlspecialchars($report['title']) ?></p>
                    <div style="margin-top: 10px; "></div>
                    <b>Description</b> <p style="font-size: 13px; line-height: 1.5; font-weight: 400;"><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                </div>
            </div>
        </div>

        <div class="media-section">
        <div class="section">
            <div class="section-title"><i class="fas fa-camera"></i> Evidence</div>
            <?php if (!empty($report['photo_path'])): ?>
                <img src="../<?= $report['photo_path'] ?>" class="img-container" alt="Report Photo">
            <?php else: ?>
                <div class="info-card" style="display: flex; align-items: center; justify-content: center; height: 250px; color: #ccc;">
                    No Photo Attached!
                </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-title"><i class="fas fa-map-marker-alt"></i> 
                Location
            </div>
            <div id="map"></div>
        </div>
    </div>

    <div class="update-box">
        <div class="section-title" style="margin: 0; color: #444;">Update Work Progress</div>
        <form class="update-form" method="POST">
                <select name="status" required>
                    <option disabled selected>Choose Status</option>
                    <?php if ($report['status'] !== 'resolved'): ?>
                        <option value="assigned">In Progress</option>
                        <option value="resolved">Mark as Completed</option>
                    <?php endif; ?>
                </select>

                <button type="submit" style="font-family: 'Poppins', sans-serif">
                    <i class="fas fa-sync-alt"></i> Update Status
                </button>
        </form>
    </div>
    
    <div class="back-link">
        <a href="home.php"><i class="fas fa-chevron-left"></i> Home</a>
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
            center: reportLocation,
        });

        new google.maps.Marker({
            position: reportLocation,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        });
    }
</script>
    
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>

    

</body>
</html>
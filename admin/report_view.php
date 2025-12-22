<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

if (!isset($_GET['id'])) {
    echo "No report ID found.";
    exit;
}

$report_id = $_GET['id'];

$sql = "SELECT r.*, u.name AS user_name, u.email 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$res = $stmt->get_result();
$report = $res->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Report Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        #map {
            width: 100%;
            height: 300px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h2>Report Details</h2>

<p><b>User:</b> <?= $report['user_name'] ?> (<?= $report['email'] ?>)</p>
<p><b>Title:</b> <?= $report['title'] ?></p>
<p><b>Description:</b> <?= $report['description'] ?></p>

<?php if (!empty($report['photo_path'])): ?>
    <p><b>Photo:</b></p>
    <img src="../<?= $report['photo_path'] ?>" alt="Report Photo">
<?php endif; ?>

<p><b>Location:</b></p>
<div id="map"></div>

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

<br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>

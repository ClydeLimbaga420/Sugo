<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /Sugo/auth/login.php");
    exit;
}

require "../config/db.php";

$report_id =$_GET['id'];
$sql =  "SELECT r.*, u.name as username, u.email FROM reports r JOIN users u ON r.user_id = u.id WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$res = $stmt->get_result();
$report = $res->fetch_assoc();
?>

<h2>Report Details</h2>
<p><b>User: </b> <?= $report['user_name'] ?> (<?= $report['email'] ?>)</p>
<p><b>Title: </b> <?= $report['title'] ?></p>
<p><b>Description: </b> <?= $report['description'] ?></p>

<?php if($report['photo_path']): ?>
    <p><b>Photo: </b> <img src="../<?= $report['photo_path'] ?>" width="300"></p>
    <?php endif; ?>

    <p><b>Location: </b></p>
    <div id="map" style="height: 300px; width: 100%;"></div>

    <script>
        let map = new google.maps.Map(document.getElementById("map"), {
            zoom: 8,
            center: {
                lat: <?= $report['latitude'] ?>, lng: <?= $report['longitude'] ?>> }
            
        });

        new google.maps.Marker({
            position: {
                lat: <?= $report['latitude'] ?>, lng: <?= $report['longitude'] ?> },
                map: map
            
        });
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
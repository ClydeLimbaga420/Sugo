<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /xampp2/htdocs/Sugo/auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Create Report</title>
        <style>
            body {
                font-family: Arial;
                background: #f1f1f1;
                padding: 20px;
            }
            .box {
                background: white;
                padding: 20px;
                width: 500px;
                margin: auto;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            }
            input, textarea, button {
                width: 100%;
                padding: 10px;
                margin-top: 10px;
                border-radius: 5px;
                border: 1px solid #ccc;
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
            <h2>Create Report</h2>
            <form action="report_save.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Title" required>
                <textarea name="description" rows="4" placeholder="Description" required></textarea>
                <input type="file" name="photo" accept="image/*">
                <h3>Select Location</h3>
                <div id="map"></div>
                <input type="hidden" name="latitude" id="lat">
                <input type="hidden" name="longitude" id="lng">

                <button type="submit">Submit Report</button>
            </form>
        </div>
        <script>
    let map, marker;

    function initMap() {
        const defaultLoc = { lat: 9.6647, lng:123.3256 };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 6,
            center: defaultLoc
        });

        map.addListener("click", (e) => {
            const pos = e.latLng;

            if (!marker) {
                marker = new google.maps.Marker({
                    position: pos,
                    map: map
                });
            } else {
                marker.setPosition(pos);
            }

            document.getElementById("lat").value = pos.lat();
            document.getElementById("lng").value = pos.lng();
        });
    }

    window.onload = initMap;
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc" async defer></script>

    </body>
</html>
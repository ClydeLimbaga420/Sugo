<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: /xampp2/htdocs/Sugo/auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report</title>
    <style>
        body {
            margin:0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }

        .container {
            max-width: 600px;
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

        form {
            max-width: 100%;
            margin: 0 auto;
        }

        .form-group {
            margin-top: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input, textarea {
            width: 90%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            display: block;
            margin: 0 auto;
        }

        textarea {
            resize: vertical;
        }

        button {
            margin: 20px auto 0 auto;
            width: 90%;
            padding: 12px;
            border-radius: 6px;
            border: none;
            background: #2196f3;
            color: white;
            font-size: 15px;
            cursor: pointer;
            display: block;
        }

        button:hover {
            background: #1976d2;
        }

        #map {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            margin-top: 10px;
        }

        .map-hint {
            font-size:13px;
            color: #666;
            margin-top: 5px;
        }

        label {
            font-weight: bold;
            display: block;
            width: 90%;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>
    <div class="container">

        <h2>Create Report</h2>

        <form action="report_save.php" method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" placeholder="Enter Report Title" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4" placeholder="Describe the issue" required></textarea>
        </div>

        <div class="form-group">
            <label>Photo (optional)</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        <div class="form-group">
            <label>Select Location</label>
            <div id="map"></div>
            <div class="map-hint">Click the map to pin the location of the incident.</div>
        </div>

        <input type="hidden" name="latitude" id="lat">
        <input type="hidden" name="longitude" id="lng">

        <button type="submit">Submit Report</button>

    </form>
    </div>

    <script>
        let map, marker;

        function initMap() {
            const defaultLoc = {
                lat: 9.6647, lng: 123.3256
            };

            map =new google.maps.Map(document.getElementById("map"), {
                zoom: 6,
                center: defaultLoc
            });

            map.addListener("click", (e) => {
                const pos = e.lating;

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
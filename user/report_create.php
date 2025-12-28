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
    <title>SUGO | Create Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --success-color: #4caf50;
            --bg-gradient: linear-gradient(-45deg, #00bcd4, #3f51b5, #00bcd4);
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

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 35px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 25px;
            left: 25px;
            color: #777;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .back-btn:hover {
            color: var(--primary-color);
        }

        .header-icon {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 10px;
            text-align: center;
            display: block;
        }

        h2 {
            margin: 0 0 10px 0;
            text-align: center;
            color: #333;
            font-weight: 600;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #444;
            font-size: 14px;
        }

        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #fdfdfd;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
            transition: 0.3s;
        }

        input:focus, textarea:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 8px rgba(33, 150, 243, 0.2);
        }

        #map {
            width: 100%;
            height: 250px;
            border-radius: 12px;
            border: 2px solid #eee;
            margin-top: 5px;
        }

        .map-hint {
            font-size: 12px;
            color: #888;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        button {
            width: 100%;
            padding: 15px;
            border-radius: 10px;
            border: none;
            background: var(--primary-color);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }

        button:hover {
            background: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.4);
        }

        input[type="file"]::file-selector-button {
            background: #eee;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 10px;
        }


    </style>
</head>
<body>
    
    <div class="container">
        <a href="home.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
    
        <i class="fas fa-file-medical header-icon"></i>
        <h2>Create Report</h2>
        <p class="subtitle">Provide details about the location or item that needs fixing.</p>

        <form action="report_save.php" method="POST" enctype="multipart/form-data">

            <div class="form-group">
                <label><i class="fas fa-heading"></i>  Report Title</label>
                <input type="text" name="title" placeholder="e.g. Broken Street Light" required>
            </div>

            <div class="form-group">
                <label><i class="fas fa-align-left"></i>  Description</label>
                <textarea name="description" rows="3" placeholder="Describe the issue in details..." required></textarea>
            </div>

            <div class="form-group">
                <label><i class="fas fa-camera"></i>  Upload Photo</label>
                <input type="file" name="photo" accept="image/*">
            </div>

            <div class="form-group">
                <label><i class="fas fa-map-marker-alt"></i>  Pin Location</label>
                <div id="map"></div>
                <div class="map-hint">
                    <i class="fas fa-info-circle"></i>Click on the map to pin the location.
                </div>
            </div>

            <input type="hidden" name="latitude" id="lat">
            <input type="hidden" name="longitude" id="lng">

            <button type="submit"><i class="fas fa-paper-plane"></i>    Submit</button>

        </form>
    </div>

    <script>
        let map, marker;

        function initMap() {
            const defaultLoc = {
                lat: 9.6577778,
                lng: 123.3294444
            };

            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 13,
                center: defaultLoc
            });

            map.addListener("click", (e) => {
                const pos = e.latLng;

                if (!marker) {
                    marker = new google.maps.Marker({
                        position: pos,
                        map: map,
                        animation: google.maps.Animation.DROP
                    });
                } else {
                    marker.setPosition(pos);
                }

                document.getElementById("lat").value = pos.lat();
                document.getElementById("lng").value = pos.lng();
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC1g808lCIljv4UQPq6fLfa6uwdLiiLpsc&callback=initMap" async defer></script>

</body>
</html>
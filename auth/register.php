<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUGO | Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --success-color: #4caf50;
            --bg-gradient: linear-gradient(-45deg, #00bcd4, #2196f3, #3f51b5, #00bcd4);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
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

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .register-box {
            background: rgba(255,255,255,0.95);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            margin: 20px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            text-align: center;
            box-sizing: border-box;
            animation: slideIn 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            backdrop-filter: blur(10px);
        }

        .logo-area {
            width: 60px;
            height: 60px;
            background: var(--success-color);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px;
            font-size: 24px;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        }

        .register-box h2 {
            margin: 0 0 10px;
            color: #333;
            font-weight: 600;
        }

        .subtitle-container {
            width: 100%;
            overflow: hidden;
            margin-bottom: 25px;
            position: relative;
            mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
        }

        .subtitle {
            font-size: 13px;
            color: #777;
            white-space: nowrap;
            display: inline-block;
            padding-left: 100%;
            animation: rotateSubtitle 20s linear infinite;
        }

        @keyframes rotateSubtitle {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-200%);
            }
        }

        .input-group {
            position: relative;
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            transition: 0.3s;
        }

        .register-box input, .register-box select {
            width: 100%;
            padding: 12px 12px 12px 45px;
            border-radius: 10px;
            border: 1px solid #eee;
            background: #fdfdfd;
            font-size: 14px;
            transition: 0.3s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .register-box select {
            appearance: none;
            cursor: pointer;
        }

        .register-box input:focus, .register-box select:focus {
            border-color: var(--primary-color);
            outline: none;
            background: #fff;
            box-shadow: 0 0 8px rgba(31, 150, 243, 0.2);
        }

        .register-box input:focus + i, .register-box select:focus + i {
            color: var(--primary-color);
        }
        
        .register-box button {
            width: 100%;
            padding: 14px;
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            background: var(--success-color);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
        }

        .register-box button:hover {
            background: #388e3c;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(76, 175, 80, 0.3);
        }

        .footer-links {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

    </style>
</head>
<body>
    
    <div class="register-box">
        <div class="logo-area">
            <i class="fas fa-user-plus"></i>
        </div>

        <h2>Join SUGO</h2>
        <div class="subtitle-container">
            <p class="subtitle">Help People and Let Us Help Your Problems</p>
        </div>
        <form action="register_process.php" method="POST">
            <div class="input-group">
                <i class="fa fa-user"></i>
                <input type="text" name="name" placeholder="Full Name" required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Create Password" required>
            </div>

            <div class="input-group">
                <i class="fas fa-briefcase"></i>
                <select name="role" required>
                    <option value="" disabled selected>Select Your Role</option>
                    <option value="user">Standard User</option>
                    <option value="worker">Service Worker</option>
                </select>
            </div>

            <button type="submit">CREATE ACCOUNT</button>
        </form>

        <div class="footer-links">
            Already have an account? <a href="login.php">Login Here</a>
        </div>
    </div>

</body>
</html>
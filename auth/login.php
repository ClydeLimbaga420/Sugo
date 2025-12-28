<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUGO Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #2196f3;
            --accent-color: #00bcd4;
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
            height: 100vh;
            margin: 0;
            overflow: hidden;
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

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 380px;
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            text-align: center;
            box-sizing: border-box;
            animation: slideUp 0.8s ease-out;
            backdrop-filter: blur(10px);
        }

        .logo-area {
            width: 70px;
            height: 70px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 20px;
            font-size: 30px;
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.4);
        }

        .login-box h2 {
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .login-box p.subtitle {
            color: #777;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            transition: 0.3s;
        }

        .login-box input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border-radius: 10px;
            border: 1px solid #eee;
            background: #f9f9f9;
            transition: 0.3s;
            box-sizing: border-box;
        }

        .login-box input:focus {
            background: #fff;
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 8px rgba(33, 150, 243, 0.2);
        }

        .login-box input:focus + i {
            color: var(--primary-color);
        }

        .login-box button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(33, 150, 243,0.3);
        }

        .login-box button:hover {
            background: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(33, 150, 243, 0,4);
            
        }

        .login-box button:active {
            transform: translateY(0);
        }

        .footer-links {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .footer-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    
    <div class="login-box">
        <div class="logo-area">
            <i class="fa fa-paper-plane"></i>
        </div>

        <h2>Welcome Back</h2>
        <p class="subtitle">Login to yout SUGO account</p>

        <form action="login_process.php" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password"name="password" placeholder="Password" required>
            </div>

            <button tyoe="submit">LOGIN</button>
        </form>

        <div class="footer-links">
            Don't have an account? <a href="register.php">Sign Up</a>
        </div>
    </div>

</body>
</html>
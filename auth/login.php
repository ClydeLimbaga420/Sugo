<!DOCTYPE html>
<html>
    <head>
        <title>SUGO Login</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #00bcd4, #2196f3);
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .login-box {
                background: white;
                width: 100%;
                max-width:360px;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.2);
                text-align: center;
                box-sizing: border-box;
            }
            .login-box h2 {
                margin-bottom: 30px;
                color: #2196f3;
                font-size: 26px;
            }
            .login-box input[type="email"], .login-box input[type="password"] {
                width: 100%;
                padding: 12px;
                margin-bottom: 20px;
                border-radius: 8px;
                border: 1px solid #ccc;
                font-size: 16px;
                transition: 0.3s;
                box-sizing: border-box;
            }
            .login-box input:focus {
                border-color: #2196f3;
                outline: none;
                box-shadow: 0 0 5px rgba(33, 150, 243, 0.5);
            }
            .login-box button {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 8px;
                background: #2196f3;
                color: white;
                font-size: 16px;
                cursor: pointer;
                transition: 0.3s;
            }
            .login-box button:hover {
                background: #1976d2;
            }
            .login-box p{
                margin-top: 20px;
                font-size: 14px;
                color: #555;
            }
            .login-box a{
                color: #2196f3;
                text-decoration: none;
                font-weight: bold;
            }
            .login-box a:hover {
                text-decoration: underline;
            }
            
            </style>
    </head>

    <body>

    <div class="login-box">
        <h2>Login to SUGO</h2>

        <form action="login_process.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form> 

        <p style="margin-top:10px;">No account? <a href="register.php">Register Here</a>
        </p>
    </div>
    </body>
</html>
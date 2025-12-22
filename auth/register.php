<!DOCTYPE html>
<html>
    <head>
        <title>SUGO Register</title>
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
            .register-box {
                background: white;
                width: 100%;
                max-width: 380px;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.2);
                text-align: center;
                box-sizing: border-box;

            }
            .register-box h2 {
                margin-bottom: 25px;
                color: #2196f3;
                font-size: 26px;
            }
            .register-box input, .register-box select {
                width: 100%;
                padding: 12px;
                margin-bottom: 18px;
                border-radius: 8px;
                border: 1px solid #ccc;
                font-size: 15px;
                box-sizing: border-box;
                transition: 0.3s;
            }
            .register-box input:focus, .register-box select:focus {
                border-color: #2196f3;
                outline: none;
                box-shadow: 0 0 5px rgba(33,150,243,0.4);
            }
            .register-box button {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 8px;
                background: #4caf50;
                color: white;
                font-size: 16px;
                cursor: pointer;
                transition: 0.3s;
            }
            .register-box button:hover {
                background: #43a047;
            }
            .register-box p {
                margin-top: 18px;
                font-size: 14px;
                color: #555;
            }
            .register-box a {
                color: #2196f3;
                font-weight:bold;
                text-decoration: none;
            }
            .register-box a:hover {
                text-decoration: underline;
            }
            
        </style>
    </head>
    <body>
        <div class="register-box">
            <h2>Create Account</h2>

            <form action="register_process.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>

                <select name="role" required>
                    <option value="" disabled selected>Select Role</option>
                    <option value="user">User</option>
                    <option value="worker">Worker</option>
                </select>


                <button type="submit">Register</button>
            </form>

            <p style="margin-top:10px;">Already have an account? <a href="login.php">Login</a></p>

        </div>
    </body>
</html>
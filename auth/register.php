<!DOCTYPE html>
<html>
    <head>
        <title>SUGO Register</title>
        <style>
            body {
                font-family: arial;
                background: #e3f2fd;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .box {
                background: white;
                width: 350px;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            }
            input, button {
                width: 100%;
                padding: 10px;
                margin-top: 10px;
            }
            button {
                background: #4caf50;
                border: none;
                border-radius: 5px;
                color: white;
            }
            button:hover {
                background: #43a047;
            }
        </style>
    </head>
    <body>
        <div class="box">
            <h2>Create Account</h2>

            <form action="register_process.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <select name="role" required>
                    <option value="user">User</option>
                    <option value="worker">Worker</option>
                </select>


                <button type="submit">Register</button>
            </form>

            <p style="margin-top:10px;">Already have an account? <a href="login.php">Login</a></p>

        </div>
    </body>
</html>
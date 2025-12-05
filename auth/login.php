<!DOCTYPE html>
<html>
    <head>
        <title>SUGO Login</title>
        <style>
            body {
                font-family: Arial;
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
                background: #2196f3;
                color: white;
                border: none;
                border-radius: 5px;
            }
            button:hover {
                background: #1976d2;
            }
            </style>
    </head>

    <body>

    <div class="box">
        <h2>Login to SUGO</h2>

        <form action="login_process.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">LOGIN</button>
        </form> 
    </div>
    </body>
</html>
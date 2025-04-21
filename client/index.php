<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/index.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="background"></div>

    <div class="loginBox">
        <h1>Login</h1><br>
        <form action="../server/controllers/LoginController.php" method="post" onsubmit="validateForm(event)">
            <label for="username">Username/StudentID</label>
            <p id="usernameError" style="color: red; margin-left:5%;"></p>
            <input type="text" id="username" name="username" placeholder="Username/StudentID"><br><br>
            <label for="password">Password</label>
            <p id="passwordError" style="color: red; margin-left:5%;"></p>
            <input type="password" id="password" name="password" placeholder="Password"><br><br>

            <?php if (isset($_GET['error'])): ?>
                <p style="color: red; text-align:center;">
                    <?php
                    if ($_GET['error'] === 'invalid_credentials')
                        echo "Invalid username or password.";
                    ?>
                </p>
            <?php endif; ?>

            <input type="submit" value="Login">
        </form>
        <p>Don't have an account? <a href="pages/shared/SignUp.php">Sign Up</a> </p>
    </div>
    <script src="js/Login.js"></script>
</body>

</html>
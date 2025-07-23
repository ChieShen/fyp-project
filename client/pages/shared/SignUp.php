<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../../css/SignUp.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="background"></div>

    <div class="signUpBox">
        <h1>Sign Up</h1><br>
        <form action="../../../server/controllers/SignUpController.php" method="post" onsubmit="validateForm(event)">
            <label for="username">Username/StudentID</label>
            <p id="usernameError" style="color: red; "></p>
            <input type="text" id="username" name="username" placeholder="Username/StudentID"><br><br>

            <label for="fname">First Name</label>
            <p id="fnameError" style="color: red; "></p>
            <input type="text" id="fname" name="fname" placeholder="First Name"><br><br>

            <label for="lname">Last Name</label>
            <p id="lnameError" style="color: red; "></p>
            <input type="text" id="lname" name="lname" placeholder="Last Name/Family Name"><br><br>

            <label for="password">Password</label>
            <p id="passwordError" style="color: red; "></p>
            <input type="password" id="password" name="password" placeholder="Password"><br><br>

            <label for="confirmPassword">Confirm Password</label>
            <p id="confirmError" style="color: red; "></p>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Retype Password"><br><br>

            <p style="margin-left:5%">I am a</p>
            <div class="segmented-control">
                <input type="radio" id="student" name="role" value="student" checked>
                <label for="student">Student</label>

                <input type="radio" id="lecturer" name="role" value="lecturer">
                <label for="lecturer">Lecturer</label>
            </div>

            <script>
                function delayedRedirect() {
                    setTimeout(function () {
                        window.location.href = "../../index.php";
                    }, 3000);
                }
            </script>

            <?php
            session_start();
            if (isset($_SESSION['signup_error'])) {
                echo '<p style="color: red; text-align: center; font-size: 14px;">' . $_SESSION['signup_error'] . '<p>';
                unset($_SESSION['signup_error']); // Clear the message after displaying
            }

            if (isset($_SESSION['signup_success'])) {
                echo '<p style="color: #04aa6d; text-align: center; font-size: 14px;">' . $_SESSION['signup_success'] . '<p>';
                echo '<script> delayedRedirect()</script>';
                unset($_SESSION['signup_success']); // Clear the message after displaying
            }
            ?>

            <input type="submit" value="Sign Up">
        </form>
        <p>Already have an account? <a href="../../index.php">Login</a> </p>
    </div>
    <script src="../../js/SignUp.js"></script>
</body>

</html>
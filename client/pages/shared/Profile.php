<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}

$conn = (new Database())->connect();
$userModel = new UserModel($conn);
$userID = $_SESSION['userID'];
$user = $userModel->getUserById($userID);

$fName = $user['firstName'];
$lName = $user['lastName'];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/Profile.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="editBox">
            <div class="titleBar">
                <h1>Profile</h1>
            </div>

            <form action="/FYP2025/SPAMS/server/controllers/EditProfileController.php" method="post" onsubmit="validateForm(event)">

                <div class="contentBox">
                    <label for="fName">First Name</label>
                    <p id="fNameError" style="color: red; margin-left:5%;"></p>
                    <input type="text" id="fName" name="fName" placeholder="First Name"
                        value="<?= htmlspecialchars($fName) ?>"><br><br>

                    <label for="lName">Last Name</label>
                    <p id="lNameError" style="color: red; margin-left:5%;"></p>
                    <input type="text" id="lName" name="lName" placeholder="Last Name"
                        value="<?= htmlspecialchars($lName) ?>"><br><br>

                    <label for="curPass">New Password</label>
                    <p id="curPassError" style="color: red; margin-left:5%;"></p>
                    <input type="password" id="curPass" name="curPass" placeholder="Current Password"><br><br>

                    <label for="newPass">New Password</label>
                    <p id="newPassError" style="color: red; margin-left:5%;"></p>
                    <input type="password" id="newPass" name="newPass" placeholder="New Password"><br><br>

                    <label for="conPass">Confirm Password</label>
                    <p id="conPassError" style="color: red; margin-left:5%;"></p>
                    <input type="password" id="conPass" name="conPass" placeholder="Confirm Password"><br>

                    <?php
                    if (isset($_SESSION['edit_error'])) {
                        echo '<p style="color: red; text-align: center; font-size: 14px;">' . $_SESSION['edit_error'] . '
                    <p><br>';
                        unset($_SESSION['edit_error']);
                    }

                    if (isset($_SESSION['edit_success'])) {
                        echo '
                    <p style="color: #04aa6d; text-align: center; font-size: 14px;">' . $_SESSION['edit_success'] . '
                    <p><br>';
                        unset($_SESSION['edit_success']);
                    }
                    ?>

                </div>


                <button type="submit" id="save">Save</button>


            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/Profile.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
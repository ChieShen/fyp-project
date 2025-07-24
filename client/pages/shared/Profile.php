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

            <form action="/FYP2025/SPAMS/server/controllers/EditProfileController.php" method="post"
                onsubmit="validateForm(event)">

                <div class="contentBox">
                    <label for="fName">First Name</label>
                    <p id="fNameError" style="color: red; "></p>
                    <input type="text" id="fName" name="fName" placeholder="First Name"
                        value="<?= htmlspecialchars($fName) ?>"><br><br>

                    <label for="lName">Last Name</label>
                    <p id="lNameError" style="color: red; "></p>
                    <input type="text" id="lName" name="lName" placeholder="Last Name"
                        value="<?= htmlspecialchars($lName) ?>"><br><br>

                    <label for="curPass">Current Password</label>
                    <p id="curPassError" style="color: red; "></p>
                    <input type="password" id="curPass" name="curPass" placeholder="Current Password"><br><br>

                    <label for="newPass">New Password</label>
                    <p id="newPassError" style="color: red; "></p>
                    <input type="password" id="newPass" name="newPass" placeholder="New Password"><br><br>

                    <label for="conPass">Confirm Password</label>
                    <p id="conPassError" style="color: red; "></p>
                    <input type="password" id="conPass" name="conPass" placeholder="Confirm Password"><br>

                    <script>
                        const sessionMessage = <?= json_encode($_SESSION['edit_success'] ?? $_SESSION['edit_error'] ?? null) ?>;
                        const sessionType = <?= isset($_SESSION['edit_success']) ? json_encode('success') : (isset($_SESSION['edit_error']) ? json_encode('error') : 'null') ?>;
                    </script>

                    <?php
                        unset($_SESSION['edit_success'], $_SESSION['edit_error']);
                    ?>

                </div>


                <button type="submit" id="save">Save</button>


            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/Profile.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
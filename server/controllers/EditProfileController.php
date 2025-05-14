<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    echo "Unauthorized";
    exit();
}

$db = (new Database())->connect();
$userModel = new UserModel($db);

$userID = $_SESSION['userID'];
$fName = $_POST['fName'] ?? '';
$lName = $_POST['lName'] ?? '';
$curPass = $_POST['curPass'] ?? null;
$newPass = $_POST['newPass'] ?? null;
$conPass = $_POST['conPass'] ?? null;

$result = $userModel->updateUserProfile($userID, $fName, $lName, $curPass, $newPass);

if ($result === true) {
    $_SESSION['edit_success'] = "Profile updated successfully";
} elseif ($result === "wrong_password") {
    $_SESSION['edit_error'] = "Current password incorrect";
} else {
    $_SESSION['edit_error'] = "Update failed";
}

header("Location: /FYP2025/SPAMS/client/pages/shared/Profile.php");

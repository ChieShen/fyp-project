<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once '../models/UserModel.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $db = new Database();
    $conn = $db->connect();

    $userModel = new UserModel($conn);
    $user = $userModel->validateLogin($username, $password);

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['roleID'];
        $_SESSION['userID'] = $user['userID'];

        // ✅ Redirect based on roleID
        switch ($user['roleID']) {
            case 1:
                header("Location: ../../client/pages/student/SProjectList.php");
                break;
            case 2:
                header("Location: ../../client/pages/lecturer/LProjectList.php");
                break;
            default:
                header("Location: ../../client/pages/student/SProjectList.php"); // fallback
                break;
        }
        exit;
    } else {
        header("Location: ../../client/index.php?error=invalid_credentials");
        exit;
    }
} else {
    // Block direct access
    header("Location: ../index.php");
    exit;
}
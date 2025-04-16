<?php
session_start();

// Include database connection or user model
require_once '../models/UserModel.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Authenticate using model
    $userModel = new UserModel();
    $user = $userModel->validateLogin($username, $password);

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_id'] = $user['roleID']; // ✅ Add roleID to session

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
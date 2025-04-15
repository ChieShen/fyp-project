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
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: ../dashboard.php"); // or whatever your home page is
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
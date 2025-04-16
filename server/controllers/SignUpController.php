<?php
session_start();
require_once '../models/UserModel.php'; // Adjust the path as needed

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $username = trim($_POST['username'] ?? '');
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');
    $role = $_POST['role'] ?? '';

    // Basic server-side validation
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($role)) {
        $_SESSION['signup_error'] = "All fields are required.";
        header("Location: ../../pages/signup.php"); // adjust path to your signup page
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['signup_error'] = "Password must be at least 8 characters.";
        header("Location: ../../pages/signup.php");
        exit;
    }

    if ($password !== $confirmPassword) {
        $_SESSION['signup_error'] = "Passwords do not match.";
        header("Location: ../../pages/signup.php");
        exit;
    }

    if ($role === 'student'){
        $role = 1;
    }else if($role === 'lecturer'){
        $role = 2;
    }

    // Create a new User model instance
    $userModel = new UserModel();

    // Attempt to register the user
    $signupSuccess = $userModel->createUser($username, $role, $fname,$lname, $password);

    if ($signupSuccess) {
        $_SESSION['signup_success'] = "Account created successfully! Redirecting to Login Page...";
        header("Location: ../../client/pages/shared/SignUp.php"); // Redirect to login
        exit;
    } else {
        $_SESSION['signup_error'] = "Username already exists!";
        header("Location: ../../client/pages/shared/SignUp.php");
        exit;
    }
} else {
    // If accessed directly without POST
    header("Location: ../../client/pages/shared/SignUp.php");
    exit;
}

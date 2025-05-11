<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';

$conn = (new Database())->connect();
$userModel = new UserModel($conn);
$user = $userModel->getUserById($_SESSION['userID']);
$username = $user['firstName'] . ' ' . $user['lastName'];
?>

<link rel="stylesheet" href="../../css/Sidebar.css" />

<div class="sidebar" id="sidebar">
    <div class="btnContainer">
        <button id="toggleBtn" class="toggle-btn">
            <img src="../../assets/images/burger-menu.svg" class="menu-icon" />
        </button>
        <span class="brand">SPAMS</span>
    </div>
    <div class="logoContainer">
        <img src="../../assets/images/logo.png" class="logo"/>
    </div>
    <ul>
        <li>
            <a href="/FYP2025/SPAMS/client/pages/student/SProjectList.php">
                <img class ="icon" src="/FYP2025/SPAMS/client/assets/images/document.png" title ="Projects">
                <span>Projects</span>
            </a>
        </li>
        <li>
            <a href="/FYP2025/SPAMS/client/pages/shared/Chat.php">
                <img class ="icon" src="/FYP2025/SPAMS/client/assets/images/messageicon.png" title ="Chats">
                <span>Chats</span>
            </a>
        </li>
        <li>
            <a href="/FYP2025/SPAMS/client/pages/shared/Profile.php">
                <img class ="icon" src="/FYP2025/SPAMS/client/assets/images/account icon.png" title ="Profile">
                <span><?=htmlspecialchars($username)?></span>
            </a>
        </li>
        <li>
            <a href="/FYP2025/SPAMS/server/controllers/LogoutController.php" id="logoutBtn">
                <img class ="icon" src="/FYP2025/SPAMS/client/assets/images/logout.png" title ="Logout">
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<script src="../../js/Sidebar.js"></script>
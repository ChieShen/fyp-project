<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ChatModel.php';

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}

$userID = $_SESSION['userID'];

$conn = (new Database())->connect();
$chatModel = new ChatModel($conn);

$chats = $chatModel->getUserChatrooms($userID);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Chat</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/Chat.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="chatBox">
                <div class="titleBar">
                    <h1>Chat</h1>
                </div>

                <div class="divider">
                    <div class="chatList">
                        <ul class="chatSelection">
                            <?php foreach ($chats as $index => $chat): ?>
                                <li>
                                    <a href="#" class="chatLink<?= $index === 0 ? ' active' : '' ?>"
                                        data-chatid="<?= htmlspecialchars($chat['chatID']) ?>"
                                        data-userid="<?= htmlspecialchars($userID) ?>">
                                        <span><?= htmlspecialchars($chat['name']) ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                    </div>

                    <div class="chatHalf">
                        <div class="chatTitle">
                            <p class="title">Join a project group to start chatting!</p>
                        </div>

                        <div class="chatHistory">
                        </div>

                        <form class="messageForm">
                            <div class="sendBar">
                                <textarea id="newMessage" name="newMessage" placeholder="Aa"></textarea>
                                <button id="sendMessage" type="submit">Send</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>


        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/Chat.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
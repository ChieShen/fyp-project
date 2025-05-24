<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ChatModel.php';

session_start();

$conn = (new Database())->connect();
$chatModel = new ChatModel($conn);

$chatID = intval($_GET['chatID']);
$userID = $_SESSION['userID'];

$messages = $chatModel->getMessages($chatID);

$pinnedMessages = $chatModel->getPinnedMessages($chatID); // Get all pinned messages (change from getPinnedMessage)

$response = [
    "pinnedMessages" => [],
    "messages" => []
];

if ($pinnedMessages) {
    foreach ($pinnedMessages as $pinned) {
        $response["pinnedMessages"][] = [
            "messageID" => $pinned['messageID'],
            "content" => $pinned['content']
        ];
    }
}

foreach ($messages as $msg) {
    $response["messages"][] = [
        'messageID' => $msg['messageID'], 
        'name' => $msg['firstName'] . ' ' . $msg['lastName'],
        'content' => $msg['content'],
        'isSender' => $msg['senderID'] == $userID,
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

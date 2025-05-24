<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ChatModel.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Only GET requests allowed']);
    exit();
}

$userID = isset($_GET['userID']) ? intval($_GET['userID']) : 0;
$targetID = isset($_GET['targetID']) ? intval($_GET['targetID']) : 0;

if ($userID <= 0 || $targetID <= 0 || $userID === $targetID) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Invalid user IDs',
        'userID' => $userID,
        'targetID' => $targetID
    ]);
    exit();
}

$db = new Database();
$conn = $db->connect();
$chatModel = new ChatModel($conn);

$existingChatID = $chatModel->privateChatExists($userID, $targetID);

$chatID = $existingChatID ?: $chatModel->createPrivateChatroom($userID, $targetID);

header("Location: /FYP2025/SPAMS/client/pages/Shared/Chat.php?chatID={$chatID}&userID={$userID}");
exit();

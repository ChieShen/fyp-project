<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ChatModel.php';

session_start();

$conn = (new Database())->connect();
$chatModel = new ChatModel($conn);

$data = json_decode(file_get_contents("php://input"), true);
$chatID = intval($data['chatID']);
$userID = intval($data['userID']);
$content = trim($data['content']);

$success = false;
if (!empty($content)) {
    $success = $chatModel->sendMessage($userID, $chatID, $content);
}

echo json_encode(['success' => $success]);

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ChatModel.php';

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$action = $input['action'] ?? '';
$messageID = intval($input['messageID'] ?? 0);
$chatID = intval($input['chatID'] ?? 0);
$content = trim($input['content'] ?? '');
$userID = $_SESSION['userID'] ?? null;

if (!$userID || !$chatID || !$messageID || !$action) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$conn = (new Database())->connect();
$chatModel = new ChatModel($conn);

// Determine action
switch ($action) {
    case 'edit':
        $stmt = $conn->prepare("UPDATE message SET content = ? WHERE messageID = ? AND senderID = ?");
        $stmt->bind_param("sii", $content, $messageID, $userID);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;

    case 'delete':
        $stmt = $conn->prepare("DELETE FROM message WHERE messageID = ? AND senderID = ?");
        $stmt->bind_param("ii", $messageID, $userID);
        $success = $stmt->execute();
        echo json_encode(['success' => $success]);
        break;

    case 'pin':
        $success = $chatModel->setPinnedMessage($chatID, $messageID);
        if ($success) {
            // Return all pinned messages, not just one
            $pinnedMessages = $chatModel->getPinnedMessages($chatID);
            echo json_encode([
                'success' => true,
                'pinnedMessages' => $pinnedMessages
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to pin message']);
        }
        break;

    case 'unpin':
        $success = $chatModel->unsetPinnedMessage($chatID, $messageID);
        if ($success) {
            $pinnedMessages = $chatModel->getPinnedMessages($chatID);
            echo json_encode([
                'success' => true,
                'pinnedMessages' => $pinnedMessages
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to unpin message']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}

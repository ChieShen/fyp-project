<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
session_start();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit;
    }

    $groupID = intval($_POST['groupID']);
    $newLeaderID = intval($_POST['newLeaderID']);
    $currentUserID = $_SESSION['userID'] ?? null;

    if (!$groupID || !$newLeaderID || !$currentUserID) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $conn = (new Database())->connect();
    $groupModel = new GroupModel($conn);
    $projectModel = new ProjectModel($conn);
    $projectId = $groupModel->getProjectIdByGroupId($groupID);
    $project = $projectModel->findByProjectId($projectId);

    // Check if current user is the leader
    $leaderID = $groupModel->getLeaderId($groupID);
    if (($leaderID !== $currentUserID) && (!$project['createdBy'] == $currentUserID) ) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'You are not the group leader or the creator']);
        exit;
    }

    // Transfer the role
    $success = $groupModel->transferLeadership($groupID, $newLeaderID);
    echo json_encode([
        'status' => $success ? 'success' : 'error',
        'message' => $success ? 'Leadership transferred' : 'Transfer failed'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}


<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();

header('Content-Type: application/json');

// Validate required data
if (!isset($_POST['groupId'])) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid groupID']);
    exit;
}

$groupID = (int) $_POST['groupId'];
$userID = $_SESSION['userID'] ?? null;

//Check if user is logged in
if (!$userID) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$conn = (new Database())->connect();
$groupModel = new GroupModel($conn);
$projectModel = new ProjectModel($conn);

// Check ownership
$projectID = $groupModel->getProjectIdByGroupId($groupID);
$project = $projectModel->findByProjectId($projectID);

if (!$project || $project['createdBy'] != $userID) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized: you are not the project owner']);
    exit;
}

// Attempt deletion
$success = $groupModel->deleteGroup($groupID);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Group deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete group (check DB constraints or dependencies)']);
}

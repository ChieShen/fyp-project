<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();

header('Content-Type: application/json');

if (!isset($_POST['groupID'], $_POST['userID'])) {
    echo json_encode(['success' => false, 'message' => 'Missing groupID or userID']);
    exit;
}

$groupID = (int) $_POST['groupID'];
$userID = (int) $_POST['userID'];

$conn = (new Database())->connect();
$groupModel = new GroupModel($conn);
$projectModel = new ProjectModel($conn);
$projectID = $groupModel->getProjectIdByGroupId($groupID);
$project = $projectModel->findByProjectId($projectID);

if ($project['createdBy'] != $_SESSION['userID']) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$success = $groupModel->removeUserFromGroup($groupID, $userID);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'User removed from group']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove user (possibly leader or DB error)']);
}
?>
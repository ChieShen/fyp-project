<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
    exit;
}

$userID = (int) $_SESSION['userID'];
$data = json_decode(file_get_contents("php://input"), true);
$joinCode = strtoupper(trim($data['joinCode'] ?? ''));

if (!$joinCode || strlen($joinCode) !== 6) {
    echo json_encode(['success' => false, 'message' => 'Invalid join code.']);
    exit;
}

$db = new Database();
$conn = $db->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);

// Check if join code matches a project
$project = $projectModel->findByJoinCode($joinCode);

if ($project) {
    $projectID = (int) $project['projectID'];

    // Check if user is already in a group in this project
    if ($groupModel->isUserInProject($userID, $projectID)) {
        echo json_encode(['success' => false, 'message' => 'You have already joined this project.']);
    } else {
        echo json_encode([
            'success' => true,
            'redirectUrl' => '/FYP2025/SPAMS/client/pages/student/JoinGroup.php?id=' . $projectID
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Join code not found.']);
}

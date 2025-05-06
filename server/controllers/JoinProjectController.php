<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$joinCode = strtoupper(trim($data['joinCode'] ?? ''));

if (!$joinCode || strlen($joinCode) !== 6) {
    echo json_encode(['success' => false, 'message' => 'Invalid join code.']);
    exit;
}

$db = new Database();
$conn = $db->connect();
$model = new ProjectModel($conn);

$project = $model->findByJoinCode($joinCode);

if ($project) {
    // Optional: save user to project, etc.
    echo json_encode([
        'success' => true,
        'redirectUrl' => '/FYP2025/SPAMS/client/pages/student/JoinGroup.php?id=' . $project['projectID']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Join code not found.']);
}

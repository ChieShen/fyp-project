<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);

$type = $_GET['type'] ?? null;
$filename = basename($_GET['file'] ?? '');
$displayName = basename($_GET['name'] ?? '');

if (!$type || !$filename || !$displayName) {
    http_response_code(400);
    exit('Missing required parameters.');
}

$filePath = '';
switch ($type) {
    case 'attachment':
        $projectID = intval($_GET['projectID'] ?? 0);
        if (!$projectID) {
            http_response_code(400);
            exit('Missing project ID.');
        }
        $project = $projectModel->findByProjectId($projectID);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }
        $creatorID = $project['createdBy'];
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/attachments/{$creatorID}/{$projectID}/{$filename}";
        break;

    case 'submission':
        $projectID = intval($_GET['projectID'] ?? 0);
        $groupID = intval($_GET['groupID'] ?? 0);
        if (!$projectID || !$groupID) {
            http_response_code(400);
            exit('Missing project or group ID.');
        }
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/{$groupID}/{$filename}";
        break;

    case 'task':
        $projectID = intval($_GET['projectID'] ?? 0);
        $taskID = intval($_GET['taskID'] ?? 0);
        $userID = intval($_GET['userID'] ?? 0);
        if (!$projectID || !$taskID || !$userID) {
            http_response_code(400);
            exit('Missing project, task, or user ID.');
        }
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/{$taskID}/{$userID}/{$filename}";
        break;

    default:
        http_response_code(400);
        exit('Invalid file type.');
}

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found.'. $filePath);
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $displayName . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

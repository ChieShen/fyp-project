<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);

if (!isset($_GET['file'], $_GET['name'], $_GET['projectID'])) {
    http_response_code(400);
    exit('Invalid request');
}

$filename = basename($_GET['file']);
$displayName = basename($_GET['name']);
$projectID = intval($_GET['projectID']);

// Get the project so we can find creator ID
$project = $projectModel->findByProjectId($projectID);
if (!$project) {
    http_response_code(404);
    exit('Project not found');
}

$creatorID = $project['createdBy'];
$filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/{$creatorID}/{$projectID}/{$filename}";

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $displayName . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

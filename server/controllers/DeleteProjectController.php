<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();
function deleteDirectory(string $dir): bool {
    if (!file_exists($dir)) {
        return true; // Already gone
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            deleteDirectory($path);
        } else {
            unlink($path);
        }
    }

    return rmdir($dir);
}

$projectID = intval($_GET['projectID']);
$userID = $_SESSION['userID'] ?? null;

if (!$projectID || !$userID) {
    http_response_code(400);
    exit('Missing project ID or user session.');
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$project = $projectModel->findByProjectId($projectID);
$createdBy = $project['createdBy'];

if($createdBy != $userID){
    die('Unauthorized');
}

$attachmentDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/attachments/{$userID}/{$projectID}/";
$submissionsDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/";
$uploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/";

deleteDirectory($attachmentDir);
deleteDirectory($submissionsDir);
deleteDirectory($uploadsDir);

$deleteSuccess = $projectModel->delete($projectID);

if (!$deleteSuccess) {
    http_response_code(500);
    exit('Failed to delete the project.');
}

header("Location: /FYP2025/SPAMS/client/pages/lecturer/LProjectList.php?");
exit();

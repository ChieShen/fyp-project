<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();

//Delete Directory
function deleteDirectory(string $dir): bool {
    if (!file_exists($dir)) {
        return true; // Already gone
    }

    //If file is found not folder, delete the file
    if (!is_dir($dir)) {
        return unlink($dir);
    }

    //Delete every file/folder inside that directory
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

    //Delete that directory after it is empty inside
    return rmdir($dir);
}

$projectID = intval($_GET['projectID']);
$userID = $_SESSION['userID'] ?? null;

//Check for valid project id and valid user id
if (!$projectID || !$userID) {
    http_response_code(400);
    exit('Missing project ID or user session.');
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$project = $projectModel->findByProjectId($projectID);
$createdBy = $project['createdBy'];

//Check if project is created by user
if($createdBy != $userID){
    die('Unauthorized');
}

$attachmentDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/attachments/{$userID}/{$projectID}/";
$submissionsDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/";
$uploadsDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/";

//Delete directories
deleteDirectory($attachmentDir);
deleteDirectory($submissionsDir);
deleteDirectory($uploadsDir);

//Remove project from database
$deleteSuccess = $projectModel->delete($projectID);

if (!$deleteSuccess) {
    http_response_code(500);
    exit('Failed to delete the project.');
}

//Redirect user back to project list page
header("Location: /FYP2025/SPAMS/client/pages/lecturer/LProjectList.php?");
exit();

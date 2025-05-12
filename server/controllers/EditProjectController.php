<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();

// Reject non-POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$projectID = intval($_POST['projectID']);
$title = trim($_POST['projectName']);
$description = trim($_POST['projectDesc']);
$deadline = $_POST['deadline'];
$userID = $_SESSION['userID'];

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$project = $projectModel->findByProjectId($projectID);

// ✅ Update the project details
$updateSuccess = $projectModel->update($projectID, $title, $description, $deadline, $project['maxMem'], $project['numGroup']);
if (!$updateSuccess) {
    http_response_code(500);
    exit('Failed to update project details.');
}

// ✅ Handle removed files
if (!empty($_POST['removeFiles'])) {
    foreach ($_POST['removeFiles'] as $fileID) {
        $attachment = $projectModel->getAttachmentById($fileID);
        if ($attachment) {
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/uploads/attachments/' . $userID . '/' . $projectID . '/' . $attachment['attachName'];
            if (file_exists($filePath)) {
                unlink($filePath); // Delete physical file
            }
            $projectModel->deleteAttachment($fileID); // Remove DB entry
        }
    }
}


$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/uploads/attachments/' . $userID . '/' . $projectID . '/';

foreach ($_FILES['files']['name'] as $index => $originalName) {
    $tmpName = $_FILES['files']['tmp_name'][$index];
    $size = $_FILES['files']['size'][$index];
    $error = $_FILES['files']['error'][$index];

    if ($error === UPLOAD_ERR_OK) {
        $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);
        $destination = $uploadDir . $safeName;

        if (move_uploaded_file($tmpName, $destination)) {
            $projectModel->addAttachment($projectID, $safeName, $originalName, $userID);
        }
    }
}


// ✅ Redirect back to the project view page
header("Location: /FYP2025/SPAMS/client/pages/lecturer/LProjectGroups.php?projectID=$projectID");
exit();

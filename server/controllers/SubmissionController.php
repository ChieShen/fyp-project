<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}

$userID = $_SESSION['userID'];
$projectID = intval($_POST['projectID']);
$groupID = intval($_POST['groupID']);

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die("File upload error.");
}

// Safe file naming logic
$originalName = basename($_FILES['file']['name']);
$safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/{$groupID}";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$targetPath = $uploadDir . '/' . $safeName;

if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    $conn = (new Database())->connect();
    $groupModel = new GroupModel($conn);
    $groupModel->setSubmitted($groupID,true);

    $relativePath = "/FYP2025/SPAMS/uploads/submissions/{$projectID}/{$groupID}/{$safeName}";
    $groupModel->saveSubmission($projectID, $groupID, $relativePath, $safeName, $originalName);

    header("Location: /FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=$projectID&groupID=$groupID");
    exit();
} else {
    die("Failed to move uploaded file.");
}

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}

$userID = $_SESSION['userID'];
$taskID = intval($_POST['taskID']);
$projectID = intval($_POST['projectID']);
$groupID = intval($_POST['groupID']);

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    die("File upload error.");
}

// Safe file naming logic
$originalName = basename($_FILES['file']['name']);
$safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/$groupID/{$taskID}/{$userID}";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$targetPath = $uploadDir . '/' . $safeName;

//Update task status, save file, and update database
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    $conn = (new Database())->connect();
    $taskModel = new TaskModel($conn);
    $taskModel->uploadTaskFile($taskID, $userID, $safeName, $originalName);
    $taskModel->updateTaskStatus($taskID, 1);
    header("Location: /FYP2025/SPAMS/client/pages/student/TaskDetails.php?taskID=$taskID&projectID=$projectID&groupID=$groupID");
    exit();
} else {
    die("Failed to move uploaded file.");
}

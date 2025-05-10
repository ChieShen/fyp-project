<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';

session_start();

// Redirect if not logged in
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}

$conn = (new Database())->connect();
$taskModel = new TaskModel($conn);
$groupModel = new GroupModel($conn);

$userID = $_SESSION['userID'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $projectID = intval($_POST['projectID'] ?? 0);
    $groupID = intval($_POST['groupID'] ?? 0);
    $taskName = trim($_POST['taskName'] ?? '');
    $taskDesc = trim($_POST['taskDesc'] ?? '');
    $status = intval($_POST['status'] ?? 0);
    $contributors = $_POST['contributors'] ?? [];

    if (!$groupModel->isUserInProject($userID, $projectID)) {
        header("Location: /FYP2025/SPAMS/client/pages/student/SProjectList.php");
        exit();
    }

    // CREATE TASK
    if ($action === 'create') {
        $taskID = $taskModel->createTask($projectID, $groupID, $status, $taskName, $taskDesc);
        if ($taskID && !empty($contributors)) {
            $taskModel->addContributors($taskID, $contributors);
        }
        header("Location: /FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=$projectID&groupID=$groupID");
        exit();
    }

    // EDIT TASK
    if ($action === 'edit') {
        $taskID = intval($_POST['taskID'] ?? 0);
        $taskModel->updateTask($taskID, $status, $taskName, $taskDesc);
        $taskModel->removeAllContributors($taskID);
        if (!empty($contributors)) {
            $taskModel->addContributors($taskID, $contributors);
        }
        header("Location: /FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=$projectID&groupID=$groupID");
        exit();
    }

    // DELETE TASK
    if ($action === 'delete') {
        $taskID = intval($_POST['taskID'] ?? 0);
        $taskModel->deleteTask($taskID);
        header("Location: /FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=$projectID&groupID=$groupID");
        exit();
    }

    // UPDATE TASK STATUS
    if (isset($_POST['newStatus']) && isset($_POST['taskID'])) {
        $taskID = intval($_POST['taskID']);
        $newStatus = intval($_POST['newStatus']);
        $projectID = intval($_POST['projectID'] ?? 0);
        $groupID = intval($_POST['groupID'] ?? 0);

        // Verify that user is in the project
        if ($groupModel->isUserInProject($userID, $projectID)) {
            $taskModel->updateTaskStatus($taskID, $newStatus);
        }

        header("Location: /FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=$projectID&groupID=$groupID");
        exit();
    }
}
?>
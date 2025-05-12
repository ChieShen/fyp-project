<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';

session_start();

if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
} elseif (!isset($_GET['taskID'])) {
    header("Location: /FYP2025/SPAMS/Client/pages/student/SProjectList.php");
    exit();
}

$taskId = $_GET['taskID'];

$conn = (new Database())->connect();
$taskModel = new TaskModel($conn);
$groupModel = new GroupModel($conn);

$task = $taskModel->getTaskById($taskId);
$taskContributors = $taskModel->getContributorsByTask($taskId);

$groupId = $task['groupID'];
$groupMembers = $groupModel->getMembersByGroup($groupId);

$projectId = $task['projectID'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/EditTask.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="createBox">
            <div class="titleBar">
                <h1>Edit Task</h1>
                <button id="deleteBtn" data-taskid="<?= $taskId ?>"
                    data-taskname="<?= htmlspecialchars($task['taskName']) ?>"
                    data-groupid="<?= $groupId ?>"
                    data-projectid="<?= $projectId ?>">Delete Task</button>
            </div>

            <form action="/FYP2025/SPAMS/server/controllers/TaskController.php" method="post"
                onsubmit="validateForm(event)" class="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="projectID" value="<?= htmlspecialchars($projectId) ?>">
                <input type="hidden" name="groupID" value="<?= htmlspecialchars($groupId) ?>">
                <input type="hidden" name="taskID" value="<?= htmlspecialchars($taskId) ?>">
                <div class="contentBox">
                    <label for="taskName">Task Name</label>
                    <p id="tNameError" style="color: red; margin-left:5%;"></p>
                    <input type="text" id="taskName" name="taskName" placeholder="Task Name"
                        value="<?= htmlspecialchars($task['taskName']) ?>"><br><br>

                    <label for="taskDesc">Task Description</label>
                    <p id="tDescError" style="color: red; margin-left:5%;"></p>
                    <textarea id="taskDesc" name="taskDesc"
                        placeholder="Brief Description"><?= htmlspecialchars($task['description']) ?></textarea><br><br>
                </div>

                <label>Contributor(s)</label>
                <p id="contribError" style="color: red; margin-left:5%;"></p>
                <div class="contributorList">
                    <?php foreach ($groupMembers as $member): ?>
                        <?php
                        $memberID = $member['userID'];
                        $fullName = htmlspecialchars($member['firstName'] . ' ' . $member['lastName']);
                        $isChecked = in_array($memberID, array_column($taskContributors, 'userID'));
                        ?>
                        <div class="contributorRow">
                            <label><?= $fullName ?></label>
                            <input type="checkbox" name="contributors[]" value="<?= $memberID ?>" <?= $isChecked ? 'checked' : '' ?>>
                            <div class="spacer"></div>
                        </div>
                    <?php endforeach; ?>
                </div>


                <div class="buttons">
                    <button id="cancel">
                        <a href="/FYP2025/SPAMS/client/pages/student/TaskDetails.php?projectID=<?= urlencode($projectId) ?>&groupID=<?= urlencode($groupId) ?>&taskID=<?= urlencode($taskId) ?>"
                            class="cancelLink">
                            Cancel
                        </a>
                    </button>
                    <button type="submit" id="create">Save</button>
                </div>

            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/EditTask.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
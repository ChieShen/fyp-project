<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
} elseif (!isset($_GET['projectID']) || !isset($_GET['groupID'])) {
    header("Location: /FYP2025/SPAMS/Client/pages/stdent/SProjectList.php");
    exit();
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);
$userID = $_SESSION['userID'];

$projectId = intval($_GET['projectID']);
$project = $projectModel->findByProjectId($projectId);

$groupId = intval($_GET['groupID']);
$group = $groupModel->getGroupById($groupId);

if (!$project || !($groupModel->isUserInProject($userID, $projectId))) {
    header("Location: /FYP2025/SPAMS/Client/Pages/student/SProjectList.php");
    exit();
}

$creator = $userModel->getUserById($project['createdBy']);
$attachments = $projectModel->getAttachmentsByProjectId($projectId);
$groupMembers = $groupModel->getMembersByGroup($groupId);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Task</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/CreateTask.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="createBox">
            <div class="titleBar">
                <h1>New Task</h1>
            </div>

            <form action="/FYP2025/SPAMS/server/controllers/TaskController.php" method="post"
                onsubmit="validateForm(event)">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="projectID" value="<?= htmlspecialchars($projectId) ?>">
                <input type="hidden" name="groupID" value="<?= htmlspecialchars($groupId) ?>">
                <div class="contentBox">
                    <label for="taskName">Task Name</label>
                    <p id="tNameError" style="color: red; margin-left:5%;"></p>
                    <input type="text" id="taskName" name="taskName" placeholder="Task Name"><br><br>

                    <label for="taskDesc">Task Description</label>
                    <p id="tDescError" style="color: red; margin-left:5%;"></p>
                    <textarea id="taskDesc" name="taskDesc" placeholder="Brief Description"></textarea><br><br>
                </div>

                <label>Contributor(s)</label>
                <p id="contribError" style="color: red; margin-left:5%;"></p>
                <div class="contributorList">
                    <?php foreach ($groupMembers as $member): ?>
                        <div class="contributorRow">
                            <label><?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?></label>
                            <input type="checkbox" name="contributors[]" value="<?= htmlspecialchars($member['userID']) ?>">
                            <div class="spacer"></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="buttons">
                    <button type="button" id="cancel">
                        <a
                            href="/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=<?= urlencode($projectId) ?>&groupID=<?= urlencode($groupId) ?>">
                            Cancel
                        </a>
                    </button>
                    <button type="submit" id="create">Create</button>
                </div>

            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/CreateTask.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
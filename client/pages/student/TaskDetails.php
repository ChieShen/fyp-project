<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/NavBar.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
} elseif (!isset($_GET['projectID']) || !isset($_GET['groupID']) || !isset($_GET['taskID'])) {
    header("Location: /FYP2025/SPAMS/Client/pages/student/SProjectList.php");
    exit();
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);
$taskModel = new TaskModel($conn);
$userID = $_SESSION['userID'];

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);
$taskModel = new TaskModel($conn);
$userID = $_SESSION['userID'];

$user = $userModel->getUserById($userID);

$projectId = intval($_GET['projectID']);
$project = $projectModel->findByProjectId($projectId);

$groupId = intval($_GET['groupID']);
$group = $groupModel->getGroupById($groupId);

$taskId = intval($_GET['taskID']);
$task = $taskModel->getTaskById($taskId);

if (!$projectModel->isUserInProject($userID, $projectId) && $project['createdBy'] != $userID) {
    header("Location: /FYP2025/SPAMS/Client/Pages/student/SProjectList.php");
    exit();
}

$leaderID = $groupModel->getLeaderId($groupId);
$contributors = $taskModel->getContributorsByTask($taskId);
$uploadedFiles = $taskModel->getUploadedFilesByTask($taskId);

$isContributor = false;
foreach ($contributors as $contributor) {
    if ($contributor['userID'] == $userID) {
        $isContributor = true;
        break;
    }
}
$isSubmitted = $groupModel->getSubmitted($groupId);
$isTaskDone = $taskModel->isTaskDone($taskId);

if ($user['roleID'] == 2) {
    $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/FYP2025/SPAMS/client/pages/lecturer/LProjectList.php'],
        ['label' => $project['title'], 'url' => "/FYP2025/SPAMS/client/pages/lecturer/LProjectGroups.php?projectID={$projectId}"],
        ['label' => $group['groupName'], 'url' => "/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID={$projectId}&groupID={$groupId}"],
        ['label' => $task['taskName'], 'url' => '']
    ];
} else {
    $breadcrumbs = [
        ['label' => 'Projects', 'url' => '/FYP2025/SPAMS/client/pages/student/SProjectList.php'],
        ['label' => $project['title'], 'url' => "/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID={$projectId}&groupID={$groupId}"],
        ['label' => $task['taskName'], 'url' => '']
    ];
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Task Details</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/TaskDetails.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <?php renderBreadcrumb($breadcrumbs) ?>
            <div class="taskDetails">
                <div class="titleBar">
                    <h1><?php echo $task['taskName'] ?></h1>
                    <?php if ($leaderID === $userID && !$isSubmitted): ?>
                        <button id="editBtn">
                            <a href="/FYP2025/SPAMS/client/pages/student/EditTask.php?taskID=<?= urlencode($taskId) ?>"
                                class="editLink">
                                Edit Task
                            </a>
                        </button>
                    <?php endif; ?>
                </div><br>

                <p class="label">Task Description:</p>
                <p class="details">
                    <?= htmlspecialchars($task['description']) ?>
                </p><br><br>

                <p class="label">Contributors</p>
                <?php if (!empty($contributors)): ?>
                    <?php foreach ($contributors as $contributor):
                        $user = $userModel->getUserById($contributor['userID']);
                        if ($user): ?>
                            <a href="" class="contributor">
                                <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?> (Click to Message)
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="details">No contributors assigned.</p>
                <?php endif; ?>
            </div>

            <div class="uploads">
                <div class="titleBar">
                    <h1>Uploads</h1>
                </div>

                <div class="uploadsTable">
                    <div class="columnNameRow">
                        <div class="columnName">File Name</div>
                        <div class="columnName">Uploaded By</div>
                        <div class="columnName">Time Uploaded</div>
                        <div class="columnName">Action</div>
                    </div>
                    <?php if (!empty($uploadedFiles)): ?>
                        <?php foreach ($uploadedFiles as $file): ?>
                            <div class="dataRow">
                                <div class="data"><?= htmlspecialchars($file['displayName']) ?></div>
                                <div class="data"><?= htmlspecialchars($file['firstName'] . ' ' . $file['lastName']) ?></div>
                                <div class="data"><?= date('d/m/Y H:i', strtotime($file['uploadedAt'])) ?></div>
                                <div class="data">
                                    <a
                                        href="/FYP2025/SPAMS/server/controllers/DownloadController.php?type=task&projectID=<?= urlencode($projectId) ?>&taskID=<?= urlencode($taskId) ?>&userID=<?= urlencode($file['userID']) ?>&file=<?= urlencode($file['fileName']) ?>&name=<?= urlencode($file['displayName']) ?>">
                                        <button class="download">Download</button>
                                    </a>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dataRow">No files uploded yet</div>
                    <?php endif; ?>

                </div>

                <?php if ($isContributor && !$isSubmitted): ?>
                    <?php if (!$isTaskDone): ?>
                        <form action="/FYP2025/SPAMS/server/controllers/TaskUploadController.php" method="post"
                            enctype="multipart/form-data" class="uploadForm">
                            <input type="hidden" name="action" value="upload">
                            <input type="hidden" name="projectID" value="<?= htmlspecialchars($projectId) ?>">
                            <input type="hidden" name="groupID" value="<?= htmlspecialchars($groupId) ?>">
                            <input type="hidden" name="taskID" value="<?= htmlspecialchars($taskId) ?>">

                            <div class="uploadBar">
                                <input type="file" id="fileInput" name="file" style="display: none;" />
                                <label for="fileInput">
                                    <img class="icon" src="/FYP2025/SPAMS/client/assets/images/attach file.png">
                                </label>
                                <span id="fileName">No file selected</span>
                                <button id="uploadFile" type="submit">Upload</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <form class="statusUpdate" method="post" action="/FYP2025/SPAMS/server/controllers/TaskController.php">
                        <input type="hidden" name="projectID" value="<?= htmlspecialchars($projectId) ?>">
                        <input type="hidden" name="groupID" value="<?= htmlspecialchars($groupId) ?>">
                        <input type="hidden" name="taskID" value="<?= htmlspecialchars($taskId) ?>">
                        <input type="hidden" name="currentStatus" value="<?= htmlspecialchars($task['status']) ?>">

                        <?php if ($task['status'] == 0 || $task['status'] == 1): ?>
                            <button type="submit" name="newStatus" value="2" id="markDone">Mark As Done</button>
                        <?php elseif ($task['status'] == 2): ?>
                            <button type="submit" name="newStatus" value="1" id="markUndone">Update Task</button>
                        <?php endif; ?>
                    </form>

                <?php endif; ?>
            </div>

        </div>

        <script src="/FYP2025/SPAMS/client/js/TaskDetails.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
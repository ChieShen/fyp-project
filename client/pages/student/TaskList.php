<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}
elseif(!isset($_GET['projectID']) || !(isset($_GET['groupID']))){
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

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Task List</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/TaskList.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="projectDetails">
                <div class="titleBar">
                    <h1><?php echo $project['title'] ?></h1>
                    <button id="transferBtn">Transfer Role</button>
                </div><br>

                <p class="label">Project Description:</p>
                <p class="details">
                    <?= htmlspecialchars($project['description']) ?>
                </p><br><br>

                <p class="label">Created By:</p>
                <a href="" class="creator"><?= htmlspecialchars($creator['username'])?></a><br><br>

                <p class="label">Attached File(s)</p>
                <?php if (!empty($attachments)): ?>
                    <?php foreach ($attachments as $file): ?>
                        <a href="/FYP2025/SPAMS/server/controllers/DownloadController.php?file=<?= urlencode($file['attachName']) ?>&name=<?= urlencode($file['displayName']) ?>&projectID=<?= $projectId ?>"
                            class="files">
                            <?= htmlspecialchars($file['displayName']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="details">No files attached.</p>
                <?php endif; ?>

            </div>

            <div class="groups">
                <div class="titleBar">
                    <h1>Tasks</h1>
                    <button id="addBtn">
                        <a href="/FYP2025/SPAMS/client/pages/student/CreateTask.php?groupID=<?= urlencode($groupId) ?>">
                            Add Task
                        </a>
                    </button>
                </div>

                <div class="memberTable">
                    <div class="columnNameRow">
                        <div class="columnName">Task Name</div>
                        <div class="columnName">Assigned To</div>
                        <div class="columnName">Last Updated</div>
                        <div class="columnName">Status</div>
                        <div class="columnName">Action</div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Task Name</div>
                        <div class="data">Student Name</div>
                        <div class="data">20/12/2025</div>
                        <div class="data ongoing">Ongoing</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Task Name</div>
                        <div class="data">Student Name</div>
                        <div class="data">10/12/2025</div>
                        <div class="data done">Done</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Task Name</div>
                        <div class="data">Student Name</div>
                        <div class="data">No data</div>
                        <div class="data notStarted">Not Started</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                </div>
                <button id="downloadAll">Download All Lastest Files</button>
            </div>

            <form class="submission">
                <h1>Submission</h1>
                <div class="submissionBar">
                    <input type="file" id="fileInput" style="display: none;" />

                    <label for="fileInput">
                        <img class="icon" src="/FYP2025/SPAMS/client/assets/images/attach file.png">
                    </label>

                    <span id="fileName">No file selected</span>

                    <button id="submission" type="submit">Submit</button>
                </div>
            </form>

        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/TaskList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

session_start();
if (!isset($_SESSION['userID']) || !isset($_GET['id'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);

$projectId = intval($_GET['id']);
$project = $projectModel->findByProjectId($projectId);

if (!$project || ($project['createdBy'] != $_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/Pages/lecturer/LProjectList.php");
    exit();
}

$groups = $groupModel->getGroupsByProject($projectId);
$creator = $userModel->getUserById($project['createdBy']);
$attachments = $projectModel->getAttachmentsByProjectId($projectId);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Project</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/LProjectGroups.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="projectDetails">
                <div class="titleBar">
                    <h1><?php echo $project['title'] ?></h1>
                    <button id="editBtn">Edit</button>
                </div><br>

                <p class="label">Project Description:</p>
                <p class="details">
                    <?= htmlspecialchars($project['description']) ?>
                </p><br><br>

                <p class="label">Created By:</p>
                <p class="details"><?= htmlspecialchars($creator['username']) ?></p><br><br>

                <p class="label">Join Code:</p>
                <p class="details"><?= htmlspecialchars($project['joinCode']) ?></p><br><br>

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
                    <h1>Group 1</h1>
                    <div class="progress-container">
                        <div class="progress-bar" id="progressBar" data-progress="100"></div>
                    </div>
                    <button class="deleteBtn">Delete</button>
                </div>

                <div class="memberTable">
                    <div class="columnNameRow">
                        <div class="columnName">Name</div>
                        <div class="columnName">Assigned Parts</div>
                        <div class="columnName">Completed Parts</div>
                        <div class="columnName">Role</div>
                        <div class="columnName">Action</div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Student Name</div>
                        <div class="data">10</div>
                        <div class="data">1</div>
                        <div class="data" id="leaderRole">Leader</div>
                        <div class="data">
                            <button class="transferBtn" data-group-id="group1"
                                data-current-leader="stu1">Transfer</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Student Name</div>
                        <div class="data">10</div>
                        <div class="data">1</div>
                        <div class="data" id="memberRole">Member</div>
                        <div class="data">
                            <button class="removeBtn">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="groupSubmission">
                    <p class="label">Submission:</p>
                    <div class="submissionBar">
                        <p class="files">Assignment2.pdf</p>
                        <button class="download">Download</button>
                    </div>
                </div>

            </div>

            <button id="downloadAll">Download All Submissions</button>

        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/LProjectGroups.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
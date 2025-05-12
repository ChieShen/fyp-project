<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/index.php");
    exit();
} elseif (!isset($_GET['projectID']) || !isset($_GET['groupID'])) {
    header("Location: /FYP2025/SPAMS/Client/pages/student/SProjectList.php");
    exit();
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);
$taskModel = new TaskModel($conn);
$userID = $_SESSION['userID'];

$projectId = intval($_GET['projectID']);
$project = $projectModel->findByProjectId($projectId);

$groupId = intval($_GET['groupID']);
$group = $groupModel->getGroupById($groupId);

if (!$groupModel->isUserInProject($userID, $projectId) && $project['createdBy'] != $userID ) {
    header("Location: /FYP2025/SPAMS/client/Pages/student/SProjectList.php");
    exit();
}

$creator = $userModel->getUserById($project['createdBy']);
$attachments = $projectModel->getAttachmentsByProjectId($projectId);
$leaderID = $groupModel->getLeaderId($groupId);
$taskArray = $taskModel->getTasksByProjectAndGroup($projectId, $groupId);
$isSubmitted = $groupModel->getSubmitted($groupId);
$members = $groupModel->getMembersByGroup($groupId);
$showBtn = false;
$downloadAll = false;

if (($leaderID === $userID) && !$isSubmitted) {
    $showBtn = true;
}
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
                    <?php if ($showBtn): ?>
                        <button id="transferBtn">Transfer Role</button>
                    <?php endif; ?>
                </div><br>

                <p class="label">Project Description:</p>
                <p class="details">
                    <?= htmlspecialchars($project['description']) ?>
                </p><br><br>

                <p class="label">Created By:</p>
                <a href="" class="creator"><?= htmlspecialchars($creator['username']) ?></a><br><br>

                <p class="label">Attached File(s):</p>
                <?php if (!empty($attachments)): ?>
                    <?php foreach ($attachments as $file): ?>
                        <a href="/FYP2025/SPAMS/server/controllers/DownloadController.php?type=attachment&file=<?= urlencode($file['attachName']) ?>&name=<?= urlencode($file['displayName']) ?>&projectID=<?= $projectId ?>"
                            class="files">
                            <?= htmlspecialchars($file['displayName']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="details">No files attached.</p>
                <?php endif; ?>
                <br><br>

                <p class="label"><?= htmlspecialchars($group['groupName']) ?>:</p>
                <?php foreach ($members as $member): ?>
                    <p class="details">
                        <?php if ($member['userID'] == $leaderID): ?>
                            <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?> (Leader)
                        <?php else: ?>
                            <?= htmlspecialchars($member['firstName'] . ' ' . $member['lastName']) ?>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>

            </div>

            <div class="groups">
                <div class="titleBar">
                    <h1>Tasks</h1>
                    <?php if ($showBtn): ?>
                        <button id="addBtn">
                            <a
                                href="/FYP2025/SPAMS/client/pages/student/CreateTask.php?projectID=<?= urlencode($projectId) ?>&groupID=<?= urlencode($groupId) ?>">
                                Add Task
                            </a>
                        </button>
                    <?php endif; ?>
                </div>

                <div class="memberTable">
                    <div class="columnNameRow">
                        <div class="columnName">Task Name</div>
                        <div class="columnName">Assigned To</div>
                        <div class="columnName">Last Updated</div>
                        <div class="columnName">Status</div>
                        <div class="columnName">Action</div>
                    </div>

                    <?php if (!empty($taskArray)): ?>
                        <?php foreach ($taskArray as $task):
                            $statusClass = '';
                            $downloadShow = false;
                            switch ($task['status']) {
                                case 0:
                                    $statusText = 'Not Started';
                                    $statusClass = 'notStarted';
                                    break;
                                case 1:
                                    $statusText = 'Ongoing';
                                    $statusClass = 'ongoing';
                                    $downloadShow = true;
                                    $downloadAll = true;
                                    break;
                                case 2:
                                    $statusText = 'Done';
                                    $statusClass = 'done';
                                    $downloadShow = true;
                                    $downloadAll = true;
                                    break;
                                default:
                                    $statusText = 'Unknown';
                                    $statusClass = '';
                                    break;
                            }

                            $contributors = $taskModel->getContributorsByTask($task['taskID']);
                            $assignedTo = 'No Contributor';

                            if (!empty($contributors)) {
                                if (count($contributors) === 1) {
                                    $userInfo = $userModel->getUserById($contributors[0]['userID']);
                                    $assignedTo = htmlspecialchars($userInfo['firstName'] . ' ' . $userInfo['lastName']);
                                } else {
                                    $assignedTo = count($contributors) . ' Contributors';
                                }
                            }
                            ?>

                            <div class="dataRow">
                                <a class="dataRowLink"
                                    href="/FYP2025/SPAMS/client/pages/student/TaskDetails.php?projectID=<?= urlencode($projectId) ?>&groupID=<?= urlencode($groupId) ?>&taskID=<?= urlencode($task['taskID']) ?>">
                                    <div class="data"><?= htmlspecialchars($task['taskName']) ?></div>
                                    <div class="data"><?= $assignedTo ?></div>
                                    <div class="data">
                                        <?= $task['lastUpdated'] ? date("d/m/Y", strtotime($task['lastUpdated'])) : 'No data' ?>
                                    </div>
                                    <div class="data <?= $statusClass ?>"><?= $statusText ?></div>
                                </a>
                                <?php if ($downloadShow): ?>
                                    <div class="downloadColumn">
                                        <button class="download">
                                            <a class="downloadLink"
                                                href="/FYP2025/SPAMS/server/controllers/DownloadController.php?type=latestUploadsByTask&projectID=<?= urlencode($projectId) ?>&taskID=<?= urlencode($task['taskID']) ?>"
                                                download>
                                                Download
                                            </a>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="dataRow">
                            <div class="data" colspan="5">No tasks found.</div>
                        </div>
                    <?php endif; ?>

                </div>

                <?php if ($downloadAll): ?>
                    <button id="downloadAll">
                        <a href="/FYP2025/SPAMS/server/controllers/DownloadController.php?type=allLatestTaskUploads&projectID=<?= urlencode($projectId) ?>&groupID=<?= urlencode($groupId) ?>"
                            class="downloadLink" download>
                            Download All Latest Files
                        </a>
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($showBtn): ?>
                <form action="/FYP2025/SPAMS/server/controllers/SubmissionController.php" method="post"
                    enctype="multipart/form-data" class="submission">
                    <h1>Submission</h1>
                    <input type="hidden" name="projectID" value="<?= htmlspecialchars($projectId) ?>">
                    <input type="hidden" name="groupID" value="<?= htmlspecialchars($groupId) ?>">
                    <div class="submissionBar">
                        <input type="file" id="fileInput" name="file" style="display: none;" />

                        <label for="fileInput">
                            <img class="icon" src="/FYP2025/SPAMS/client/assets/images/attach file.png">
                        </label>

                        <span id="fileName">No file selected</span>

                        <button id="submission" type="submit">Submit</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/TaskList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
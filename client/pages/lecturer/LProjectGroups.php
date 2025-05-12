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
}

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);
$taskModel = new TaskModel($conn);

$projectId = intval($_GET['projectID']);
$project = $projectModel->findByProjectId($projectId);

if (!$project || ($project['createdBy'] != $_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/Client/Pages/lecturer/LProjectList.php");
    exit();
}

$groups = $groupModel->getGroupsByProject($projectId);
$creator = $userModel->getUserById($project['createdBy']);
$attachments = $projectModel->getAttachmentsByProjectId($projectId);
$hasSubmission = false;

$deadlineRaw = $project['deadline'];

$deadline = new DateTime($deadlineRaw);
$formattedDeadline = $deadline->format('Y-m-d h:i A');
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
                    <button id="editBtn">
                        <a href="/FYP2025/SPAMS/client/pages/lecturer/EditProject.php?projectID=<?= urldecode($projectId) ?>"
                            class="editLink">
                            Edit
                        </a>
                    </button>
                </div><br>

                <p class="label">Project Description:</p>
                <p class="details">
                    <?= htmlspecialchars($project['description']) ?>
                </p><br><br>

                <p class="label">Created By:</p>
                <p class="details"><?= htmlspecialchars($creator['username']) ?></p><br><br>

                <p class="label">Deadline:</p>
                <p class="details"><?= htmlspecialchars($formattedDeadline) ?></p><br><br>

                <p class="label">Join Code:</p>
                <p class="details"><?= htmlspecialchars($project['joinCode']) ?></p><br><br>

                <p class="label">Attached File(s)</p>
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

            </div>

            <?php foreach ($groups as $grp): ?>
                <?php
                $progress = $groupModel->calculateProjectProgress($projectId, $grp['groupID']);
                $members = $groupModel->getMembersByGroup($grp['groupID']);
                $submitted = $groupModel->getSubmitted($grp['groupID']);
                $submission = $groupModel->getSubmissionByGroup($projectId, $grp['groupID']);
                $leaderID = $groupModel->getLeaderId($grp['groupID']);
                if ($submission)
                    $hasSubmission = true;
                ?>

                <div class="groups">
                    <div class="titleBar">
                        <a href="/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=<?= urlencode($projectId) ?>&groupID=<?= urlencode($grp['groupID']) ?>"
                            class="groupLink">
                            <h1><?= htmlspecialchars($grp['groupName']) ?></h1>
                        </a>
                        <div class="progress-container">
                            <div class="progress-bar" id="progressBar" data-progress="<?= $progress ?>"></div>
                        </div>
                        <button class="deleteBtn" data-group-id="<?= $grp['groupID'] ?>"
                            data-grpname="<?= htmlspecialchars($grp['groupName']) ?>">Delete</button>
                    </div>

                    <div class="memberTable">
                        <div class="columnNameRow">
                            <div class="columnName">Name</div>
                            <div class="columnName">Assigned Parts</div>
                            <div class="columnName">Completed Parts</div>
                            <div class="columnName">Role</div>
                            <div class="columnName">Action</div>
                        </div>
                        <?php foreach ($members as $member): ?>
                            <?php
                            $name = $member['firstName'] . ' ' . $member['lastName'];
                            $assignedTask = $taskModel->countAssignedTasksByUserAndGroup($member['userID'], $projectId, $grp['groupID']);
                            $completedTask = $taskModel->countCompletedTasksByUserAndGroup($member['userID'], $projectId, $grp['groupID']);
                            ?>
                            <div class="dataRow">
                                <div class="data"><?= htmlspecialchars($name) ?></div>
                                <div class="data"><?= $assignedTask ?></div>
                                <div class="data"><?= $completedTask ?></div>
                                <div class="data <?= ($member['userID'] == $leaderID) ? 'leader' : 'member' ?>">
                                    <?= ($member['userID'] == $leaderID) ? 'Leader' : 'Member' ?>
                                </div>
                                <div class="data">
                                    <?php if ($member['userID'] == $leaderID): ?>
                                        <?php
                                        // Prepare member list for this group
                                        $membersForJS = array_filter($members, function ($m) use ($leaderID) {
                                            return $m['userID'] != $leaderID; //exclude current leader
                                        });
                                        $membersData = array_values(array_map(function ($m) {
                                            return [
                                                'value' => $m['userID'],
                                                'label' => $m['firstName'] . ' ' . $m['lastName']
                                            ];
                                        }, $membersForJS));
                                        ?>
                                        <button class="transferBtn" data-group-id="<?= $grp['groupID'] ?>"
                                            data-current-leader="<?= $member['userID'] ?>"
                                            data-members='<?= htmlspecialchars(json_encode($membersData), ENT_QUOTES, 'UTF-8') ?>'>
                                            Transfer
                                        </button>
                                    <?php else: ?>
                                        <button class="removeBtn" data-user-id="<?= $member['userID'] ?>"
                                            data-group-id="<?= $grp['groupID'] ?>" data-username="<?= htmlspecialchars($name) ?>"
                                            data-grpname="<?= htmlspecialchars($grp['groupName']) ?>">Remove</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="groupSubmission">
                        <p class="label">Submission:</p>
                        <div class="submissionBar">
                            <?php if ($submission): ?>
                                <?php
                                $fileName = htmlspecialchars($submission['submissionName']);
                                $displayName = htmlspecialchars($submission['displayName']);
                                ?>
                                <p class="files"><?= htmlspecialchars($submission['displayName']) ?></p>
                                <button class="download">
                                    <a class="downloadLink"
                                        href="/FYP2025/SPAMS/server/controllers/DownloadController.php?type=submission&file=<?= urlencode($fileName) ?>&name=<?= urlencode($displayName) ?>&projectID=<?= $projectId ?>&groupID=<?= $grp['groupID'] ?>">
                                        Download
                                    </a>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($hasSubmission): ?>
                <button id="downloadAll">
                    <a href="/FYP2025/SPAMS/server/controllers/DownloadController.php?type=allSubmissions&projectID=<?= $projectId ?>"
                        class="downloadLink">
                        Download All Submissions
                    </a>
                </button>
            <?php endif; ?>

        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/LProjectGroups.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
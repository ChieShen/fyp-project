<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/client/index.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$groupModel = new GroupModel($conn);
$userModel = new UserModel($conn);
$userID = $_SESSION['userID'];

$user = $userModel->getUserById($userID);
if ($user['roleID'] == 2){
    header("Location: /FYP2025/SPAMS/client/pages/lecturer/LProjectList.php");
}

$projects = $groupModel->getUserGroupsWithProjects($userID);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Project List</title>
    <link rel="stylesheet" href="../../css/SProjectList.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="listBox">
            <div class="titleBar">
                <h1>Projects</h1>
                <button id="joinProject">Join Project</button>
            </div>

            <div class="listTable">
                <div class="columnNameRow">
                    <div class="columnName">Project Name</div>
                    <div class="columnName">Group Name</div>
                    <div class="columnName">Deadline</div>
                    <div class="columnName">Created By</div>
                    <div class="columnName">Progress</div>
                    <div class="columnName">Status</div>
                </div>

                <?php if (empty($projects)): ?>
                    <div class="dataRow">
                        <div class="data" colspan="5">No joined projects found.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $proj): ?>
                        <?php 
                            $creator = $userModel->getUserById($proj['createdBy']);
                            $submitted = ($proj['submitted'] == '1') ? "Submitted" : "Not Submitted";
                            $progress = $groupModel->calculateProjectProgress($proj['projectID'], $proj['groupID']);
                        ?>
                        <a href="/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=<?= urlencode($proj['projectID']) ?>&groupID=<?= urlencode($proj['groupID']) ?>"
                            class="dataRowLink">
                            <div class="dataRow">
                                <div class="data"><?= htmlspecialchars($proj['title']) ?></div>
                                <div class="data"><?= htmlspecialchars($proj['groupName']) ?></div>
                                <div class="data"><?= htmlspecialchars($proj['deadline']) ?></div>
                                <div class="data"><?= htmlspecialchars($creator['username']) ?></div>
                                <div class="data">
                                    <div class="progress-container">
                                        <div class="progress-bar" data-progress="<?= $progress ?>"></div>
                                    </div>
                                </div>
                                <div class="data"><?= htmlspecialchars($submitted) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>

                <?php endif; ?>


            </div>
        </div>
    </div>
    <script src="../../js/SProjectList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
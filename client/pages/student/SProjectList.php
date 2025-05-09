<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';

if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/client/index.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$groupModel = new GroupModel($conn);
$userID = $_SESSION['userID'];
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
                </div>

                <?php if (empty($projects)): ?>
                    <div class="dataRow">
                        <div class="data" colspan="5">No joined projects found.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $proj): ?>
                        <a href="/FYP2025/SPAMS/client/pages/student/TaskList.php?projectID=<?= urlencode($proj['projectID']) ?>&groupID=<?= urlencode($proj['groupID']) ?>"
                            class="dataRowLink">
                            <div class="dataRow">
                                <div class="data"><?= htmlspecialchars($proj['title']) ?></div>
                                <div class="data"><?= htmlspecialchars($proj['groupName']) ?></div>
                                <div class="data"><?= htmlspecialchars($proj['deadline']) ?></div>
                                <div class="data"><?= htmlspecialchars($proj['createdBy']) ?></div>
                                <div class="data">
                                    <div class="progress-container">
                                        <div class="progress-bar" data-progress="<?= $proj['progress'] ?>"></div>
                                    </div>
                                </div>
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
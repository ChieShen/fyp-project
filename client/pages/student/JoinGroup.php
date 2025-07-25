<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/NavBar.php';

session_start();

$projectID = isset($_GET['id']) ? intval($_GET['id']) : 0;

$db = new Database();
$conn = $db->connect();

$groupModel = new GroupModel($conn);
$projectModel = new ProjectModel($conn);

$project = $projectModel->findByProjectId($projectID);
$groups = $groupModel->getGroupsByProject($projectID);
$projectName = $project['title'];
$maxMem = $project['maxMem'] ?? 0;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Join Group</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/JoinGroup.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="listBox">
            <div class="titleBar">
                <h1><?php echo $projectName; ?></h1>
            </div>

            <div class="listTable">
                <div class="columnNameRow">
                    <div class="columnName">Group Name</div>
                    <div class="columnName">Members</div>
                    <div class="columnName">Members Allowed</div>
                    <div class="columnName">Action</div>
                </div>

                <?php foreach ($groups as $group): ?>
                    <?php
                    $groupID = $group['groupID'];
                    $memberCount = $groupModel->countMembersInGroup($groupID);
                    $isFull = $memberCount >= $maxMem;
                    ?>
                    <div class="dataRow">
                        <div class="data"><?= htmlspecialchars($group['groupName']) ?></div>
                        <div class="data"><?= $memberCount ?></div>
                        <div class="data"><?= $maxMem ?></div>
                        <div class="data <?= $isFull ? 'full' : '' ?>">
                            <?php if ($isFull): ?>
                                FULL!!
                            <?php else: ?>
                                <form method="post" action="/FYP2025/SPAMS/server/controllers/JoinGroupController.php">
                                    <input type="hidden" name="groupID" value="<?= $groupID ?>">
                                    <button class="join" type="submit">Join</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <script src="/FYP2025/SPAMS/client/js/JoinGroup.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
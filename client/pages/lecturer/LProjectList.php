<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

session_start();
if (!isset($_SESSION['userID'])) {
    header('Location: /FYP2025/SPAMS/client/index.php');
    exit();
}
$userId = $_SESSION['userID'];

$db = new Database();
$conn = $db->connect();
$userModel = new UserModel($conn);

$user = $userModel->getUserById($userId);
if ($user['roleID'] != 2) {
    header('Location: /FYP2025/SPAMS/client/pages/student/SProjectList.php');
    exit();
}

$model = new ProjectModel($conn);
$projects = $model->findByCreatedId($userId);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Project List</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/LProjectList.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="listBox">
            <div class="titleBar">
                <h1>Projects</h1>
                <button id="createProject">Create Project</button>
            </div>

            <div class="listTable">
                <div class="columnNameRow">
                    <div class="columnName" data-column="title">Project Name<span class="sortIcon"></span></div>
                    <div class="columnName" data-column="deadline">Deadline<span class="sortIcon"></span></div>
                    <div class="columnName" data-column="groups">Groups<span class="sortIcon"></span></div>
                    <div class="columnName" data-column="participants">Participants<span class="sortIcon"></span></div>
                    <div class="columnName" data-column="submittedGroups">Submitted Groups<span class="sortIcon"></span>
                    </div>
                </div>
                <?php if ($projects): ?>
                    <?php foreach ($projects as $project): ?>
                        <?php
                        $stats = $model->getProjectStats($project['projectID']);
                        ?>
                        <a class="dataRow"
                            href="/FYP2025/SPAMS/client/pages/lecturer/LProjectGroups.php?projectID=<?= urlencode($project['projectID']) ?>"
                            data-title="<?= htmlspecialchars(strtolower($project['title'])) ?>"
                            data-deadline="<?= htmlspecialchars($project['deadline']) ?>"
                            data-groups="<?= (int) $project['numGroup'] ?>"
                            data-participants="<?= (int) $stats['participants'] ?>"
                            data-submittedgroups="<?= (int) $stats['submittedGroups'] ?>">
                            <div class="data"><?= htmlspecialchars($project['title']) ?></div>
                            <div class="data"><?= date("d/m/Y h:i A", strtotime($project['deadline'])) ?></div>
                            <div class="data"><?= htmlspecialchars($project['numGroup']) ?></div>
                            <div class="data"><?= htmlspecialchars($stats['participants']) ?></div>
                            <div class="data"><?= htmlspecialchars($stats['submittedGroups']) ?></div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dataRow">
                        <div class="data">Click the create project button to start creating a project now!</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="/FYP2025/SPAMS/client/js/LProjectList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
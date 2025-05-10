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
if ($user['roleID'] != 2){
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
                    <div class="columnName">Project Name</div>
                    <div class="columnName">Deadline</div>
                    <div class="columnName">Groups</div>
                    <div class="columnName">Participants</div>
                    <div class="columnName">Submitted</div>
                </div>
                <?php foreach ($projects as $project): ?>
                    <div class="dataRow">
                        <div class="data"><?= htmlspecialchars($project['title']) ?></div>
                        <div class="data"><?= htmlspecialchars($project['deadline']) ?></div>
                        <div class="data"><?= htmlspecialchars($project['numGroup']) ?></div>
                        <div class="data">TBD</div>
                        <div class="data">TBD</div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
    <script src="/FYP2025/SPAMS/client/js/LProjectList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
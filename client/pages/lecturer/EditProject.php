<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

session_start();
$projectID = intval($_GET['projectID']);

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$project = $projectModel->findByProjectId($projectID);

$title = $project['title'];
$description = $project['description'];
$deadline = $project['deadline'];
$attachments = $projectModel->getAttachmentsByProjectId($projectID);

$existingFiles = json_encode(array_map(function ($attachments) {
    return [
        "id" => $attachments["attachID"],
        "fileName" => $attachments["attachName"],
        "displayName" => $attachments["displayName"]
    ];
}, $attachments));

$deadlineFormatted = '';
if (!empty($deadline)) {
    $dt = new DateTime($deadline);
    $deadlineFormatted = $dt->format('Y-m-d\TH:i');
}

$breadcrumbs = [
    ['label' => 'Projects', 'url' => '/FYP2025/SPAMS/client/pages/lecturer/LProjectList.php'],
    ['label' => $project['title'], 'url' => "/FYP2025/SPAMS/client/pages/lecturer/LProjectGroups.php?projectID={$projectID}"],
    ['label' => 'Edit Project', 'url' => '']
];

$crumbUrls = array_map(fn($crumb) => $crumb['url'], $breadcrumbs);
$jsonUrls = htmlspecialchars(json_encode($crumbUrls), ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Project</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/CreateProject.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="createBox">
            <nav class="breadcrumb-nav" data-crumbs="<?= $jsonUrls ?>" hidden></nav>
            <div class="titleBar">
                <h1>Edit Project</h1>
                <button class="deleteBtn" id="deleteProjectBtn" data-project-id="<?= $projectID ?>"
                    data-project-title="<?= htmlspecialchars($title) ?>">
                    Delete
                </button>

            </div>

            <form action="/FYP2025/SPAMS/server/controllers/EditProjectController.php" method="post"
                enctype="multipart/form-data" onsubmit="validateForm(event)">
                <input type="hidden" name="projectID" value='<?= $projectID ?>'>
                <div class="contentBox">
                    <div class="detailsHalf">
                        <label for="projectName">Project Name</label>
                        <p id="pNameError" style="color: red; margin-left:5%;"></p>
                        <input class="createInput" type="text" id="projectName" name="projectName"
                            placeholder="Project Name" value="<?= htmlspecialchars($title) ?>"><br><br><br>

                        <label for="projectDesc">Project Description</label>
                        <p id="pDescError" style="color: red; margin-left:5%;"></p>
                        <textarea id="projectDesc" name="projectDesc"
                            placeholder="Brief Description"><?= htmlspecialchars($description) ?></textarea><br><br><br>

                        <label for="deadline">Deadline</label>
                        <p id="deadlineError" style="color: red; margin-left:5%;"></p>
                        <input class="createInput" type="datetime-local" id="deadline" name="deadline"
                            placeholder="Deadline" value="<?= $deadlineFormatted ?>"><br><br><br>
                    </div>

                    <div class="fileHalf">
                        <div class="fileTitle" data-existingfiles='<?= $existingFiles ?>'>
                            <label for="fileTitle">Files</label>
                            <input type="file" id="fileInput" name="files[]" style="display: none;" multiple>
                            <button type="button" class="addFile" id="addFile">Add File</button>
                        </div>
                        <div class="fileList" id="fileList">
                            <label>Attached File(s)</label>
                        </div>
                    </div>

                </div>

                <div class="buttons">
                    <button type="button" id="cancel">
                        <a href="/FYP2025/SPAMS/client/pages/lecturer/LProjectGroups.php?projectID=<?= urldecode($projectID) ?>"
                            class="cancelLink">
                            Cancel
                        </a>
                    </button>
                    <button type="submit" id="create">Save</button>
                </div>

            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/EditProject.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
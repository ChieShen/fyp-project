<?php
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: /FYP2025/SPAMS/client/index.php");
    exit;
}
if ($_SESSION['role_id'] != 2){
        header("Location: /FYP2025/SPAMS/client/pages/student/SProjectList.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Create Project</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/CreateProject.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="createBox">
            <div class="titleBar">
                <h1>Create Project</h1>
            </div>

            <form action="/FYP2025/SPAMS/server/controllers/CreateProjectController.php" method="post"
                enctype="multipart/form-data" onsubmit="validateForm(event)">
                <div class="contentBox">

                    <div class="detailsHalf">
                        <label for="projectName">Project Name</label>
                        <p id="pNameError" style="color: red; margin-left:5%;"></p>
                        <input class="createInput" type="text" id="projectName" name="projectName" placeholder="Project Name"><br><br><br>

                        <label for="projectDesc">Project Description</label>
                        <p id="pDescError" style="color: red; margin-left:5%;"></p>
                        <textarea id="projectDesc" name="projectDesc" placeholder="Brief Description"></textarea><br><br><br>

                        <label for="deadline">Deadline</label>
                        <p id="deadlineError" style="color: red; margin-left:5%;"></p>
                        <input class="createInput" type="datetime-local" id="deadline" name="deadline" placeholder="Deadline"><br><br><br>

                        <label for="groupCount">Number of Groups</label>
                        <p id="gcError" style="color: red; margin-left:5%;"></p>
                        <input class="createInput" type="text" id="groupCount" name="groupCount" placeholder="Number of groups"><br><br><br>

                        <label for="maxMem">Maximum Number of Members Per Group</label>
                        <p id="maxMemError" style="color: red; margin-left:5%;"></p>
                        <input class="createInput" type="text" id="maxMem" name="maxMem" placeholder="Maximum Number"><br><br><br>
                    </div>

                    <div class="fileHalf">
                        <div class="fileTitle">
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
                    <button type="button" id="cancel">Cancel</button>
                    <button type="submit" id="create">Create</button>
                </div>

            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/CreateProject.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
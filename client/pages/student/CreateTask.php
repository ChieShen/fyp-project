<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Task</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/CreateTask.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="createBox">
            <div class="titleBar">
                <h1>New Task</h1>
            </div>

            <form action="" method="post" onsubmit="validateForm(event)">
                <div class="contentBox">
                    <label for="taskName">Task Name</label>
                    <p id="tNameError" style="color: red; margin-left:5%;"></p>
                    <input type="text" id="taskName" name="taskName" placeholder="Task Name"><br><br>

                    <label for="taskDesc">Task Description</label>
                    <p id="tDescError" style="color: red; margin-left:5%;"></p>
                    <textarea id="taskDesc" name="taskDesc" placeholder="Brief Description"></textarea><br><br>
                </div>

                <label>Contributor(s)</label>
                <div class="contributorList">
                    <div class="contributorRow">
                        <label>Alice Tan</label>
                        <input type="checkbox" name="contributors[]" value="student1">
                        <div class="spacer"></div>
                    </div>
                    <div class="contributorRow">
                        <label>John Lim</label>
                        <input type="checkbox" name="contributors[]" value="student2">
                        <div class="spacer"></div>
                    </div>
                </div>

                <div class="buttons">
                    <button type="button" id="cancel">Cancel</button>
                    <button type="submit" id="create">Create</button>
                </div>

            </form>

        </div>
        <script src="/FYP2025/SPAMS/client/js/CreateTask.js" defer></script>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
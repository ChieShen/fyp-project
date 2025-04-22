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
                <?php for ($i = 1; $i <= 100; $i++): ?>
                    <?php $progress = rand(0, 100); ?>
                    <div class="dataRow">
                        <div class="data">Assignment <?= $i ?></div>
                        <div class="data"><?= date('Y-m-d', strtotime("+$i days")) ?></div>
                        <div class="data">Group <?= ceil($i / 5) ?></div>
                        <div class="data"><?= chr(65 + ($i % 26)) ?></div>
                        <div class="data"><?= chr(65 + ($i % 26)) ?></div>
                    </div>
                <?php endfor; ?>

            </div>
        </div>
    </div>
    <script src="/FYP2025/SPAMS/client/js/LProjectList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
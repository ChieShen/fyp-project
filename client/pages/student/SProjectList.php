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
                <h1>Project List</h1>
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
                <div class="dataRow">
                    <div class="data">Project Name</div>
                    <div class="data">Group Name</div>
                    <div class="data">Deadline</div>
                    <div class="data">Created By</div>
                    <div class="data">
                        <div class="progress-container">
                            <div class="progress-bar" id="progressBar" data-progress="100"></div>
                        </div>
                    </div>
                </div>
                <div class="dataRow">
                    <div class="data">Project Name</div>
                    <div class="data">Group Name</div>
                    <div class="data">Deadline</div>
                    <div class="data">Created By</div>
                    <div class="data">Progress</div>
                </div>
                <?php for ($i = 1; $i <= 100; $i++): ?>
                    <?php $progress = rand(0, 100); ?>
                    <div class="dataRow">
                        <div class="data">Project <?= $i ?></div>
                        <div class="data">Group <?= ceil($i / 5) ?></div>
                        <div class="data"><?= date('Y-m-d', strtotime("+$i days")) ?></div>
                        <div class="data">Creator <?= chr(65 + ($i % 26)) ?></div>
                        <div class="data">
                            <div class="progress-container">
                                <div class="progress-bar" data-progress="<?= $progress ?>"></div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>

            </div>
        </div>
    </div>
    <script src="../../js/SProjectList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
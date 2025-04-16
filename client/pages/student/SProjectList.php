<!DOCTYPE html>
<html>

<head>
    <title>Project List</title>
    <link rel="stylesheet" href="../../css/SProjectList.css" />
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="listBox">
            <div class="titleBar">
                <h1>Project List</h1>
                <button>Join Project</button>
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
            </div>
        </div>
    </div>
    <script src="../../js/SProjectList.js" defer></script>
</body>

</html>
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
                <h1>Rmb Replace This</h1>
            </div>

            <div class="listTable">
                <div class="columnNameRow">
                    <div class="columnName">Group Name</div>
                    <div class="columnName">Members</div>
                    <div class="columnName">Members Allowed</div>
                    <div class="columnName">Action</div>
                </div>
                <div class="dataRow">
                    <div class="data">Group 1</div>
                    <div class="data">1</div>
                    <div class="data">5</div>
                    <div class="data">
                        <button class="join">Join</button>
                    </div>
                </div>
                <div class="dataRow">
                    <div class="data">Group 2</div>
                    <div class="data">5</div>
                    <div class="data">5</div>
                    <div class="data" id="full">FULL!!</div>
                </div>

            </div>
        </div>
    </div>
    <script src="/FYP2025/SPAMS/client/js/JoinGroup.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Task List</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/TaskList.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="projectDetails">
                <div class="titleBar">
                    <h1>Replace This</h1>
                    <button id="transferBtn">Transfer Role</button>
                </div><br>

                <p class="label">Project Description:</p>
                <p class="details">
                    In the spirit of togetherness and celebration, we warmly invite you to Rumah Terbuka Sunway a
                    special gathering to celebrate Hari Raya with the entire Sunway University and Sunway College
                    community! This is the perfect time to reconnect, enjoy delicious food, and immerse yourself in the
                    rich traditions of this festive season.
                </p><br><br>

                <p class="label">Created By:</p>
                <a href="" class="creator">David (Click to Message)</a><br><br>

                <p class="label">Attached File(s)</p>
                <a href="" class="files">Assignment2.pdf</a>
                <a href="" class="files">Assignment2.pdf</a>
                <a href="" class="files">Assignment2.pdf</a>
            </div>

            <div class="groups">
                <div class="titleBar">
                    <h1>Tasks</h1>
                    <button id="addBtn">Add Task</button>
                </div>

                <div class="memberTable">
                    <div class="columnNameRow">
                        <div class="columnName">Task Name</div>
                        <div class="columnName">Assigned To</div>
                        <div class="columnName">Last Updated</div>
                        <div class="columnName">Status</div>
                        <div class="columnName">Action</div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Task Name</div>
                        <div class="data">Student Name</div>
                        <div class="data">20/12/2025</div>
                        <div class="data ongoing">Ongoing</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Task Name</div>
                        <div class="data">Student Name</div>
                        <div class="data">10/12/2025</div>
                        <div class="data done">Done</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Task Name</div>
                        <div class="data">Student Name</div>
                        <div class="data">No data</div>
                        <div class="data notStarted">Not Started</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                </div>
                <button id="downloadAll">Download All Lastest Files</button>
            </div>

            <form class="submission">
                <h1>Submission</h1>
                <div class="submissionBar">
                    <input type="file" id="fileInput" style="display: none;" />

                    <label for="fileInput">
                        <img class="icon" src="/FYP2025/SPAMS/client/assets/images/attach file.png">
                    </label>

                    <span id="fileName">No file selected</span>

                    <button id="submission" type="submit">Submit</button>
                </div>
            </form>

        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/TaskList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
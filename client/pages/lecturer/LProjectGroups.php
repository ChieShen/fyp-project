<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Project</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/LProjectGroups.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="projectDetails">
                <div class="titleBar">
                    <h1>Assignment 2</h1>
                    <button id="editBtn">Edit</button>
                </div><br>

                <p class="label">Project Description:</p>
                <p class="details">
                    In the spirit of togetherness and celebration, we warmly invite you to Rumah Terbuka Sunway a
                    special gathering to celebrate Hari Raya with the entire Sunway University and Sunway College
                    community! This is the perfect time to reconnect, enjoy delicious food, and immerse yourself in the
                    rich traditions of this festive season.
                </p><br><br>

                <p class="label">Created By:</p>
                <p class="details">David</p><br><br>

                <p class="label">Join Code:</p>
                <p class="details">123456</p><br><br>

                <p class="label">Attached File(s)</p>
                <a href="" class="files">Assignment2.pdf</a>
                <a href="" class="files">Assignment2.pdf</a>
                <a href="" class="files">Assignment2.pdf</a>
            </div>

            <div class="groups">
                <div class="titleBar">
                    <h1>Group 1</h1>
                    <div class="progress-container">
                        <div class="progress-bar" id="progressBar" data-progress="100"></div>
                    </div>
                    <button class="deleteBtn">Delete</button>
                </div>

                <div class="memberTable">
                    <div class="columnNameRow">
                        <div class="columnName">Name</div>
                        <div class="columnName">Assigned Parts</div>
                        <div class="columnName">Completed Parts</div>
                        <div class="columnName">Role</div>
                        <div class="columnName">Action</div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Student Name</div>
                        <div class="data">10</div>
                        <div class="data">1</div>
                        <div class="data" id="leaderRole">Leader</div>
                        <div class="data">
                            <button class="transferBtn">Transfer</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Student Name</div>
                        <div class="data">10</div>
                        <div class="data">1</div>
                        <div class="data" id="memberRole">Member</div>
                        <div class="data">
                            <button class="removeBtn">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="groupSubmission">
                    <p class="label">Submission:</p>
                    <div class="submissionBar">
                        <p class="files">Assignment2.pdf</p>
                        <button class="download">Download</button>
                    </div>
                </div>

            </div>

            <button id="downloadAll">Download All Submissions</button>

        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/LProjectGroups.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
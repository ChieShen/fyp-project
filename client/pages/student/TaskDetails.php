<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Task Details</title>
    <link rel="stylesheet" href="/FYP2025/SPAMS/client/css/TaskDetails.css" />
    <link rel="icon" type="image/png" href="/FYP2025/SPAMS/client/assets/images/logo.png">
</head>

<body>
    <div class="page">
        <?php include '../../components/sidebar.php'; ?>

        <div class="contentBox">
            <div class="taskDetails">
                <div class="titleBar">
                    <h1>Replace This</h1>
                    <button id="editBtn">Edit Task</button>
                </div><br>

                <p class="label">Task Description:</p>
                <p class="details">
                    In the spirit of togetherness and celebration, we warmly invite you to Rumah Terbuka Sunway a
                    special gathering to celebrate Hari Raya with the entire Sunway University and Sunway College
                    community! This is the perfect time to reconnect, enjoy delicious food, and immerse yourself in the
                    rich traditions of this festive season.
                </p><br><br>

                <p class="label">Contributors</p>
                <a href="" class="contributor">David (Click to Message)</a>
                <a href="" class="contributor">John (Click to Message)</a>
            </div>

            <div class="uploads">
                <div class="titleBar">
                    <h1>Uploads</h1>
                </div>

                <div class="uploadsTable">
                    <div class="columnNameRow">
                        <div class="columnName">File Name</div>
                        <div class="columnName">Uploaded By</div>
                        <div class="columnName">Time Uploaded</div>
                        <div class="columnName">Action</div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Waterfall Model.pdf</div>
                        <div class="data">Student Name</div>
                        <div class="data">20/12/2025</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                    <div class="dataRow">
                        <div class="data">Waterfall Model.docx</div>
                        <div class="data">Student Name</div>
                        <div class="data">10/12/2025</div>
                        <div class="data">
                            <button class="download">Download</button>
                        </div>
                    </div>
                </div>

                <form class="uploadForm">
                    <div class="uploadBar">
                        <input type="file" id="fileInput" style="display: none;" />

                        <label for="fileInput">
                            <img class="icon" src="/FYP2025/SPAMS/client/assets/images/attach file.png">
                        </label>

                        <span id="fileName">No file selected</span>

                        <button id="uploadFile" type="submit">Upload</button>
                    </div>
                </form>


            </div>
            
            <form class="statusUpdate">
                    <!-- <button id="markUndone" type="submit" name="action" value="undone">Update</button> -->
                    <button id="markDone" type="submit" name="action" value="done">Mark As Done</button>
                </form>

        </div>

    </div>

    <script src="/FYP2025/SPAMS/client/js/TaskList.js" defer></script>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/client/components/MessageBox.php'; ?>
</body>

</html>
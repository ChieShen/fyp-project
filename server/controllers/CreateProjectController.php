<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once '../models/ProjectModel.php';
require_once '../models/UserModel.php';
require_once '../models/GroupModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = $_POST['projectName'];
    $projectDesc = $_POST['projectDesc'];
    $deadline = $_POST['deadline'];
    $groupCount = $_POST['groupCount'];
    $maxMem = $_POST['maxMem'];
    $createdBy = $_SESSION['userID'];

    if (empty($projectName) || empty($deadline) || empty($groupCount) || empty($maxMem)) {
        die("Missing required fields.");
    }

    $db = new Database();
    $conn = $db->connect();

    $user = new UserModel($conn);

    $projectModel = new ProjectModel($conn);
    $joinCode = $projectModel->generateUniqueJoinCode();
    $groupModel = new GroupModel($conn);

    //Save project into database
    $projectID = $projectModel->save([
        'createdBy' => $createdBy,
        'title' => $projectName,
        'description' => $projectDesc,
        'deadline' => $deadline,
        'joinCode' => $joinCode,
        'maxMem' => $maxMem,
        'numGroup' => $groupCount
    ]);

    //Create the Number of Groups as defined
    for ($i = 1; $i <= $groupCount; $i++) {
        $groupName = "Group $i";
        $groupModel->createGroup($projectID, $groupName);
    }

    // Create directory
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/uploads/attachments/' . $createdBy . '/' . $projectID . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    //Save uploaded files
    foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
        $originalName = basename($_FILES['files']['name'][$index]);
        $safeName = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '_', $originalName);
        $destination = $uploadDir . '/' . $safeName;

        if (move_uploaded_file($tmpName, $destination)) {
            $projectModel->saveFile($projectID, $safeName, $originalName, $createdBy);
        }
    }

    //Redirect user back to project list page
    header("Location: /FYP2025/SPAMS/client/pages/lecturer/LProjectList.php");
    exit();
}

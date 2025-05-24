<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ChatModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['userID'])) {
        die("Unauthorized access.");
    }

    $userID = $_SESSION['userID'];
    $groupID = isset($_POST['groupID']) ? intval($_POST['groupID']) : 0;

    if ($groupID <= 0) {
        die("Invalid group ID.");
    }

    $db = new Database();
    $conn = $db->connect();
    $groupModel = new GroupModel($conn);
    $chatModel = new ChatModel($conn);
    $projectModel = new ProjectModel($conn);

    // Check if user is already in a group for this project
    $projectID = $groupModel->getProjectIdByGroupId($groupID);
    if ($groupModel->isUserInProject($userID, $projectID)) {
        die("You are already in a group for this project.");
    }

    $project = $projectModel->findByProjectId($projectID);
    $projectName = $project['title'];

    $group = $groupModel->getGroupById($groupID);
    $groupName = $group['groupName'];

    $chatName = $projectName . ' ' . $groupName;

    // Count members
    $memberCount = $groupModel->countMembersInGroup($groupID);

    // Assign role
    $isLeader = ($memberCount === 0) ? true : false;

    // Add to group
    $groupModel->assignUserToGroup($groupID, $userID, $isLeader);

    // Check if chatroom already exists for this group
    $chatID = $chatModel->getChatIDByGroupID($groupID);

    if (!$chatID) {
        $chatID = $chatModel->createGroupChatroom($chatName, $groupID);
    }

    $chatModel->addUserToChatroom($chatID, $userID);

    header("Location: /FYP2025/SPAMS/client/pages/student/SProjectList.php");
    exit();
} else {
    die("Invalid request method.");
}

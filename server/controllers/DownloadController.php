<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/ProjectModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/GroupModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/UserModel.php';

$conn = (new Database())->connect();
$projectModel = new ProjectModel($conn);
$groupModel = new GroupModel($conn);
$taskModel = new TaskModel($conn);
$userModel = new UserModel($conn);

$type = $_GET['type'] ?? null;
$filename = basename($_GET['file'] ?? '');
$displayName = basename($_GET['name'] ?? '');

$filePath = '';

switch ($type) {
    case 'attachment':
        $projectID = intval($_GET['projectID'] ?? 0);
        if (!$projectID) {
            http_response_code(400);
            exit('Missing project ID.');
        }
        $project = $projectModel->findByProjectId($projectID);
        if (!$project) {
            http_response_code(404);
            exit('Project not found.');
        }
        $creatorID = $project['createdBy'];
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/attachments/{$creatorID}/{$projectID}/{$filename}";
        $displayName = $displayName ?: $filename;
        break;

    case 'submission':
        $projectID = intval($_GET['projectID'] ?? 0);
        $groupID = intval($_GET['groupID'] ?? 0);
        if (!$projectID || !$groupID) {
            http_response_code(400);
            exit('Missing project or group ID.');
        }
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/{$groupID}/{$filename}";
        $displayName = $displayName ?: $filename;
        break;

    case 'task':
        $projectID = intval($_GET['projectID'] ?? 0);
        $taskID = intval($_GET['taskID'] ?? 0);
        $userID = intval($_GET['userID'] ?? 0);
        if (!$projectID || !$taskID || !$userID) {
            http_response_code(400);
            exit('Missing project, task, or user ID.');
        }
        $filePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/{$taskID}/{$userID}/{$filename}";
        $displayName = $displayName ?: $filename;
        break;

    case 'allSubmissions':
        $projectID = intval($_GET['projectID'] ?? 0);
        if (!$projectID) {
            http_response_code(400);
            exit('Missing project ID.');
        }

        $groups = $groupModel->getGroupsByProject($projectID);
        $project = $projectModel->findByProjectId($projectID);
        $zip = new ZipArchive();
        $zipFilename = "Project_{$project['title']}_Submissions.zip";
        $tempPath = sys_get_temp_dir() . "/$zipFilename";

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            http_response_code(500);
            exit('Could not create ZIP file.');
        }

        foreach ($groups as $grp) {
            $submission = $groupModel->getSubmissionByGroup($projectID, $grp['groupID']);
            if ($submission) {
                $file = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/{$grp['groupID']}/{$submission['submissionName']}";
                if (file_exists($file)) {
                    $safeName = "Group_{$grp['groupID']}_{$submission['displayName']}";
                    $zip->addFile($file, $safeName);
                }
            }
        }

        $zip->close();

        if (!file_exists($tempPath)) {
            http_response_code(500);
            exit('Failed to create ZIP file.');
        }

        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=\"$zipFilename\"");
        header('Content-Length: ' . filesize($tempPath));
        readfile($tempPath);
        unlink($tempPath);
        exit;

    case 'allLatestTaskUploads':
        $projectID = intval($_GET['projectID'] ?? 0);
        $groupID = intval($_GET['groupID'] ?? 0);

        if (!$projectID || !$groupID) {
            http_response_code(400);
            exit('Missing project or group ID.');
        }

        $project = $projectModel->findByProjectId($projectID);
        $tasks = $taskModel->getTasksByProjectAndGroup($projectID, $groupID);

        $zip = new ZipArchive();
        $zipFilename = "Latest_Task_Uploads_Project_{$project['title']}_Group_{$groupID}.zip";
        $tempPath = sys_get_temp_dir() . "/$zipFilename";

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            http_response_code(500);
            exit('Could not create ZIP file.');
        }

        foreach ($tasks as $task) {
            $contributors = $taskModel->getContributorsByTask($task['taskID']);
            foreach ($contributors as $contributor) {
                $latestFile = $taskModel->getLatestUpload($task['taskID'], $contributor['userID']);
                if ($latestFile) {
                    $user = $userModel->getUserById($contributor['userID']);
                    $file = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/{$task['taskID']}/{$contributor['userID']}/{$latestFile['fileName']}";
                    if (file_exists($file)) {
                        $safeName = "Task_{$task['taskName']}_{$user['firstName']}_{$user['lastName']}_{$latestFile['displayName']}";
                        $zip->addFile($file, $safeName);
                    }
                }
            }
        }

        $zip->close();

        if (!file_exists($tempPath)) {
            http_response_code(500);
            exit('Failed to create ZIP file.');
        }

        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=\"$zipFilename\"");
        header('Content-Length: ' . filesize($tempPath));
        readfile($tempPath);
        unlink($tempPath);
        exit;

    case 'latestUploadsByTask':
        $projectID = intval($_GET['projectID'] ?? 0);
        $taskID = intval($_GET['taskID'] ?? 0);

        if (!$projectID || !$taskID) {
            http_response_code(400);
            exit('Missing project or task ID.');
        }

        $project = $projectModel->findByProjectId($projectID);
        $task = $taskModel->getTaskById($taskID);

        if (!$task) {
            http_response_code(404);
            exit('Task not found.');
        }

        $contributors = $taskModel->getContributorsByTask($taskID);

        $zip = new ZipArchive();
        $zipFilename = "Latest_Uploads_Task_{$task['taskName']}_Project_{$project['title']}.zip";
        $tempPath = sys_get_temp_dir() . "/$zipFilename";

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            http_response_code(500);
            exit('Could not create ZIP file.');
        }

        foreach ($contributors as $contributor) {
            $latestFile = $taskModel->getLatestUpload($taskID, $contributor['userID']);
            if ($latestFile) {
                $user = $userModel->getUserById($contributor['userID']);
                $file = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/{$taskID}/{$contributor['userID']}/{$latestFile['fileName']}";
                if (file_exists($file)) {
                    $safeName = "{$user['firstName']}_{$user['lastName']}_{$latestFile['displayName']}";
                    $zip->addFile($file, $safeName);
                }
            }
        }

        $zip->close();

        if (!file_exists($tempPath)) {
            http_response_code(500);
            exit('Failed to create ZIP file.');
        }

        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename=\"$zipFilename\"");
        header('Content-Length: ' . filesize($tempPath));
        readfile($tempPath);
        unlink($tempPath);
        exit;

    case 'studentsList':
        $projectID = intval($_GET['projectID']);
        $project = $projectModel->findByProjectId($projectID);
        $projectName = $project['title'];

        $safeProjectName = preg_replace('/[^a-zA-Z0-9-_]/', '_', $projectName);

        $groupModel = new GroupModel($conn);
        $groups = $groupModel->getGroupsByProject($projectID);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $safeProjectName . '_student_list.csv"');

        $output = fopen("php://output", "w");
        fputcsv($output, ['Group Name', 'Full Name', 'Username (Student ID)']);

        foreach ($groups as $group) {
            $members = $groupModel->getMembersByGroup($group['groupID']);
            foreach ($members as $member) {
                $fullname = $member['firstName'] . ' ' . $member['lastName'];
                fputcsv($output, [$group['groupName'], $fullname, $member['username']]);
            }
        }

        fclose($output);
        exit;


    default:
        http_response_code(400);
        exit('Invalid file type.');
}

if (!file_exists($filePath)) {
    http_response_code(404);
    exit('File not found: ' . $filePath);
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $displayName . '"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

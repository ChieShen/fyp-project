<?php
class TaskModel
{
    private $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // Create a new task
    public function createTask($projectID, $groupID, $status, $name, $description)
    {
        $stmt = $this->conn->prepare("INSERT INTO task (projectID, groupID, status, taskName, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $projectID, $groupID, $status, $name, $description);
        if ($stmt->execute()) {
            return $this->conn->insert_id; // return the new taskID
        }
        return false;
    }

    // Update a task
    public function updateTask($taskID, $status, $name, $description)
    {
        $stmt = $this->conn->prepare("UPDATE task SET status = ?, taskName = ?, description = ? WHERE taskID = ?");
        $stmt->bind_param("issi", $status, $name, $description, $taskID);
        return $stmt->execute();
    }

    // Get task by ID
    public function getTaskById($taskID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get all tasks for a project/group
    public function getTasksByProjectAndGroup($projectID, $groupID)
    {
        $stmt = $this->conn->prepare("SELECT * FROM task WHERE projectID = ? AND groupID = ?");
        $stmt->bind_param("ii", $projectID, $groupID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update only the status of a task
    public function updateTaskStatus($taskID, $status)
    {
        $stmt = $this->conn->prepare("UPDATE task SET status = ? WHERE taskID = ?");
        $stmt->bind_param("ii", $status, $taskID);
        return $stmt->execute();
    }

    // Delete a task
    public function deleteTask($taskID)
    {
        // Step 1: Get projectID and groupID from task
        $stmt = $this->conn->prepare("SELECT projectID, groupID FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result) {
            return false; // Task not found
        }

        $projectID = (int) $result['projectID'];
        $groupID = (int) $result['groupID'];

        // Step 2: Remove all contributors
        $this->removeAllContributors($taskID);

        // Step 3: Delete all files and the task directory
        $taskDir = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/{$groupID}/{$taskID}";
        $this->deleteDirectory($taskDir);

        // Step 4: Delete the task
        $stmt = $this->conn->prepare("DELETE FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    private function deleteDirectory($dir)
    {
        if (!is_dir($dir))
            return;

        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..')
                continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->deleteDirectory($path); // Recursively delete subdirectories
            } else {
                unlink($path); // Delete file
            }
        }
        rmdir($dir); // Remove the directory itself
    }

    // Assign contributors to a task
    public function addContributors($taskID, array $userIDs)
    {
        $stmt = $this->conn->prepare("INSERT INTO taskcontributor (userID, taskID) VALUES (?, ?)");
        foreach ($userIDs as $userID) {
            $stmt->bind_param("ii", $userID, $taskID);
            $stmt->execute();
        }
        return true;
    }

    // Get contributors for a task
    public function getContributorsByTask($taskID)
    {
        $stmt = $this->conn->prepare("SELECT userID FROM taskcontributor WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Remove all contributors from a task
    public function removeAllContributors($taskID)
    {
        $stmt = $this->conn->prepare("DELETE FROM taskcontributor WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        return $stmt->execute();
    }
    // Upload a file related to a task
    public function uploadTaskFile($taskID, $userID, $fileName, $displayName)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO taskuploads (taskID, userID, fileName, displayName, uploadedAt)
                 VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("iiss", $taskID, $userID, $fileName, $displayName);
        return $stmt->execute();
    }

    // Get uploaded files for a task
    public function getUploadedFilesByTask($taskID)
    {
        $stmt = $this->conn->prepare("
        SELECT tu.*, u.firstName, u.lastName 
        FROM taskuploads tu 
        JOIN user u ON tu.userID = u.userID 
        WHERE tu.taskID = ?
        ORDER BY tu.uploadedAt DESC
    ");
        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Check if a task is marked as done
    public function isTaskDone($taskID)
    {
        $stmt = $this->conn->prepare("SELECT status FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return isset($result['status']) && $result['status'] == 2;
    }

    public function countAssignedTasksByUserAndGroup($userID, $projectID, $groupID)
    {
        $stmt = $this->conn->prepare("
        SELECT COUNT(*) as total 
        FROM task t
        JOIN taskcontributor tc ON t.taskID = tc.taskID
        WHERE tc.userID = ? AND t.projectID = ? AND t.groupID = ?
    ");
        $stmt->bind_param("iii", $userID, $projectID, $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    public function countCompletedTasksByUserAndGroup($userID, $projectID, $groupID)
    {
        $stmt = $this->conn->prepare("
        SELECT COUNT(*) as completed 
        FROM task t
        JOIN taskcontributor tc ON t.taskID = tc.taskID
        WHERE tc.userID = ? AND t.projectID = ? AND t.groupID = ? AND t.status = 2
    ");
        $stmt->bind_param("iii", $userID, $projectID, $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['completed'] ?? 0;
    }

    public function getLatestUpload($taskID, $userID)
    {
        $stmt = $this->conn->prepare("
        SELECT * FROM taskuploads
        WHERE taskID = ? AND userID = ?
        ORDER BY uploadedAt DESC
        LIMIT 1
    ");
        $stmt->bind_param("ii", $taskID, $userID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
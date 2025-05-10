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
        // First, delete contributors
        $this->removeAllContributors($taskID);

        // Then, delete the task
        $stmt = $this->conn->prepare("DELETE FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        return $stmt->execute();
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


}
?>
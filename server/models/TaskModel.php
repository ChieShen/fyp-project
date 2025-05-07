<?php
class TaskModel {
    private $conn;

    public function __construct(mysqli $dbConnection) {
        $this->conn = $dbConnection;
    }

    // Create a new task
    public function createTask($projectID, $groupID, $status, $name) {
        $stmt = $this->conn->prepare("INSERT INTO task (projectID, groupID, status, taskName) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $projectID, $groupID, $status, $name);
        return $stmt->execute();
    }
    
    public function updateTask($taskID, $status, $name) {
        $stmt = $this->conn->prepare("UPDATE task SET status = ?, taskName = ? WHERE taskID = ?");
        $stmt->bind_param("isi", $status, $name, $taskID);
        return $stmt->execute();
    }
   

    // Get task by ID
    public function getTaskById($taskID) {
        $stmt = $this->conn->prepare("SELECT * FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get all tasks for a project/group
    public function getTasksByProjectAndGroup($projectID, $groupID) {
        $stmt = $this->conn->prepare("SELECT * FROM task WHERE projectID = ? AND groupID = ?");
        $stmt->bind_param("ii", $projectID, $groupID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update task status
    public function updateTaskStatus($taskID, $status) {
        $stmt = $this->conn->prepare("UPDATE task SET status = ? WHERE taskID = ?");
        $stmt->bind_param("ii", $status, $taskID);
        return $stmt->execute();
    }

    // Delete a task
    public function deleteTask($taskID) {
        $stmt = $this->conn->prepare("DELETE FROM task WHERE taskID = ?");
        $stmt->bind_param("i", $taskID);
        return $stmt->execute();
    }
}
?>

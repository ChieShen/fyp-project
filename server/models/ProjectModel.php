<?php

class ProjectModel
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function save($data)
    {
        $stmt = $this->conn->prepare("INSERT INTO project (createdBy, title, description, deadline) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $data['createdBy'], $data['title'], $data['description'], $data['deadline']);
        $stmt->execute();
        return $stmt->insert_id;
    }

    public function findByProjectId(int $projectId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM project WHERE projectID = ?");
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();
        $stmt->close();

        return $project ?: null;
    }

    public function findByCreatedId(int $userId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM project WHERE createdBy = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = [];

        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }

        $stmt->close();
        return $projects;
    }

    public function delete(int $projectID): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM project WHERE projectID = ?");
        $stmt->bind_param("i", $projectID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function update(int $projectID, string $title, ?string $description, string $deadline): bool
    {
        $stmt = $this->conn->prepare("UPDATE project SET title = ?, description = ?, deadline = ? WHERE projectID = ?");
        $stmt->bind_param("sssi", $title, $description, $deadline, $projectID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function saveFile($projectID, $filename, $uploader) {
        $stmt = $this->conn->prepare("INSERT INTO attachment (attachName, uploader, projectID) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $filename, $uploader, $projectID);
        $stmt->execute();
    }
    
}

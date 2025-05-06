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
        $stmt = $this->conn->prepare(
            "INSERT INTO project (createdBy, title, description, deadline, joinCode, maxMem, numGroup) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "issssii",
            $data['createdBy'],
            $data['title'],
            $data['description'],
            $data['deadline'],
            $data['joinCode'],
            $data['maxMem'],
            $data['numGroup']
        );
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


    public function update(
        int $projectID,
        string $title,
        ?string $description,
        string $deadline,
        int $maxMem,
        int $numGroup
    ): bool {
        $stmt = $this->conn->prepare(
            "UPDATE project SET title = ?, description = ?, deadline = ?, maxMem = ?, numGroup = ? WHERE projectID = ?"
        );
        $stmt->bind_param("sssiii", $title, $description, $deadline, $maxMem, $numGroup, $projectID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function saveFile($projectID, $filename, $uploader)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO attachment (attachName, uploader, projectID) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sii", $filename, $uploader, $projectID);
        $stmt->execute();
    }

    public function isJoinCodeExists(string $joinCode): bool
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM project WHERE joinCode = ?");
        $stmt->bind_param("s", $joinCode);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result['count'] > 0;
    }

    public function generateUniqueJoinCode(): string
    {
        do {
            $joinCode = $this->generateJoinCode();
        } while ($this->isJoinCodeExists($joinCode));
        return $joinCode;
    }

    private function generateJoinCode(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charsArray = str_split($chars);
        shuffle($charsArray);
        return implode('', array_slice($charsArray, 0, 6));
    }

    public function findByJoinCode(string $joinCode): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM project WHERE joinCode = ?");
        $stmt->bind_param("s", $joinCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $project = $result->fetch_assoc();
        $stmt->close();

        return $project ?: null;
    }

}

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

    public function saveFile($projectID, $filename, $displayName, $uploader)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO attachment (attachName, displayName, uploader, projectID) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssii", $filename, $displayName, $uploader, $projectID);
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
    public function getAttachmentsByProjectId(int $projectId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM attachment WHERE projectID = ?");
        $stmt->bind_param("i", $projectId);
        $stmt->execute();
        $result = $stmt->get_result();
        $attachments = [];

        while ($row = $result->fetch_assoc()) {
            $attachments[] = $row;
        }

        $stmt->close();
        return $attachments;
    }

    public function getProjectStats(int $projectId): array
    {
        // Get number of participants
        $stmt1 = $this->conn->prepare("
        SELECT COUNT(*) AS participantCount 
        FROM groupmember gm
        JOIN projectgroups pg ON gm.groupID = pg.groupID
        WHERE pg.projectID = ?
    ");
        $stmt1->bind_param("i", $projectId);
        $stmt1->execute();
        $result1 = $stmt1->get_result()->fetch_assoc();
        $participantCount = $result1['participantCount'];
        $stmt1->close();

        // Get number of groups that submitted (assuming column `hasSubmitted`)
        $stmt2 = $this->conn->prepare("
        SELECT COUNT(*) AS submittedCount 
        FROM projectgroups 
        WHERE projectID = ? AND submitted = 1
    ");
        $stmt2->bind_param("i", $projectId);
        $stmt2->execute();
        $result2 = $stmt2->get_result()->fetch_assoc();
        $submittedCount = $result2['submittedCount'];
        $stmt2->close();

        // Get total number of groups
        $stmt3 = $this->conn->prepare("
        SELECT COUNT(*) AS groupCount 
        FROM projectgroups
        WHERE projectID = ?
    ");
        $stmt3->bind_param("i", $projectId);
        $stmt3->execute();
        $result3 = $stmt3->get_result()->fetch_assoc();
        $groupCount = $result3['groupCount'];
        $stmt3->close();

        return [
            'participants' => $participantCount,
            'submittedGroups' => $submittedCount,
            'totalGroups' => $groupCount,
        ];
    }

    // Delete an attachment by its ID
    public function deleteAttachment(int $attachID): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM attachment WHERE attachID = ?");
        $stmt->bind_param("i", $attachID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    // Get a single attachment by its ID
    public function getAttachmentById(int $attachID): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM attachment WHERE attachID = ?");
        $stmt->bind_param("i", $attachID);
        $stmt->execute();
        $result = $stmt->get_result();
        $attachment = $result->fetch_assoc();
        $stmt->close();

        return $attachment ?: null;
    }

    // Add a new attachment (alias of saveFile for naming consistency)
    public function addAttachment(int $projectID, string $filename, string $displayName, int $uploader): bool
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO attachment (attachName, displayName, uploader, projectID) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssii", $filename, $displayName, $uploader, $projectID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

}

<?php

class GroupModel
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    //Create group using project id and group name
    public function createGroup(int $projectID, string $groupName): int
    {
        $stmt = $this->conn->prepare("INSERT INTO projectgroups (projectID, groupName) VALUES (?, ?)");
        $stmt->bind_param("is", $projectID, $groupName);
        $stmt->execute();
        $groupId = $stmt->insert_id;
        $stmt->close();
        return $groupId;
    }

    //Get groups by project id
    public function getGroupsByProject(int $projectID): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM projectgroups WHERE projectID = ?");
        $stmt->bind_param("i", $projectID);
        $stmt->execute();
        $result = $stmt->get_result();
        $groups = [];

        while ($row = $result->fetch_assoc()) {
            $groups[] = $row;
        }

        $stmt->close();
        return $groups;
    }

    //Get group using group id
    public function getGroupById(int $groupID): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM projectgroups WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result();
        $group = $result->fetch_assoc();
        $stmt->close();
        return $group ?: null;
    }

    //Add user to group
    public function assignUserToGroup(int $groupID, int $userID, bool $isLeader = false): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO groupmember (groupID, userID, isLeader) VALUES (?, ?, ?)");
        $leaderFlag = $isLeader ? 1 : 0;
        $stmt->bind_param("iii", $groupID, $userID, $leaderFlag);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    //Remove user from group
    public function removeUserFromGroup(int $groupID, int $userID): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM groupmember WHERE groupID = ? AND userID = ?");
        $stmt->bind_param("ii", $groupID, $userID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    //Get all members in the group using group id
    public function getMembersByGroup(int $groupID): array
    {
        $stmt = $this->conn->prepare("
            SELECT u.*, gm.isLeader FROM user u
            JOIN groupmember gm ON u.userID = gm.userID
            WHERE gm.groupID = ?
        ");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result();
        $members = [];

        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }

        $stmt->close();
        return $members;
    }

    //Calculate the number of members inside the group
    public function countMembersInGroup(int $groupID): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM groupmember WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) $result['count'];
    }

    //Delete group using group id
    public function deleteGroup(int $groupID): bool
    {
        // Step 1: Get projectID for the group
        $stmt = $this->conn->prepare("SELECT projectID FROM projectgroups WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$result) {
            return false; // Group not found
        }

        $projectID = (int) $result['projectID'];
        $subTFilePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/submissions/{$projectID}/{$groupID}";
        $upFilePath = $_SERVER['DOCUMENT_ROOT'] . "/FYP2025/SPAMS/uploads/tasks/{$projectID}/{$groupID}";

        // Step 2: Delete group members to avoid FK constraint issues
        $this->conn->query("DELETE FROM groupmember WHERE groupID = $groupID");

        // Step 3: Delete the group
        $stmt = $this->conn->prepare("DELETE FROM projectgroups WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $result = $stmt->execute();
        $stmt->close();

        if (!$result) {
            return false;
        }

        // Step 4: Decrement numGroup in project table
        $this->conn->query("UPDATE project SET numGroup = numGroup - 1 WHERE projectID = $projectID");

        $this->deleteDirectory($subTFilePath);
        $this->deleteDirectory($upFilePath);

        return true;
    }

    //Update group info
    public function updateGroup(int $groupID, string $groupName): bool
    {
        $stmt = $this->conn->prepare("UPDATE projectgroups SET groupName = ? WHERE groupID = ?");
        $stmt->bind_param("si", $groupName, $groupID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    //Transfer leadership of group
    public function transferLeadership(int $groupID, int $newLeaderID): bool
    {
        // Reset all to non-leaders
        $this->conn->query("UPDATE groupmember SET isLeader = 0 WHERE groupID = $groupID");

        // Set new leader
        $stmt = $this->conn->prepare("UPDATE groupmember SET isLeader = 1 WHERE groupID = ? AND userID = ?");
        $stmt->bind_param("ii", $groupID, $newLeaderID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    //Get leader user id using group id
    public function getLeaderId(int $groupID): ?int
    {
        $stmt = $this->conn->prepare("SELECT userID FROM groupmember WHERE groupID = ? AND isLeader = 1");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ? (int) $result['userID'] : null;
    }

    //Get project id using group id
    public function getProjectIdByGroupId(int $groupID): ?int
    {
        $stmt = $this->conn->prepare("SELECT projectID FROM projectgroups WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ? (int) $result['projectID'] : null;
    }

    //Get all the projects the user is registered with using user id
    public function getUserGroupsWithProjects(int $userID): array
    {
        $query = "
        SELECT 
            p.projectID,
            p.title,
            p.deadline,
            p.createdBy,
            pg.groupName,
            pg.groupID,
            pg.submitted
        FROM groupmember gm
        JOIN projectgroups pg ON gm.groupID = pg.groupID
        JOIN project p ON pg.projectID = p.projectID
        WHERE gm.userID = ?
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();
        $projects = [];

        while ($row = $result->fetch_assoc()) {
            // Add progress placeholder for now (e.g. 0)
            $row['progress'] = 0;
            $projects[] = $row;
        }

        $stmt->close();
        return $projects;
    }

    //Get the project group the user is in using user id and project id
    public function getUserGroupInProject(int $userID, int $projectID): ?array
    {
        $query = "
        SELECT pg.* FROM groupmember gm
        JOIN projectgroups pg ON gm.groupID = pg.groupID
        WHERE gm.userID = ? AND pg.projectID = ?
        LIMIT 1
    ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $userID, $projectID);
        $stmt->execute();
        $result = $stmt->get_result();
        $group = $result->fetch_assoc();
        $stmt->close();
        return $group ?: null;
    }

    //Set group submission status as submitted
    public function setSubmitted(int $groupID, bool $submitted): bool
    {
        $stmt = $this->conn->prepare("UPDATE projectgroups SET submitted = ? WHERE groupID = ?");
        $submittedInt = $submitted ? 1 : 0;
        $stmt->bind_param("ii", $submittedInt, $groupID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    //Check if the group have made their submission
    public function getSubmitted(int $groupID): ?bool
    {
        $stmt = $this->conn->prepare("SELECT submitted FROM projectgroups WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return isset($result['submitted']) ? (bool) $result['submitted'] : null;
    }

    //Calulate project progress based on task status
    public function calculateProjectProgress($projectID, $groupID)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/FYP2025/SPAMS/server/models/TaskModel.php';
        $taskModel = new TaskModel($this->conn);
        $tasks = $taskModel->getTasksByProjectAndGroup($projectID, $groupID);

        $total = count($tasks);
        if ($total === 0)
            return 0;

        $completed = 0;
        foreach ($tasks as $task) {
            if ((int) $task['status'] === 2) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }

    //Update submission database
    public function saveSubmission(int $projectID, int $groupID, string $fileURL, string $submissionName, string $displayName): bool
    {
        $stmt = $this->conn->prepare("
        INSERT INTO submission (projectID, groupID, fileURL, submissionName, displayName)
        VALUES (?, ?, ?, ?, ?)
    ");
        $stmt->bind_param("iisss", $projectID, $groupID, $fileURL, $submissionName, $displayName);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    //Get submission made by a group
    public function getSubmissionByGroup(int $projectID, int $groupID): ?array
    {
        $stmt = $this->conn->prepare("
        SELECT * FROM submission
        WHERE projectID = ? AND groupID = ?
        ORDER BY submissionTime DESC
        LIMIT 1
    ");
        $stmt->bind_param("ii", $projectID, $groupID);
        $stmt->execute();
        $result = $stmt->get_result();
        $submission = $result->fetch_assoc();
        $stmt->close();
        return $submission ?: null;
    }

    //Get all submissions by project
    public function getAllSubmissionsByProject(int $projectID): array
    {
        $stmt = $this->conn->prepare("
        SELECT * FROM submission
        WHERE projectID = ?
        ORDER BY submissionTime DESC
    ");
        $stmt->bind_param("i", $projectID);
        $stmt->execute();
        $result = $stmt->get_result();
        $submissions = [];

        while ($row = $result->fetch_assoc()) {
            $submissions[] = $row;
        }

        $stmt->close();
        return $submissions;
    }

    //Delete directory helper function
    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = "$dir/$item";
            if (is_dir($path)) {
                $this->deleteDirectory($path); // Recursive call
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }
}

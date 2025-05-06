<?php

class GroupModel
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function createGroup(int $projectID, string $groupName): int
    {
        $stmt = $this->conn->prepare("INSERT INTO projectgroups (projectID, groupName) VALUES (?, ?)");
        $stmt->bind_param("is", $projectID, $groupName);
        $stmt->execute();
        $groupId = $stmt->insert_id;
        $stmt->close();
        return $groupId;
    }

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

    public function assignUserToGroup(int $groupID, int $userID, bool $isLeader = false): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO groupmember (groupID, userID, isLeader) VALUES (?, ?, ?)");
        $leaderFlag = $isLeader ? 1 : 0;
        $stmt->bind_param("iii", $groupID, $userID, $leaderFlag);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

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

    public function countMembersInGroup(int $groupID): int
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM groupmember WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int) $result['count'];
    }

    public function deleteGroup(int $groupID): bool
    {
        // Delete members first to prevent FK issues
        $this->conn->query("DELETE FROM groupmember WHERE groupID = $groupID");

        $stmt = $this->conn->prepare("DELETE FROM projectgroups WHERE groupID = ?");
        $stmt->bind_param("i", $groupID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    public function updateGroup(int $groupID, string $groupName): bool
    {
        $stmt = $this->conn->prepare("UPDATE projectgroups SET groupName = ? WHERE groupID = ?");
        $stmt->bind_param("si", $groupName, $groupID);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

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

    public function getLeaderId(int $groupID): ?int
    {
        $stmt = $this->conn->prepare("SELECT userID FROM groupmember WHERE groupID = ? AND isLeader = 1");
        $stmt->bind_param("i", $groupID);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result ? (int) $result['userID'] : null;
    }
}

<?php

class ChatModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Create a chatroom
    public function createChatroom($name)
    {
        $stmt = $this->conn->prepare("INSERT INTO chatroom (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            return $this->conn->insert_id; // Return the newly inserted chatID
        }
        return false; // Or null, depending on your preference
    }

    // Add user to a chatroom
    public function addUserToChatroom($chatID, $userID)
    {
        $stmt = $this->conn->prepare("INSERT INTO roommember (chatID, userID) VALUES (?, ?)");
        $stmt->bind_param("ii", $chatID, $userID);
        return $stmt->execute();
    }

    // Send a message
    public function sendMessage($senderID, $chatID, $content)
    {
        $stmt = $this->conn->prepare("INSERT INTO message (senderID, chatID, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $senderID, $chatID, $content);
        return $stmt->execute();
    }

    // Get messages from a chatroom
    public function getMessages($chatID)
    {
        $stmt = $this->conn->prepare("SELECT m.messageID, m.senderID, u.firstName, u.lastName, m.content, m.sentTime
                                      FROM message m
                                      JOIN user u ON m.senderID = u.userID
                                      WHERE m.chatID = ?
                                      ORDER BY m.sentTime ASC");
        $stmt->bind_param("i", $chatID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all chatrooms for a user
    public function getUserChatrooms($userID)
    {
        $stmt = $this->conn->prepare("SELECT c.chatID, c.name
                                      FROM chatroom c
                                      JOIN roommember rm ON c.chatID = rm.chatID
                                      WHERE rm.userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get members of a chatroom
    public function getChatroomMembers($chatID)
    {
        $stmt = $this->conn->prepare("SELECT u.userID, u.username, u.firstName, u.lastName
                                      FROM user u
                                      JOIN roommember rm ON u.userID = rm.userID
                                      WHERE rm.chatID = ?");
        $stmt->bind_param("i", $chatID);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Check if a user is a member of a chatroom
    public function isUserInChatroom($chatID, $userID)
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM roommember WHERE chatID = ? AND userID = ?");
        $stmt->bind_param("ii", $chatID, $userID);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getChatIDByName($name)
    {
        $stmt = $this->conn->prepare("SELECT chatID FROM chatroom WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row['chatID'];
        }
        return null; // Chatroom not found
    }

    public function removeUserFromChatroom($chatID, $userID)
    {
        $stmt = $this->conn->prepare("DELETE FROM roommember WHERE chatID = ? AND userID = ?");
        $stmt->bind_param("ii", $chatID, $userID);
        return $stmt->execute();
    }

    public function chatroomExists($name)
    {
        $stmt = $this->conn->prepare("SELECT 1 FROM chatroom WHERE name = ? LIMIT 1");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->num_rows > 0;
    }

    // Set a message as pinned (and unpin others)
    public function setPinnedMessage($chatID, $messageID)
    {
        // Unpin all existing pinned messages in the chat
        $this->removePinnedMessage($chatID);

        // Pin the selected message
        $stmt = $this->conn->prepare("UPDATE message SET isPinned = 1 WHERE chatID = ? AND messageID = ?");
        $stmt->bind_param("ii", $chatID, $messageID);
        return $stmt->execute();
    }

    // Get the currently pinned message for a chat
    public function getPinnedMessages($chatID)
    {
        $stmt = $this->conn->prepare("SELECT messageID, content FROM message WHERE chatID = ? AND isPinned = 1");
        $stmt->bind_param("i", $chatID);
        $stmt->execute();
        $result = $stmt->get_result();

        $pinnedMessages = [];
        while ($row = $result->fetch_assoc()) {
            $pinnedMessages[] = $row;
        }
        return $pinnedMessages;
    }

    // Remove all pinned messages in a chat
    public function removePinnedMessage($chatID)
    {
        $stmt = $this->conn->prepare("UPDATE message SET isPinned = 0 WHERE chatID = ?");
        $stmt->bind_param("i", $chatID);
        return $stmt->execute();
    }

    // Unpin a specific pinned message in a chat
    public function unsetPinnedMessage($chatID, $messageID)
    {
        $stmt = $this->conn->prepare("UPDATE message SET isPinned = 0 WHERE chatID = ? AND messageID = ?");
        $stmt->bind_param("ii", $chatID, $messageID);
        return $stmt->execute();
    }
}

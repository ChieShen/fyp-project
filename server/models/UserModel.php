<?php
class UserModel
{
    private $conn;

    // Constructor: Establish database connection
    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // Insert a new user
    public function createUser($username, $roleID, $firstName, $lastName, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO user (username, roleID, firstName, lastName, password)
                VALUES (?, ?, ?, ?, ?)";

        try {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                return false;
            }

            $stmt->bind_param("sisss", $username, $roleID, $firstName, $lastName, $hashedPassword);
            $stmt->execute();
            $stmt->close();
            return true;

        } catch (mysqli_sql_exception $e) {

            return false;
        }
    }

    // Get user by username
    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return false;

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
        return $user;
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM user WHERE userID = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt)
            return false;

        $stmt->bind_param("s", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        $stmt->close();
        return $user;
    }

    // Validate login
    public function validateLogin($username, $password)
    {
        $user = $this->getUserByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function getConnection(): mysqli
    {
        return $this->conn;
    }
}
?>
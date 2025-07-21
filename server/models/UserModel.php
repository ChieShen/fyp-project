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

    //Get user details by user id
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

    // Update user's profile information
    public function updateUserProfile($userID, $firstName, $lastName, $curPass = null, $newPass = null)
    {
        // Get current user to check password
        $user = $this->getUserById($userID);
        if (!$user)
            return false;

        // If any password is provided, validate and update password
        if ($curPass !== null && $newPass !== null && $curPass !== "" && $newPass !== "") {
            // Validate current password
            if (!password_verify($curPass, $user['password'])) {
                return "wrong_password";
            }

            // Hash the new password
            $hashedNewPassword = password_hash($newPass, PASSWORD_BCRYPT);

            // Update name and password
            $sql = "UPDATE user SET firstName = ?, lastName = ?, password = ? WHERE userID = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt)
                return false;

            $stmt->bind_param("sssi", $firstName, $lastName, $hashedNewPassword, $userID);
        } else {
            // Update only name
            $sql = "UPDATE user SET firstName = ?, lastName = ? WHERE userID = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt)
                return false;

            $stmt->bind_param("ssi", $firstName, $lastName, $userID);
        }

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}
?>
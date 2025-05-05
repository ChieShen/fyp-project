<?php
class Database {
    private $host = 'localhost';
    private $db = 'spams';
    private $user = 'root';
    private $pass = '';
    private $conn;

    public function connect(): mysqli {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}

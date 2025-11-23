<?php
class Database {
    private $host = "localhost";
    private $port = "3307";
    private $db_name = "dibbys_restaurant_db";
    private $username = "root";
    private $password = "CJYL@lmc_26";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, 
                $this->username, 
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo json_encode([
                "success" => false,
                "message" => "Database connection failed: " . $exception->getMessage()
            ]);
            exit;
        }
        return $this->conn;
    }
}
?>
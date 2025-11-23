<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Check if user exists
        $query = "SELECT id FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->email]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "success" => false,
                "message" => "User already exists with this email"
            ]);
            exit;
        }
        
        // Create user
        $query = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $password_hash = password_hash($data->password, PASSWORD_DEFAULT);
        
        if ($stmt->execute([$data->name, $data->email, $password_hash])) {
            echo json_encode([
                "success" => true,
                "message" => "User registered successfully",
                "user_id" => $db->lastInsertId()
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Unable to register user"
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields: name, email, password"
    ]);
}
?>
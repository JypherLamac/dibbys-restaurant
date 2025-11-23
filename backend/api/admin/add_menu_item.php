<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// 🔒 SECURITY CHECK MUST BE AT THE TOP!
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(["success" => false, "message" => "Admin access required"]);
    exit; // STOP immediately if not admin
}

include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->price) && !empty($data->category_id)) {
    
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "INSERT INTO menu_items (category_id, name, description, price, image_url) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $data->category_id,
            $data->name,
            $data->description ?? '',
            $data->price,
            $data->image_url ?? ''
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Menu item added successfully",
            "item_id" => $db->lastInsertId()
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error adding menu item: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields: name, price, category_id"
    ]);
}
?>
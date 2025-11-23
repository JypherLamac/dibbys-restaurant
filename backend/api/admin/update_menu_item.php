<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(["success" => false, "message" => "Admin access required"]);
    exit;
}

include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->id) && (!empty($data->name) || !empty($data->price))) {
    
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Build dynamic update query based on provided fields
        $update_fields = [];
        $params = [];

        if (!empty($data->name)) {
            $update_fields[] = "name = ?";
            $params[] = $data->name;
        }
        if (!empty($data->description)) {
            $update_fields[] = "description = ?";
            $params[] = $data->description;
        }
        if (!empty($data->price)) {
            $update_fields[] = "price = ?";
            $params[] = $data->price;
        }
        if (!empty($data->category_id)) {
            $update_fields[] = "category_id = ?";
            $params[] = $data->category_id;
        }
        if (isset($data->is_available)) {
            $update_fields[] = "is_available = ?";
            $params[] = $data->is_available ? 1 : 0;
        }

        $params[] = $data->id; // For WHERE clause

        $query = "UPDATE menu_items SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Menu item updated successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No changes made or item not found"
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error updating menu item: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing item ID and update fields"
    ]);
}
?>
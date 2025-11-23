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

if (!empty($data->id)) {
    
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "success" => true,
                "message" => "Menu item deleted successfully"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Menu item not found"
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error deleting menu item: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing menu item ID"
    ]);
}
?>
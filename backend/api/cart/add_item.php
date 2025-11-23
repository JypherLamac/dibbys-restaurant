<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->user_id) && !empty($data->menu_item_id)) {
    
    try {
        $database = new Database();
        $db = $database->getConnection();

        // Check if item exists and is available
        $check_item = "SELECT id, price FROM menu_items WHERE id = ? AND is_available = 1";
        $stmt = $db->prepare($check_item);
        $stmt->execute([$data->menu_item_id]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode([
                "success" => false,
                "message" => "Menu item not available"
            ]);
            exit;
        }

        // Check if item already in cart
        $check_cart = "SELECT id, quantity FROM cart_items 
                      WHERE user_id = ? AND menu_item_id = ?";
        $stmt = $db->prepare($check_cart);
        $stmt->execute([$data->user_id, $data->menu_item_id]);
        
        if ($stmt->rowCount() > 0) {
            // Update quantity
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_quantity = $cart_item['quantity'] + ($data->quantity ?? 1);
            
            $update_query = "UPDATE cart_items SET quantity = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->execute([$new_quantity, $cart_item['id']]);
            
            $message = "Cart updated";
        } else {
            // Add new item
            $insert_query = "INSERT INTO cart_items (user_id, menu_item_id, quantity, special_instructions) 
                            VALUES (?, ?, ?, ?)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([
                $data->user_id, 
                $data->menu_item_id, 
                $data->quantity ?? 1,
                $data->special_instructions ?? ''
            ]);
            
            $message = "Item added to cart";
        }
        
        echo json_encode([
            "success" => true,
            "message" => $message
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing user_id or menu_item_id"
    ]);
}
?>
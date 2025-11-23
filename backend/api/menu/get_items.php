<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "
        SELECT mc.id as category_id, mc.name as category_name, 
               mi.id, mi.name, mi.description, mi.price, mi.image_url, mi.is_available
        FROM menu_categories mc
        LEFT JOIN menu_items mi ON mc.id = mi.category_id AND mi.is_available = 1
        ORDER BY mc.display_order, mi.name
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $categories = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $category_id = $row['category_id'];
        
        if (!isset($categories[$category_id])) {
            $categories[$category_id] = [
                'id' => $row['category_id'],
                'name' => $row['category_name'],
                'items' => []
            ];
        }
        
        if ($row['id']) {
            $categories[$category_id]['items'][] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => (float)$row['price'],
                'image_url' => $row['image_url'],
                'is_available' => (bool)$row['is_available']
            ];
        }
    }

    echo json_encode([
        "success" => true,
        "categories" => array_values($categories)
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
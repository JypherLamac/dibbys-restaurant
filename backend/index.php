<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

echo json_encode([
    "success" => true,
    "message" => "🍽️ Dibby's Restaurant API is running!",
    "restaurant" => "Dibby's Restaurant",
    "endpoints" => [
        "GET /api/menu/get_items.php" => "Get all menu items",
        "POST /api/auth/register.php" => "Register new customer",
        "POST /api/cart/add_item.php" => "Add item to cart",
        "POST /api/orders/create_order.php" => "Create new order",
        "POST /api/feedback/submit_feedback.php" => "Submit feedback",
        "POST /api/admin/add_menu_item.php" => "Add new menu item (Admin)",
        "POST /api/admin/update_menu_item.php" => "Update menu item (Admin)",
        "POST /api/admin/delete_menu_item.php" => "Delete menu item (Admin)"
    ],
    "test_pages" => [
        "test_page" => "http://localhost/dibbys-restaurant/test.html",
        "admin_panel" => "http://localhost/dibbys-restaurant/admin.html"
    ]
]);
?>
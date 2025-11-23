<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

// Simple hardcoded admin login - replace with database check later
$admin_username = "admin";
$admin_password = "dibbys123"; // Change this!

if (!empty($data->username) && !empty($data->password)) {
    session_start();
    
    if ($data->username === $admin_username && $data->password === $admin_password) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_username'] = $data->username;
        
        echo json_encode([
            "success" => true,
            "message" => "Admin login successful"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Invalid admin credentials"
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Username and password required"
    ]);
}
?>
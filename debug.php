<?php
// debug.php - Test basic PHP and database
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

echo json_encode([
    "php_working" => true,
    "timestamp" => date('Y-m-d H:i:s'),
    "test" => "Dibby's Restaurant Debug"
]);
?>
<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->rating) && !empty($data->comment)) {
    
    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = "INSERT INTO feedbacks (user_id, rating, comment) VALUES (?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            $data->user_id ?? 1, // Default to user 1 if not provided
            $data->rating,
            $data->comment
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Feedback submitted successfully",
            "feedback_id" => $db->lastInsertId()
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error submitting feedback: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields: rating and comment"
    ]);
}
?>
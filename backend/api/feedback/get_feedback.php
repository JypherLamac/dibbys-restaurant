<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get all feedbacks with user info
    $query = "
        SELECT f.id, f.rating, f.comment, f.created_at, u.name as user_name, u.email
        FROM feedbacks f
        LEFT JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $feedbacks = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $feedbacks[] = [
            'id' => $row['id'],
            'user_name' => $row['user_name'] ?? 'Anonymous',
            'user_email' => $row['email'] ?? '',
            'rating' => (int)$row['rating'],
            'comment' => $row['comment'],
            'date' => $row['created_at']
        ];
    }

    // Calculate statistics
    $total = count($feedbacks);
    $average = $total > 0 ? array_sum(array_column($feedbacks, 'rating')) / $total : 0;
    $five_star = array_filter($feedbacks, function($f) { return $f['rating'] === 5; });
    
    // This month count
    $this_month = array_filter($feedbacks, function($f) {
        $feedbackDate = new DateTime($f['date']);
        $now = new DateTime();
        return $feedbackDate->format('Y-m') === $now->format('Y-m');
    });

    echo json_encode([
        "success" => true,
        "feedbacks" => $feedbacks,
        "statistics" => [
            "total_reviews" => $total,
            "average_rating" => round($average, 1),
            "five_star_count" => count($five_star),
            "this_month_count" => count($this_month)
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error loading feedbacks: " . $e->getMessage()
    ]);
}
?>
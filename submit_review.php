<?php
// submit_review.php - Process Review Form Submission
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acc_id = isset($_POST['accommodation_id']) ? intval($_POST['accommodation_id']) : 0;
    $user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($acc_id > 0 && !empty($user_name) && !empty($comment)) {
        // Save review as Approved by default or Pending
        $stmt = $pdo->prepare("
            INSERT INTO reviews (accommodation_id, user_name, rating, comment, status) 
            VALUES (?, ?, ?, ?, 'Approved')
        ");
        $stmt->execute([$acc_id, $user_name, $rating, $comment]);

        // Recalculate average rating for accommodation
        $stmtAvg = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE accommodation_id = ? AND status = 'Approved'");
        $stmtAvg->execute([$acc_id]);
        $newAvg = $stmtAvg->fetch()['avg_rating'];
        if ($newAvg) {
            $stmtUp = $pdo->prepare("UPDATE accommodations SET rating = ? WHERE id = ?");
            $stmtUp->execute([round($newAvg, 1), $acc_id]);
        }

        header("Location: /berot/details.php?id={$acc_id}&review_success=1#reviews-section");
        exit();
    }
}

header("Location: /berot/accommodations.php");
exit();

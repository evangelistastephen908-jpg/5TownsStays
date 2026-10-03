<?php
// submit_inquiry.php - Process Inquiry Form Submission
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acc_id = isset($_POST['accommodation_id']) ? intval($_POST['accommodation_id']) : 0;
    $user_name = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $user_email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
    $user_phone = isset($_POST['user_phone']) ? trim($_POST['user_phone']) : '';
    $check_in = isset($_POST['check_in']) ? trim($_POST['check_in']) : '';
    $check_out = isset($_POST['check_out']) ? trim($_POST['check_out']) : '';
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 1;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($acc_id > 0 && !empty($user_name) && !empty($user_email) && !empty($user_phone)) {
        $stmt = $pdo->prepare("
            INSERT INTO inquiries (user_name, user_email, user_phone, accommodation_id, check_in, check_out, guests, message, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([$user_name, $user_email, $user_phone, $acc_id, $check_in, $check_out, $guests, $message]);
        
        $inquiryId = $pdo->lastInsertId();

        // Redirect back with confirmation message
        header("Location: /berot/details.php?id={$acc_id}&inquiry_success=1&ref=INQ-" . sprintf('%04d', $inquiryId));
        exit();
    }
}

header("Location: /berot/accommodations.php");
exit();

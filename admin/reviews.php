<?php
// admin/reviews.php - Manage & Moderate Tourist Reviews
$admin_page_title = "Reviews Moderation";
$admin_active = "reviews";

require_once __DIR__ . '/header.php';

// Handle Moderation Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $revId = intval($_GET['id']);
    $act = $_GET['action'];

    if ($act === 'approve') {
        $pdo->prepare("UPDATE reviews SET status = 'Approved' WHERE id = ?")->execute([$revId]);
    } elseif ($act === 'reject') {
        $pdo->prepare("UPDATE reviews SET status = 'Rejected' WHERE id = ?")->execute([$revId]);
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$revId]);
    }

    // Recalculate rating for affected accommodation
    $stmtAccId = $pdo->prepare("SELECT accommodation_id FROM reviews WHERE id = ?");
    $stmtAccId->execute([$revId]);
    $accRow = $stmtAccId->fetch();
    if ($accRow) {
        $targetAcc = $accRow['accommodation_id'];
        $stmtAvg = $pdo->prepare("SELECT AVG(rating) as avg_r FROM reviews WHERE accommodation_id = ? AND status = 'Approved'");
        $stmtAvg->execute([$targetAcc]);
        $avgVal = $stmtAvg->fetch()['avg_r'];
        if ($avgVal) {
            $pdo->prepare("UPDATE accommodations SET rating = ? WHERE id = ?")->execute([round($avgVal, 1), $targetAcc]);
        }
    }

    header("Location: /berot/admin/reviews.php?msg=updated");
    exit();
}

// Fetch Reviews
$stmtR = $pdo->query("
    SELECT r.*, a.name as acc_name, a.municipality 
    FROM reviews r 
    JOIN accommodations a ON r.accommodation_id = a.id 
    ORDER BY r.created_at DESC
");
$reviews = $stmtR->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-comments" style="color:#059669;"></i> Tourist Reviews & Feedback Moderation</h3>
        <span class="badge-status active"><?php echo count($reviews); ?> Reviews</span>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-check-circle"></i> Review status updated and accommodation ratings recalculated.
        </div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Target Accommodation</th>
                <th>Reviewer Name</th>
                <th>Star Rating</th>
                <th>Review Comment</th>
                <th>Status</th>
                <th>Submitted Date</th>
                <th>Moderation Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; color: #64748B;">No tourist reviews recorded in system yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($reviews as $rev): ?>
                    <tr>
                        <td>#<?php echo $rev['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($rev['acc_name']); ?></strong><br>
                            <small class="text-muted">(<?php echo htmlspecialchars($rev['municipality']); ?>)</small>
                        </td>
                        <td><strong><?php echo htmlspecialchars($rev['user_name']); ?></strong></td>
                        <td>
                            <span style="color: #F59E0B; font-weight:700;"><i class="fa-solid fa-star"></i> <?php echo $rev['rating']; ?>/5</span>
                        </td>
                        <td>
                            <p style="font-size:0.9rem; max-width: 320px; line-height: 1.4;">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                        </td>
                        <td>
                            <span class="badge-status <?php echo strtolower($rev['status']); ?>">
                                <?php echo htmlspecialchars($rev['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></td>
                        <td>
                            <div style="display: flex; gap: 6px;">
                                <?php if ($rev['status'] !== 'Approved'): ?>
                                    <a href="/berot/admin/reviews.php?action=approve&id=<?php echo $rev['id']; ?>" class="btn-sm btn-success" title="Approve Review">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </a>
                                <?php endif; ?>
                                <?php if ($rev['status'] !== 'Rejected'): ?>
                                    <a href="/berot/admin/reviews.php?action=reject&id=<?php echo $rev['id']; ?>" class="btn-sm btn-warning" title="Reject Review" style="background:#F59E0B;">
                                        <i class="fa-solid fa-ban"></i> Reject
                                    </a>
                                <?php endif; ?>
                                <a href="/berot/admin/reviews.php?action=delete&id=<?php echo $rev['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Permanently remove this review?');" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

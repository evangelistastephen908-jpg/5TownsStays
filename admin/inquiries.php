<?php
// admin/inquiries.php - Manage Tourist Inquiries
$admin_page_title = "Guest Inquiries Management";
$admin_active = "inquiries";

require_once __DIR__ . '/header.php';

// Handle Action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $inqId = intval($_GET['id']);
    $act = $_GET['action'];

    if ($act === 'status' && isset($_GET['status'])) {
        $newStatus = $_GET['status'];
        $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?")->execute([$newStatus, $inqId]);
        header("Location: /berot/admin/inquiries.php?msg=updated");
        exit();
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM inquiries WHERE id = ?")->execute([$inqId]);
        header("Location: /berot/admin/inquiries.php?msg=deleted");
        exit();
    }
}

// Fetch Inquiries
$stmtInq = $pdo->query("
    SELECT i.*, a.name as acc_name, a.municipality, a.contact_number as host_phone 
    FROM inquiries i 
    JOIN accommodations a ON i.accommodation_id = a.id 
    ORDER BY i.created_at DESC
");
$inquiries = $stmtInq->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-paper-plane" style="color:#0288D1;"></i> Tourist Inquiry Submissions</h3>
        <span class="badge-status active"><?php echo count($inquiries); ?> Total Inquiries</span>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-check-circle"></i> Inquiry record status updated.
        </div>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Ref Code</th>
                <th>Tourist / Guest Details</th>
                <th>Accommodation Target</th>
                <th>Dates & Guests</th>
                <th>Message / Special Request</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($inquiries)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748B;">No tourist inquiries recorded in the system yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($inquiries as $inq): ?>
                    <tr>
                        <td><strong>INQ-<?php echo sprintf('%04d', $inq['id']); ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($inq['user_name']); ?></strong><br>
                            <small><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($inq['user_email']); ?></small><br>
                            <small><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($inq['user_phone']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($inq['acc_name']); ?></strong><br>
                            <small class="text-muted"><?php echo htmlspecialchars($inq['municipality']); ?></small>
                        </td>
                        <td>
                            <strong><?php echo date('M d, Y', strtotime($inq['check_in'])); ?></strong> to<br>
                            <strong><?php echo date('M d, Y', strtotime($inq['check_out'])); ?></strong><br>
                            <small><i class="fa-solid fa-user-group"></i> <?php echo $inq['guests']; ?> Guest(s)</small>
                        </td>
                        <td>
                            <p style="font-size:0.88rem; max-width: 280px; line-height: 1.4;">"<?php echo htmlspecialchars($inq['message']); ?>"</p>
                        </td>
                        <td>
                            <span class="badge-status <?php echo strtolower($inq['status']); ?>">
                                <?php echo htmlspecialchars($inq['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <?php if ($inq['status'] === 'Pending'): ?>
                                    <a href="/berot/admin/inquiries.php?action=status&id=<?php echo $inq['id']; ?>&status=Contacted" class="btn-sm btn-primary">
                                        Mark Contacted
                                    </a>
                                <?php elseif ($inq['status'] === 'Contacted'): ?>
                                    <a href="/berot/admin/inquiries.php?action=status&id=<?php echo $inq['id']; ?>&status=Closed" class="btn-sm btn-success">
                                        Mark Closed
                                    </a>
                                <?php endif; ?>
                                <a href="/berot/admin/inquiries.php?action=delete&id=<?php echo $inq['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this inquiry record?');">
                                    Delete
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

<?php
// admin/dashboard.php - Admin Dashboard Overview
$admin_page_title = "Dashboard Overview";
$admin_active = "dashboard";

require_once __DIR__ . '/header.php';

// Fetch System Statistics
$totalAcc = $pdo->query("SELECT COUNT(*) FROM accommodations")->fetchColumn();
$totalMunis = $pdo->query("SELECT COUNT(DISTINCT municipality) FROM accommodations")->fetchColumn();
$totalInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$pendingInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'Pending'")->fetchColumn();
$totalReviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$pendingReviews = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'Pending'")->fetchColumn();

// Fetch Recent Inquiries
$stmtInq = $pdo->query("
    SELECT i.*, a.name as acc_name, a.municipality 
    FROM inquiries i 
    JOIN accommodations a ON i.accommodation_id = a.id 
    ORDER BY i.created_at DESC LIMIT 5
");
$recentInquiries = $stmtInq->fetchAll();

// Fetch Recent Reviews
$stmtRev = $pdo->query("
    SELECT r.*, a.name as acc_name 
    FROM reviews r 
    JOIN accommodations a ON r.accommodation_id = a.id 
    ORDER BY r.created_at DESC LIMIT 5
");
$recentReviews = $stmtRev->fetchAll();
?>

<!-- Statistics Widgets -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: #E0F7FA; color: #0288D1;">
            <i class="fa-solid fa-hotel"></i>
        </div>
        <div>
            <div class="stat-val"><?php echo $totalAcc; ?></div>
            <div class="stat-lbl">Total Accommodations</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: #FEF3C7; color: #D97706;">
            <i class="fa-solid fa-location-dot"></i>
        </div>
        <div>
            <div class="stat-val"><?php echo $totalMunis; ?> / 5</div>
            <div class="stat-lbl">Towns Covered</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: #E0F2FE; color: #0369A1;">
            <i class="fa-solid fa-paper-plane"></i>
        </div>
        <div>
            <div class="stat-val"><?php echo $totalInquiries; ?> <small style="font-size:0.8rem; font-weight:normal; color:#E65100;">(<?php echo $pendingInquiries; ?> pending)</small></div>
            <div class="stat-lbl">Guest Inquiries</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper" style="background: #D1FAE5; color: #059669;">
            <i class="fa-solid fa-star"></i>
        </div>
        <div>
            <div class="stat-val"><?php echo $totalReviews; ?> <small style="font-size:0.8rem; font-weight:normal; color:#92400E;">(<?php echo $pendingReviews; ?> new)</small></div>
            <div class="stat-lbl">Tourist Reviews</div>
        </div>
    </div>
</div>

<!-- Recent Inquiries Section -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-paper-plane" style="color:#0288D1;"></i> Recent Guest Inquiries</h3>
        <a href="/berot/admin/inquiries.php" class="btn-sm btn-primary">View All Inquiries</a>
    </div>

    <?php if (empty($recentInquiries)): ?>
        <p style="color: #64748B;">No inquiries received yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Guest Name</th>
                    <th>Accommodation</th>
                    <th>Dates</th>
                    <th>Guests</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentInquiries as $inq): ?>
                    <tr>
                        <td><strong>INQ-<?php echo sprintf('%04d', $inq['id']); ?></strong></td>
                        <td>
                            <strong><?php echo htmlspecialchars($inq['user_name']); ?></strong><br>
                            <small style="color:#64748B;"><?php echo htmlspecialchars($inq['user_email']); ?></small>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($inq['acc_name']); ?><br>
                            <small class="text-muted">(<?php echo htmlspecialchars($inq['municipality']); ?>)</small>
                        </td>
                        <td><?php echo date('M d', strtotime($inq['check_in'])); ?> - <?php echo date('M d', strtotime($inq['check_out'])); ?></td>
                        <td><?php echo $inq['guests']; ?> Guests</td>
                        <td>
                            <span class="badge-status <?php echo strtolower($inq['status']); ?>">
                                <?php echo htmlspecialchars($inq['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="/berot/admin/inquiries.php?id=<?php echo $inq['id']; ?>" class="btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Recent Reviews Moderation -->
<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-comments" style="color:#059669;"></i> Recent Tourist Reviews</h3>
        <a href="/berot/admin/reviews.php" class="btn-sm btn-primary">Manage Reviews</a>
    </div>

    <?php if (empty($recentReviews)): ?>
        <p style="color: #64748B;">No reviews submitted yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Accommodation</th>
                    <th>Reviewer</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentReviews as $rev): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($rev['acc_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($rev['user_name']); ?></td>
                        <td>
                            <span style="color: #F59E0B; font-weight:700;"><i class="fa-solid fa-star"></i> <?php echo $rev['rating']; ?>/5</span>
                        </td>
                        <td>"<?php echo htmlspecialchars(substr($rev['comment'], 0, 60)); ?>..."</td>
                        <td>
                            <span class="badge-status <?php echo strtolower($rev['status']); ?>">
                                <?php echo htmlspecialchars($rev['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

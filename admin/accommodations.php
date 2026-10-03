<?php
// admin/accommodations.php - Manage Accommodations (CRUD)
$admin_page_title = "Manage Accommodations";
$admin_active = "accommodations";

require_once __DIR__ . '/header.php';

// Handle Delete Request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delId = intval($_GET['id']);
    if ($delId > 0) {
        $stmtDel = $pdo->prepare("DELETE FROM accommodations WHERE id = ?");
        $stmtDel->execute([$delId]);
        header("Location: /berot/admin/accommodations.php?msg=deleted");
        exit();
    }
}

// Fetch Accommodations
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql = "SELECT * FROM accommodations WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE ? OR municipality LIKE ? OR type LIKE ?)";
    $st = "%{$search}%";
    $params = [$st, $st, $st];
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$accommodations = $stmt->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-hotel" style="color:#0288D1;"></i> Accommodation Directory Records</h3>
        <a href="/berot/admin/accommodation_form.php" class="btn-sm btn-primary" style="padding: 10px 18px; font-size: 0.9rem;">
            <i class="fa-solid fa-plus"></i> Add New Accommodation
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div style="background: #FEE2E2; color: #991B1B; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-check-circle"></i> Accommodation record successfully deleted.
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
        <div style="background: #D1FAE5; color: #065F46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-check-circle"></i> Accommodation record successfully saved!
        </div>
    <?php endif; ?>

    <!-- Search Bar Filter -->
    <div style="margin-bottom: 20px;">
        <form action="/berot/admin/accommodations.php" method="GET" style="display: flex; gap: 10px; max-width: 400px;">
            <input type="text" name="q" class="form-control" placeholder="Search by name, town, or type..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-sm btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Accommodation Name</th>
                <th>Municipality</th>
                <th>Type</th>
                <th>Starting Price</th>
                <th>Rating</th>
                <th>Featured</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accommodations as $acc): ?>
                <tr>
                    <td>#<?php echo $acc['id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($acc['name']); ?></strong><br>
                        <small style="color: #64748B;"><?php echo htmlspecialchars($acc['address']); ?></small>
                    </td>
                    <td><span class="badge-status active"><?php echo htmlspecialchars($acc['municipality']); ?></span></td>
                    <td><?php echo htmlspecialchars($acc['type']); ?></td>
                    <td><strong>₱<?php echo number_format($acc['price_start'], 2); ?></strong></td>
                    <td><i class="fa-solid fa-star" style="color:#F59E0B;"></i> <?php echo number_format($acc['rating'], 1); ?></td>
                    <td>
                        <?php if ($acc['featured']): ?>
                            <span class="badge-status active">Featured</span>
                        <?php else: ?>
                            <span style="color:#94A3B8;">Standard</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge-status <?php echo strtolower($acc['status']); ?>">
                            <?php echo htmlspecialchars($acc['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="/berot/admin/accommodation_form.php?id=<?php echo $acc['id']; ?>" class="btn-sm btn-primary" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <a href="/berot/admin/rooms.php?acc_id=<?php echo $acc['id']; ?>" class="btn-sm btn-warning" title="Manage Rooms" style="background:#F59E0B;">
                                <i class="fa-solid fa-bed"></i> Rooms
                            </a>
                            <a href="/berot/admin/accommodations.php?action=delete&id=<?php echo $acc['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this accommodation?');" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

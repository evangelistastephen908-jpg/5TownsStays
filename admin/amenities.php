<?php
// admin/amenities.php - Manage Amenities & Municipalities
$admin_page_title = "Amenities & Municipalities";
$admin_active = "amenities";

require_once __DIR__ . '/header.php';

// Add Amenity Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_amenity'])) {
    $name = trim($_POST['name']);
    $icon = trim($_POST['icon']);
    if (!empty($name) && !empty($icon)) {
        $stmtIns = $pdo->prepare("INSERT INTO amenities (name, icon) VALUES (?, ?)");
        $stmtIns->execute([$name, $icon]);
        header("Location: /berot/admin/amenities.php?msg=added");
        exit();
    }
}

$amenities = $pdo->query("SELECT * FROM amenities ORDER BY name ASC")->fetchAll();
$municipalities = $pdo->query("SELECT * FROM municipalities ORDER BY name ASC")->fetchAll();
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Amenities Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-list-check" style="color:#0288D1;"></i> Managed Amenities</h3>
        </div>

        <form action="/berot/admin/amenities.php" method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="hidden" name="add_amenity" value="1">
            <input type="text" name="name" class="form-control" placeholder="Amenity Name (e.g. Helipad)" required>
            <input type="text" name="icon" class="form-control" placeholder="FontAwesome Icon (e.g. fa-helicopter)" required>
            <button type="submit" class="btn-sm btn-primary" style="white-space: nowrap;"><i class="fa-solid fa-plus"></i> Add</button>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Amenity Name</th>
                    <th>Class</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($amenities as $am): ?>
                    <tr>
                        <td style="font-size: 1.2rem; color: #0288D1;"><i class="fa-solid <?php echo htmlspecialchars($am['icon']); ?>"></i></td>
                        <td><strong><?php echo htmlspecialchars($am['name']); ?></strong></td>
                        <td><code><?php echo htmlspecialchars($am['icon']); ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Municipalities Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-location-dot" style="color:#D97706;"></i> CarCanMadCarLan Towns</h3>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Municipality</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($municipalities as $m): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($m['name']); ?></strong></td>
                        <td><small style="color:#64748B;"><?php echo htmlspecialchars($m['description']); ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

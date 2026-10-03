<?php
// admin/rooms.php - Manage Accommodation Rooms & Rates
$admin_page_title = "Manage Rooms & Rates";
$admin_active = "rooms";

require_once __DIR__ . '/header.php';

$acc_id = isset($_GET['acc_id']) ? intval($_GET['acc_id']) : 0;

// Handle Delete Room Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['room_id'])) {
    $roomId = intval($_GET['room_id']);
    $pdo->prepare("DELETE FROM rooms WHERE id = ?")->execute([$roomId]);
    header("Location: /berot/admin/rooms.php?acc_id={$acc_id}&msg=deleted");
    exit();
}

// Handle Add / Edit Room
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetAccId = intval($_POST['accommodation_id']);
    $room_name = trim($_POST['room_name']);
    $capacity = intval($_POST['capacity']);
    $price = floatval($_POST['price']);
    $availability = trim($_POST['availability']);

    if ($targetAccId > 0 && !empty($room_name)) {
        $stmtIns = $pdo->prepare("INSERT INTO rooms (accommodation_id, room_name, capacity, price, availability) VALUES (?, ?, ?, ?, ?)");
        $stmtIns->execute([$targetAccId, $room_name, $capacity, $price, $availability]);
        header("Location: /berot/admin/rooms.php?acc_id={$targetAccId}&msg=added");
        exit();
    }
}

// Fetch Accommodations List
$accommodations = $pdo->query("SELECT id, name, municipality FROM accommodations ORDER BY name ASC")->fetchAll();

if ($acc_id == 0 && !empty($accommodations)) {
    $acc_id = $accommodations[0]['id'];
}

// Fetch Rooms for selected accommodation
$rooms = [];
if ($acc_id > 0) {
    $stmtR = $pdo->prepare("SELECT * FROM rooms WHERE accommodation_id = ? ORDER BY price ASC");
    $stmtR->execute([$acc_id]);
    $rooms = $stmtR->fetchAll();
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-bed" style="color:#0288D1;"></i> Room Matrix & Rates</h3>
    </div>

    <!-- Select Accommodation Filter -->
    <div style="margin-bottom: 24px;">
        <form action="/berot/admin/rooms.php" method="GET" style="display: flex; gap: 12px; align-items: center;">
            <label style="font-weight: 700;">Select Accommodation:</label>
            <select name="acc_id" class="form-control" style="max-width: 360px;" onchange="this.form.submit()">
                <?php foreach ($accommodations as $a): ?>
                    <option value="<?php echo $a['id']; ?>" <?php echo $a['id'] == $acc_id ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($a['name']); ?> (<?php echo htmlspecialchars($a['municipality']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Add Room Form -->
    <div style="background: #F8FAFC; padding: 20px; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 24px;">
        <h4 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; margin-bottom: 14px;"><i class="fa-solid fa-plus-circle" style="color:#0288D1;"></i> Add New Room Option</h4>
        <form action="/berot/admin/rooms.php" method="POST" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 140px; gap: 12px; align-items: flex-end;">
            <input type="hidden" name="accommodation_id" value="<?php echo $acc_id; ?>">

            <div class="form-group">
                <label>Room Name</label>
                <input type="text" name="room_name" class="form-control" required placeholder="e.g. Ocean Deluxe Suite">
            </div>

            <div class="form-group">
                <label>Max Guests</label>
                <input type="number" name="capacity" class="form-control" required value="2" min="1">
            </div>

            <div class="form-group">
                <label>Nightly Rate (₱)</label>
                <input type="number" step="50" name="price" class="form-control" required value="1500.00">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="availability" class="form-control">
                    <option value="Available">Available</option>
                    <option value="Fully Booked">Fully Booked</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>

            <button type="submit" class="btn-sm btn-primary" style="height: 48px; justify-content: center;">
                <i class="fa-solid fa-plus"></i> Add Room
            </button>
        </form>
    </div>

    <!-- Existing Rooms Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Room Type Name</th>
                <th>Guest Capacity</th>
                <th>Nightly Rate</th>
                <th>Availability Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rooms)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748B;">No specific room types configured yet for this stay.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rooms as $rm): ?>
                    <tr>
                        <td>#<?php echo $rm['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($rm['room_name']); ?></strong></td>
                        <td><i class="fa-solid fa-user-group"></i> <?php echo $rm['capacity']; ?> Guests</td>
                        <td><strong style="color: #0288D1;">₱<?php echo number_format($rm['price'], 2); ?></strong></td>
                        <td>
                            <span class="badge-status <?php echo strtolower(str_replace(' ', '', $rm['availability'])) == 'available' ? 'active' : 'inactive'; ?>">
                                <?php echo htmlspecialchars($rm['availability']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="/berot/admin/rooms.php?action=delete&room_id=<?php echo $rm['id']; ?>&acc_id=<?php echo $acc_id; ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this room option?');">
                                <i class="fa-solid fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

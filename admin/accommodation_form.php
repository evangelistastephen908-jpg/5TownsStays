<?php
// admin/accommodation_form.php - Add / Edit Accommodation Form
$admin_page_title = "Add Accommodation";
$admin_active = "accommodations";

require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$acc = [
    'id' => 0,
    'name' => '',
    'description' => '',
    'type' => 'Hotel',
    'municipality' => 'Cantilan',
    'address' => '',
    'contact_number' => '',
    'email' => '',
    'website' => '',
    'price_start' => 1500.00,
    'rating' => 4.5,
    'featured' => 0,
    'status' => 'Active'
];
$selectedAmenities = [];
$photoUrl = '';

if ($id > 0) {
    $admin_page_title = "Edit Accommodation";
    $stmt = $pdo->prepare("SELECT * FROM accommodations WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $acc = $existing;
    }

    // Fetch existing amenities
    $stmtA = $pdo->prepare("SELECT amenity_id FROM accommodation_amenities WHERE accommodation_id = ?");
    $stmtA->execute([$id]);
    $selectedAmenities = $stmtA->fetchAll(PDO::FETCH_COLUMN);

    // Fetch primary photo
    $stmtP = $pdo->prepare("SELECT image_url FROM photos WHERE accommodation_id = ? AND is_primary = 1 LIMIT 1");
    $stmtP->execute([$id]);
    $ph = $stmtP->fetch();
    if ($ph) {
        $photoUrl = $ph['image_url'];
    }
}

// Fetch All Amenities
$allAmenities = $pdo->query("SELECT * FROM amenities ORDER BY name ASC")->fetchAll();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $type = trim($_POST['type']);
    $municipality = trim($_POST['municipality']);
    $address = trim($_POST['address']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $website = trim($_POST['website']);
    $price_start = floatval($_POST['price_start']);
    $rating = floatval($_POST['rating']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = trim($_POST['status']);
    $amenities_input = isset($_POST['amenities']) ? $_POST['amenities'] : [];
    $main_photo = trim($_POST['photo_url']);

    if (!empty($name) && !empty($description) && !empty($address)) {
        if ($id > 0) {
            // Update
            $stmtUp = $pdo->prepare("
                UPDATE accommodations 
                SET name = ?, description = ?, type = ?, municipality = ?, address = ?, contact_number = ?, email = ?, website = ?, price_start = ?, rating = ?, featured = ?, status = ? 
                WHERE id = ?
            ");
            $stmtUp->execute([$name, $description, $type, $municipality, $address, $contact_number, $email, $website, $price_start, $rating, $featured, $status, $id]);
            $accId = $id;
        } else {
            // Insert
            $stmtIns = $pdo->prepare("
                INSERT INTO accommodations (name, description, type, municipality, address, contact_number, email, website, price_start, rating, featured, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtIns->execute([$name, $description, $type, $municipality, $address, $contact_number, $email, $website, $price_start, $rating, $featured, $status]);
            $accId = $pdo->lastInsertId();
        }

        // Sync Amenities
        $pdo->prepare("DELETE FROM accommodation_amenities WHERE accommodation_id = ?")->execute([$accId]);
        $stmtAA = $pdo->prepare("INSERT INTO accommodation_amenities (accommodation_id, amenity_id) VALUES (?, ?)");
        foreach ($amenities_input as $aid) {
            $stmtAA->execute([$accId, intval($aid)]);
        }

        // Sync Primary Photo
        if (!empty($main_photo)) {
            $pdo->prepare("DELETE FROM photos WHERE accommodation_id = ? AND is_primary = 1")->execute([$accId]);
            $stmtPh = $pdo->prepare("INSERT INTO photos (accommodation_id, image_url, caption, is_primary) VALUES (?, ?, 'Primary Photo', 1)");
            $stmtPh->execute([$accId, $main_photo]);
        }

        header("Location: /berot/admin/accommodations.php?msg=saved");
        exit();
    }
}
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><?php echo $admin_page_title; ?></h3>
        <a href="/berot/admin/accommodations.php" class="btn-sm btn-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Accommodations List
        </a>
    </div>

    <form action="/berot/admin/accommodation_form.php?id=<?php echo $id; ?>" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Accommodation Name *</label>
                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($acc['name']); ?>" placeholder="e.g. Cantilan Beach Resort">
            </div>

            <div class="form-group">
                <label>Accommodation Category *</label>
                <select name="type" class="form-control" required>
                    <option value="Hotel" <?php echo $acc['type'] == 'Hotel' ? 'selected' : ''; ?>>Hotel</option>
                    <option value="Resort" <?php echo $acc['type'] == 'Resort' ? 'selected' : ''; ?>>Resort</option>
                    <option value="Beach Resort" <?php echo $acc['type'] == 'Beach Resort' ? 'selected' : ''; ?>>Beach Resort</option>
                    <option value="Inn" <?php echo $acc['type'] == 'Inn' ? 'selected' : ''; ?>>Inn</option>
                    <option value="Homestay" <?php echo $acc['type'] == 'Homestay' ? 'selected' : ''; ?>>Homestay</option>
                    <option value="Guesthouse" <?php echo $acc['type'] == 'Guesthouse' ? 'selected' : ''; ?>>Guesthouse</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Municipality *</label>
                <select name="municipality" class="form-control" required>
                    <option value="Cantilan" <?php echo $acc['municipality'] == 'Cantilan' ? 'selected' : ''; ?>>Cantilan</option>
                    <option value="Lanuza" <?php echo $acc['municipality'] == 'Lanuza' ? 'selected' : ''; ?>>Lanuza</option>
                    <option value="Madrid" <?php echo $acc['municipality'] == 'Madrid' ? 'selected' : ''; ?>>Madrid</option>
                    <option value="Carmen" <?php echo $acc['municipality'] == 'Carmen' ? 'selected' : ''; ?>>Carmen</option>
                    <option value="Carrascal" <?php echo $acc['municipality'] == 'Carrascal' ? 'selected' : ''; ?>>Carrascal</option>
                </select>
            </div>

            <div class="form-group">
                <label>Starting Price (₱ / night) *</label>
                <input type="number" step="50" name="price_start" class="form-control" required value="<?php echo $acc['price_start']; ?>">
            </div>

            <div class="form-group">
                <label>Rating Score (1.0 to 5.0)</label>
                <input type="number" step="0.1" min="1.0" max="5.0" name="rating" class="form-control" value="<?php echo $acc['rating']; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Complete Street / Barangay Address *</label>
            <input type="text" name="address" class="form-control" required value="<?php echo htmlspecialchars($acc['address']); ?>" placeholder="e.g. Brgy. Baybay, Cantilan, Surigao del Sur">
        </div>

        <div class="form-group">
            <label>Description & Tourist Overview *</label>
            <textarea name="description" class="form-control" rows="4" required placeholder="Describe the atmosphere, beachfront, rooms, and hospitality..."><?php echo htmlspecialchars($acc['description']); ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label>Contact Phone Number *</label>
                <input type="text" name="contact_number" class="form-control" required value="<?php echo htmlspecialchars($acc['contact_number']); ?>" placeholder="+63 917 123 4567">
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($acc['email']); ?>" placeholder="info@resort.com">
            </div>

            <div class="form-group">
                <label>Website URL (Optional)</label>
                <input type="url" name="website" class="form-control" value="<?php echo htmlspecialchars($acc['website']); ?>" placeholder="https://resort.com">
            </div>
        </div>

        <div class="form-group">
            <label>Primary Photo Image URL</label>
            <input type="url" name="photo_url" class="form-control" value="<?php echo htmlspecialchars($photoUrl); ?>" placeholder="https://images.unsplash.com/photo-...">
        </div>

        <!-- Amenities Selection -->
        <div class="form-group">
            <label>Select Included Amenities & Facilities</label>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; background: #F8FAFC; padding: 16px; border-radius: 8px;">
                <?php foreach ($allAmenities as $am): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="amenities[]" value="<?php echo $am['id']; ?>" <?php echo in_array($am['id'], $selectedAmenities) ? 'checked' : ''; ?>>
                        <span><i class="fa-solid <?php echo htmlspecialchars($am['icon']); ?>"></i> <?php echo htmlspecialchars($am['name']); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="display: flex; gap: 20px; align-items: center;">
            <label class="checkbox-item">
                <input type="checkbox" name="featured" value="1" <?php echo $acc['featured'] ? 'checked' : ''; ?>>
                <strong>Featured on Homepage</strong>
            </label>

            <div class="form-group" style="margin-left: auto; width: 160px;">
                <label>Record Status</label>
                <select name="status" class="form-control">
                    <option value="Active" <?php echo $acc['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $acc['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-search" style="width: auto; padding: 14px 32px; align-self: flex-start;">
            <i class="fa-solid fa-floppy-disk"></i> Save Accommodation Record
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

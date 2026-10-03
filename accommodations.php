<?php
// accommodations.php - Search & Filter Catalog Page
$page_title = "Search & Filter Accommodations";
$active_nav = "accommodations";

require_once __DIR__ . '/includes/db.php';

// Get Filter Parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$municipality = isset($_GET['municipality']) ? trim($_GET['municipality']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$min_rating = isset($_GET['min_rating']) && is_numeric($_GET['min_rating']) ? floatval($_GET['min_rating']) : 0;
$selected_amenities = isset($_GET['amenities']) && is_array($_GET['amenities']) ? $_GET['amenities'] : [];

// Build SQL Query
$sql = "SELECT DISTINCT a.* FROM accommodations a WHERE a.status = 'Active'";
$params = [];

if ($search !== '') {
    $sql .= " AND (a.name LIKE ? OR a.description LIKE ? OR a.address LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($municipality !== '') {
    $sql .= " AND a.municipality = ?";
    $params[] = $municipality;
}

if ($type !== '') {
    $sql .= " AND a.type = ?";
    $params[] = $type;
}

if ($min_price > 0) {
    $sql .= " AND a.price_start >= ?";
    $params[] = $min_price;
}

if ($max_price < 10000) {
    $sql .= " AND a.price_start <= ?";
    $params[] = $max_price;
}

if ($min_rating > 0) {
    $sql .= " AND a.rating >= ?";
    $params[] = $min_rating;
}

// Amenity Filter Subquery
if (!empty($selected_amenities)) {
    foreach ($selected_amenities as $aid) {
        $sql .= " AND EXISTS (SELECT 1 FROM accommodation_amenities aa WHERE aa.accommodation_id = a.id AND aa.amenity_id = ?)";
        $params[] = intval($aid);
    }
}

$sql .= " ORDER BY a.featured DESC, a.rating DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Fetch All Amenities for Sidebar
$allAmenities = $pdo->query("SELECT * FROM amenities ORDER BY name ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Subhero Header -->
<section style="background: linear-gradient(135deg, var(--dark-bg) 0%, #1E293B 100%); color: #FFFFFF; padding: 40px 0;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: var(--font-heading); font-size: 2rem; font-weight: 800;">
                Find Local Accommodations
            </h1>
            <p style="color: #94A3B8; font-size: 0.95rem;">
                Search, filter, and compare hotels, resorts, inns, and homestays across CarCanMadCarLan.
            </p>
        </div>
        <div class="demo-badge" style="font-size: 0.85rem; padding: 6px 14px;">
            <i class="fa-solid fa-hotel"></i> <?php echo count($results); ?> Accommodations Available
        </div>
    </div>
</section>

<div class="container filter-layout">
    <!-- Filter Sidebar -->
    <aside class="filter-sidebar">
        <form action="/berot/accommodations.php" method="GET" id="filterForm">
            <h3>
                <span><i class="fa-solid fa-filter"></i> Filters</span>
                <a href="/berot/accommodations.php" style="font-size: 0.78rem; font-weight: 600; color: var(--accent);"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            </h3>

            <!-- Keyword Search -->
            <div class="filter-group">
                <label><i class="fa-solid fa-magnifying-glass"></i> Keyword</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <!-- Municipality -->
            <div class="filter-group">
                <label><i class="fa-solid fa-location-dot"></i> Municipality</label>
                <select name="municipality" class="form-control">
                    <option value="">All Municipalities</option>
                    <option value="Cantilan" <?php echo $municipality == 'Cantilan' ? 'selected' : ''; ?>>Cantilan</option>
                    <option value="Lanuza" <?php echo $municipality == 'Lanuza' ? 'selected' : ''; ?>>Lanuza</option>
                    <option value="Madrid" <?php echo $municipality == 'Madrid' ? 'selected' : ''; ?>>Madrid</option>
                    <option value="Carmen" <?php echo $municipality == 'Carmen' ? 'selected' : ''; ?>>Carmen</option>
                    <option value="Carrascal" <?php echo $municipality == 'Carrascal' ? 'selected' : ''; ?>>Carrascal</option>
                </select>
            </div>

            <!-- Accommodation Type -->
            <div class="filter-group">
                <label><i class="fa-solid fa-hotel"></i> Stay Category</label>
                <select name="type" class="form-control">
                    <option value="">All Categories</option>
                    <option value="Hotel" <?php echo $type == 'Hotel' ? 'selected' : ''; ?>>Hotel</option>
                    <option value="Resort" <?php echo $type == 'Resort' ? 'selected' : ''; ?>>Resort</option>
                    <option value="Beach Resort" <?php echo $type == 'Beach Resort' ? 'selected' : ''; ?>>Beach Resort</option>
                    <option value="Inn" <?php echo $type == 'Inn' ? 'selected' : ''; ?>>Inn</option>
                    <option value="Homestay" <?php echo $type == 'Homestay' ? 'selected' : ''; ?>>Homestay</option>
                    <option value="Guesthouse" <?php echo $type == 'Guesthouse' ? 'selected' : ''; ?>>Guesthouse</option>
                </select>
            </div>

            <!-- Price Range -->
            <div class="filter-group">
                <label><i class="fa-solid fa-peso-sign"></i> Max Starting Price (₱)</label>
                <input type="range" name="max_price" min="1000" max="10000" step="500" value="<?php echo $max_price; ?>" class="form-control" style="padding:0;" oninput="document.getElementById('priceVal').innerText = this.value">
                <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary-dark); margin-top: 4px;">
                    Up to ₱<span id="priceVal"><?php echo number_format($max_price); ?></span> / night
                </div>
            </div>

            <!-- Rating Filter -->
            <div class="filter-group">
                <label><i class="fa-solid fa-star"></i> Minimum Rating</label>
                <select name="min_rating" class="form-control">
                    <option value="0">Any Rating</option>
                    <option value="4.5" <?php echo $min_rating == 4.5 ? 'selected' : ''; ?>>4.5+ Stars (Top Rated)</option>
                    <option value="4.0" <?php echo $min_rating == 4.0 ? 'selected' : ''; ?>>4.0+ Stars</option>
                </select>
            </div>

            <!-- Amenities Checkboxes -->
            <div class="filter-group">
                <label><i class="fa-solid fa-list-check"></i> Amenities & Facilities</label>
                <div class="checkbox-list">
                    <?php foreach ($allAmenities as $am): ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="amenities[]" value="<?php echo $am['id']; ?>" <?php echo in_array($am['id'], $selected_amenities) ? 'checked' : ''; ?>>
                            <span><?php echo htmlspecialchars($am['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn-search" style="width: 100%;">
                <i class="fa-solid fa-magnifying-glass"></i> Apply Filters
            </button>
        </form>
    </aside>

    <!-- Results Grid -->
    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div style="font-size: 1.1rem; font-weight: 700;">
                Showing <?php echo count($results); ?> Result(s) 
                <?php if ($municipality): ?>
                    in <span style="color: var(--primary);"><?php echo htmlspecialchars($municipality); ?></span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($results)): ?>
            <div style="background: #FFFFFF; padding: 48px; border-radius: var(--radius-md); text-align: center; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm);">
                <i class="fa-solid fa-hotel" style="font-size: 3.5rem; color: var(--text-muted); margin-bottom: 16px;"></i>
                <h3 style="font-family: var(--font-heading); font-size: 1.5rem; margin-bottom: 10px;">No Accommodations Found</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">We couldn't find any accommodation matching your current search criteria. Try adjusting your filters or search term.</p>
                <a href="/berot/accommodations.php" class="btn-search" style="display: inline-flex; width: auto;">
                    <i class="fa-solid fa-rotate-left"></i> Reset All Filters
                </a>
            </div>
        <?php else: ?>
            <div class="accommodations-grid">
                <?php foreach ($results as $stay): 
                    // Fetch photo
                    $stmtP = $pdo->prepare("SELECT image_url FROM photos WHERE accommodation_id = ? ORDER BY is_primary DESC LIMIT 1");
                    $stmtP->execute([$stay['id']]);
                    $photo = $stmtP->fetch();
                    $imgUrl = $photo ? $photo['image_url'] : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80';

                    // Fetch amenities
                    $stmtA = $pdo->prepare("
                        SELECT a.name 
                        FROM amenities a 
                        JOIN accommodation_amenities aa ON a.id = aa.amenity_id 
                        WHERE aa.accommodation_id = ? LIMIT 3
                    ");
                    $stmtA->execute([$stay['id']]);
                    $amenityList = $stmtA->fetchAll();
                ?>
                    <div class="acc-card">
                        <div class="acc-img-wrapper">
                            <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($stay['name']); ?>">
                            <span class="acc-badge-type"><?php echo htmlspecialchars($stay['type']); ?></span>
                            <span class="acc-badge-muni"><?php echo htmlspecialchars($stay['municipality']); ?></span>
                        </div>

                        <div class="acc-info">
                            <div class="acc-rating">
                                <i class="fa-solid fa-star"></i>
                                <span><?php echo number_format($stay['rating'], 1); ?> Excellent</span>
                            </div>
                            <h3 class="acc-title"><?php echo htmlspecialchars($stay['name']); ?></h3>
                            <div class="acc-address">
                                <i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($stay['address']); ?>
                            </div>
                            <p class="acc-desc"><?php echo htmlspecialchars($stay['description']); ?></p>

                            <div class="acc-amenities-pills">
                                <?php foreach ($amenityList as $am): ?>
                                    <span class="amenity-pill"><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($am['name']); ?></span>
                                <?php endforeach; ?>
                            </div>

                            <div class="acc-footer">
                                <div class="acc-price-tag">
                                    <span class="acc-price-label">Starting from</span>
                                    <span class="acc-price-val">₱<?php echo number_format($stay['price_start'], 2); ?> <small style="font-size:0.75rem; font-weight:normal; color:var(--text-muted);">/ night</small></span>
                                </div>
                                <a href="/berot/details.php?id=<?php echo $stay['id']; ?>" class="btn-details">
                                    View Details <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/includes/header.php'; ?>

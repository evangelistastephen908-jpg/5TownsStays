<?php
// details.php - Accommodation Details Page
$page_title = "Accommodation Details";
$active_nav = "accommodations";

require_once __DIR__ . '/includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: /berot/accommodations.php");
    exit();
}

// Fetch Accommodation Record
$stmt = $pdo->prepare("SELECT * FROM accommodations WHERE id = ? AND status = 'Active'");
$stmt->execute([$id]);
$stay = $stmt->fetch();

if (!$stay) {
    header("Location: /berot/accommodations.php");
    exit();
}

$page_title = $stay['name'] . " - " . $stay['municipality'];

// Fetch Photos
$stmtP = $pdo->prepare("SELECT * FROM photos WHERE accommodation_id = ? ORDER BY is_primary DESC");
$stmtP->execute([$id]);
$photos = $stmtP->fetchAll();

if (empty($photos)) {
    $photos = [
        ['image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Exterior View'],
        ['image_url' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Deluxe Room'],
        ['image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80', 'caption' => 'Lounge & Amenities']
    ];
}

// Fetch Amenities
$stmtA = $pdo->prepare("
    SELECT a.* 
    FROM amenities a 
    JOIN accommodation_amenities aa ON a.id = aa.amenity_id 
    WHERE aa.accommodation_id = ? 
    ORDER BY a.name ASC
");
$stmtA->execute([$id]);
$amenities = $stmtA->fetchAll();

// Fetch Rooms Matrix
$stmtRm = $pdo->prepare("SELECT * FROM rooms WHERE accommodation_id = ? ORDER BY price ASC");
$stmtRm->execute([$id]);
$rooms = $stmtRm->fetchAll();

// Fetch Reviews
$stmtRev = $pdo->prepare("SELECT * FROM reviews WHERE accommodation_id = ? AND status = 'Approved' ORDER BY created_at DESC");
$stmtRev->execute([$id]);
$reviews = $stmtRev->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Header Info Banner -->
<div class="details-hero">
    <div class="container">
        <div class="details-header-row">
            <div class="details-title-box">
                <h1><?php echo htmlspecialchars($stay['name']); ?></h1>
                <div class="details-meta-tags">
                    <span class="badge-tag muni"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($stay['municipality']); ?>, Surigao del Sur</span>
                    <span class="badge-tag type"><i class="fa-solid fa-hotel"></i> <?php echo htmlspecialchars($stay['type']); ?></span>
                    <span style="color: var(--accent); font-weight: 700; font-size: 0.95rem;">
                        <i class="fa-solid fa-star"></i> <?php echo number_format($stay['rating'], 1); ?> Rated
                    </span>
                </div>
            </div>

            <div style="text-align: right;">
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Starting Nightly Rate</span>
                <div style="font-size: 2rem; font-weight: 800; color: var(--primary-dark);">
                    ₱<?php echo number_format($stay['price_start'], 2); ?>
                </div>
            </div>
        </div>

        <!-- Photo Gallery Grid -->
        <div class="gallery-grid">
            <div class="gallery-main">
                <img src="<?php echo htmlspecialchars($photos[0]['image_url']); ?>" alt="Main Photo">
            </div>
            <div class="gallery-sub">
                <?php if (isset($photos[1])): ?>
                    <img src="<?php echo htmlspecialchars($photos[1]['image_url']); ?>" alt="Sub Photo 1">
                <?php endif; ?>
                <?php if (isset($photos[2])): ?>
                    <img src="<?php echo htmlspecialchars($photos[2]['image_url']); ?>" alt="Sub Photo 2">
                <?php else: ?>
                    <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80" alt="Sub Photo 2">
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Details Layout -->
<div class="container details-container">
    <!-- Left Column: Details, Rooms, Amenities, Reviews -->
    <div>
        <!-- Overview & Description -->
        <div class="details-card-block">
            <h2 class="details-block-title"><i class="fa-solid fa-circle-info"></i> About This Accommodation</h2>
            <p style="font-size: 1.02rem; line-height: 1.7; color: var(--text-main);">
                <?php echo nl2br(htmlspecialchars($stay['description'])); ?>
            </p>

            <div style="margin-top: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; background: var(--bg-light); padding: 18px; border-radius: var(--radius-sm);">
                <div>
                    <strong style="font-size: 0.82rem; color: var(--text-muted); text-transform: uppercase;">Location Address</strong>
                    <p style="font-size: 0.95rem; font-weight: 600;"><i class="fa-solid fa-map-pin" style="color:var(--accent);"></i> <?php echo htmlspecialchars($stay['address']); ?></p>
                </div>
                <div>
                    <strong style="font-size: 0.82rem; color: var(--text-muted); text-transform: uppercase;">Contact Hotline</strong>
                    <p style="font-size: 0.95rem; font-weight: 600;"><i class="fa-solid fa-phone" style="color:var(--primary);"></i> <?php echo htmlspecialchars($stay['contact_number']); ?></p>
                </div>
                <div>
                    <strong style="font-size: 0.82rem; color: var(--text-muted); text-transform: uppercase;">Direct Email</strong>
                    <p style="font-size: 0.95rem; font-weight: 600;"><i class="fa-solid fa-envelope" style="color:var(--teal);"></i> <?php echo htmlspecialchars($stay['email']); ?></p>
                </div>
            </div>
        </div>

        <!-- Available Rooms Table Matrix -->
        <div class="details-card-block">
            <h2 class="details-block-title"><i class="fa-solid fa-bed"></i> Available Room Options</h2>
            <?php if (empty($rooms)): ?>
                <p class="text-muted">Standard room details available upon inquiry.</p>
            <?php else: ?>
                <table class="room-table">
                    <thead>
                        <tr>
                            <th>Room Name</th>
                            <th>Max Capacity</th>
                            <th>Nightly Rate</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rooms as $rm): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($rm['room_name']); ?></strong></td>
                                <td><i class="fa-solid fa-user-group"></i> <?php echo $rm['capacity']; ?> Guests</td>
                                <td><strong style="color:var(--primary-dark);">₱<?php echo number_format($rm['price'], 2); ?></strong></td>
                                <td>
                                    <span class="badge-tag" style="background:#D1FAE5; color:#065F46;">
                                        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($rm['availability']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Amenities & Facilities -->
        <div class="details-card-block">
            <h2 class="details-block-title"><i class="fa-solid fa-list-check"></i> Amenities & Facilities</h2>
            <div class="amenities-icon-grid">
                <?php foreach ($amenities as $am): ?>
                    <div class="amenity-item-box">
                        <i class="fa-solid <?php echo htmlspecialchars($am['icon']); ?>"></i>
                        <span><?php echo htmlspecialchars($am['name']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- House Rules & Policies -->
        <div class="details-card-block">
            <h2 class="details-block-title"><i class="fa-solid fa-clipboard-list"></i> House Rules & Guidelines</h2>
            <ul style="list-style: disc; padding-left: 20px; font-size: 0.95rem; color: var(--text-muted); display: flex; flex-direction: column; gap: 8px;">
                <li><strong>Check-in Time:</strong> 2:00 PM onwards | <strong>Check-out Time:</strong> 12:00 PM</li>
                <li>Valid government-issued ID or passport required upon arrival.</li>
                <li>Quiet hours observed between 10:00 PM and 7:00 AM.</li>
                <li>Smoking permitted in designated outdoor areas only.</li>
                <li>Cancellation policies managed directly with accommodation host.</li>
            </ul>
        </div>

        <!-- Map Location Preview Simulator -->
        <div class="details-card-block">
            <h2 class="details-block-title"><i class="fa-solid fa-map-location-dot"></i> Location & Access</h2>
            <div style="background: #E2E8F0; height: 260px; border-radius: var(--radius-sm); display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; overflow: hidden; text-align: center; padding: 20px;">
                <i class="fa-solid fa-map-pin" style="font-size: 3rem; color: var(--accent); margin-bottom: 12px;"></i>
                <h4 style="font-family: var(--font-heading); font-size: 1.2rem; margin-bottom: 4px;"><?php echo htmlspecialchars($stay['name']); ?> Map Location</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 400px;"><?php echo htmlspecialchars($stay['address']); ?></p>
                <div style="margin-top: 14px; background: rgba(255,255,255,0.9); padding: 6px 16px; border-radius: 20px; font-size: 0.78rem; font-weight: 700; color: var(--dark-bg);">
                    Coordinates: Surigao del Sur Coastal Highway • <?php echo htmlspecialchars($stay['municipality']); ?> Sector
                </div>
            </div>
        </div>

        <!-- Tourist Reviews & Submission Form -->
        <div class="details-card-block" id="reviews-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 class="details-block-title" style="margin-bottom: 0; border: none;"><i class="fa-solid fa-comments"></i> Tourist Reviews (<?php echo count($reviews); ?>)</h2>
                <button class="btn-details" onclick="document.getElementById('reviewModal').style.display='flex'">
                    <i class="fa-solid fa-pen-to-square"></i> Submit Review
                </button>
            </div>

            <?php if (empty($reviews)): ?>
                <p style="color: var(--text-muted); font-style: italic;">No reviews submitted yet for this stay. Be the first to share your experience!</p>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <?php foreach ($reviews as $rev): ?>
                        <div style="background: var(--bg-light); padding: 18px; border-radius: var(--radius-sm); border-left: 4px solid var(--accent);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
                                <div style="color: var(--accent); font-size: 0.85rem;">
                                    <?php for($i=1; $i<=5; $i++): ?>
                                        <i class="fa-solid fa-star <?php echo $i <= $rev['rating'] ? '' : 'fa-regular'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p style="font-size: 0.95rem; color: var(--text-main); margin-bottom: 6px;">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                            <small style="color: var(--text-muted); font-size: 0.78rem;"><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar: Inquiry Request Form -->
    <aside>
        <div class="inquiry-card" id="inquiry-section">
            <div class="inquiry-card-header">
                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent); text-transform: uppercase;">Direct Host Contact</span>
                <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: var(--dark-bg); margin-top: 4px;">Send Stay Inquiry</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted);">Inquire about rates, availability, or custom requests directly with the manager.</p>
            </div>

            <form action="/berot/submit_inquiry.php" method="POST" class="inquiry-form-body">
                <input type="hidden" name="accommodation_id" value="<?php echo $stay['id']; ?>">

                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="user_name" class="form-control" required placeholder="e.g. Juan dela Cruz">
                </div>

                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="user_email" class="form-control" required placeholder="name@example.com">
                </div>

                <div class="form-group">
                    <label>Contact Phone Number *</label>
                    <input type="text" name="user_phone" class="form-control" required placeholder="0917 123 4567">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="form-group">
                        <label>Check-in</label>
                        <input type="date" name="check_in" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Check-out</label>
                        <input type="date" name="check_out" class="form-control" required value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Number of Guests</label>
                    <select name="guests" class="form-control">
                        <option value="1">1 Guest</option>
                        <option value="2" selected>2 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="6">6+ Guests</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message / Special Request</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Inquire about room options, airport transfers, ocean view..."></textarea>
                </div>

                <button type="submit" class="btn-search" style="width: 100%; margin-top: 10px;">
                    <i class="fa-solid fa-paper-plane"></i> Send Inquiry Request
                </button>

                <p style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 8px;">
                    <i class="fa-solid fa-shield"></i> Your inquiry goes straight to accommodation management. No booking fee charged.
                </p>
            </form>
        </div>
    </aside>
</div>

<!-- Review Modal Popup -->
<div id="reviewModal" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.7); backdrop-filter: blur(6px); z-index: 10000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; width: 100%; max-width: 500px; border-radius: var(--radius-md); padding: 28px; box-shadow: var(--shadow-lg);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-family: var(--font-heading); font-size: 1.4rem;">Submit Tourist Review</h3>
            <button onclick="document.getElementById('reviewModal').style.display='none'" style="background: none; border: none; font-size: 1.2rem; cursor: pointer;"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form action="/berot/submit_review.php" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
            <input type="hidden" name="accommodation_id" value="<?php echo $stay['id']; ?>">

            <div class="form-group">
                <label>Your Name *</label>
                <input type="text" name="user_name" class="form-control" required placeholder="e.g. Maria Santos">
            </div>

            <div class="form-group">
                <label>Rating (1 to 5 Stars) *</label>
                <select name="rating" class="form-control" required>
                    <option value="5" selected>5 Stars - Exceptional</option>
                    <option value="4">4 Stars - Very Good</option>
                    <option value="3">3 Stars - Average</option>
                    <option value="2">2 Stars - Poor</option>
                    <option value="1">1 Star - Terrible</option>
                </select>
            </div>

            <div class="form-group">
                <label>Your Feedback / Comment *</label>
                <textarea name="comment" class="form-control" rows="4" required placeholder="Share your experience regarding clean rooms, staff, amenities, location..."></textarea>
            </div>

            <button type="submit" class="btn-search">
                <i class="fa-solid fa-check"></i> Submit Review for Moderation
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

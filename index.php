<?php
// index.php - 5TownsStays Homepage
$page_title = "Discover Your Stay in CarCanMadCarLan";
$active_nav = "home";

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Fetch Featured Accommodations
$stmtF = $pdo->query("SELECT * FROM accommodations WHERE featured = 1 AND status = 'Active' ORDER BY rating DESC LIMIT 6");
$featuredStays = $stmtF->fetchAll();

// Fetch Municipality Counts
$munisList = ['Cantilan', 'Lanuza', 'Madrid', 'Carmen', 'Carrascal'];
$muniCounts = [];
foreach ($munisList as $m) {
    $stmtC = $pdo->prepare("SELECT COUNT(*) as count FROM accommodations WHERE municipality = ?");
    $stmtC->execute([$m]);
    $muniCounts[$m] = $stmtC->fetch()['count'];
}

// Fetch Approved Reviews
$stmtR = $pdo->query("
    SELECT r.*, a.name as acc_name, a.municipality 
    FROM reviews r 
    JOIN accommodations a ON r.accommodation_id = a.id 
    WHERE r.status = 'Approved' 
    ORDER BY r.created_at DESC LIMIT 4
");
$recentReviews = $stmtR->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-content">
        <div class="hero-badge">
            <i class="fa-solid fa-compass"></i> Surigao del Sur Accommodation Directory
        </div>
        <h1 class="hero-title">Discover Your Stay in <span>CarCanMadCarLan</span></h1>
        <p class="hero-subtitle">
            Find hotels, resorts, inns, homestays, and local accommodations across Carmen, Cantilan, Madrid, Lanuza, and Carrascal.
        </p>

        <!-- Search Bar Card -->
        <div class="search-card-wrapper">
            <form action="/berot/accommodations.php" method="GET" class="hero-search-form">
                <div class="form-group">
                    <label><i class="fa-solid fa-magnifying-glass"></i> Search Stay</label>
                    <input type="text" name="search" class="form-control" placeholder="Resort name, place, amenity...">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-location-dot"></i> Municipality</label>
                    <select name="municipality" class="form-control">
                        <option value="">All Municipalities</option>
                        <option value="Cantilan">Cantilan</option>
                        <option value="Lanuza">Lanuza</option>
                        <option value="Madrid">Madrid</option>
                        <option value="Carmen">Carmen</option>
                        <option value="Carrascal">Carrascal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-hotel"></i> Stay Type</label>
                    <select name="type" class="form-control">
                        <option value="">All Types</option>
                        <option value="Hotel">Hotel</option>
                        <option value="Resort">Resort</option>
                        <option value="Beach Resort">Beach Resort</option>
                        <option value="Inn">Inn</option>
                        <option value="Homestay">Homestay</option>
                        <option value="Guesthouse">Guesthouse</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-calendar"></i> Check-in</label>
                    <input type="date" name="check_in" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-check"></i> Check-out</label>
                    <input type="date" name="check_out" class="form-control" value="<?php echo date('Y-m-d', strtotime('+2 days')); ?>">
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-user-group"></i> Guests</label>
                    <select name="guests" class="form-control">
                        <option value="1">1 Guest</option>
                        <option value="2" selected>2 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="6">6+ Family</option>
                    </select>
                </div>

                <button type="submit" class="btn-search">
                    <i class="fa-solid fa-magnifying-glass"></i> Find Stay
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Section 1: Explore by Municipality -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Local Destinations</span>
            <h2 class="section-title">Explore Stays by Municipality</h2>
            <p class="section-desc">Browse local accommodations tailored across the five iconic coastal and river towns of Surigao del Sur.</p>
        </div>

        <div class="muni-grid">
            <a href="/berot/accommodations.php?municipality=Cantilan" class="muni-card">
                <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=600&q=80" alt="Cantilan">
                <div class="muni-content">
                    <div class="muni-name">Cantilan</div>
                    <div class="muni-count"><i class="fa-solid fa-bed"></i> <?php echo $muniCounts['Cantilan']; ?> Accommodations</div>
                </div>
            </a>

            <a href="/berot/accommodations.php?municipality=Lanuza" class="muni-card">
                <img src="https://images.unsplash.com/photo-1502680390469-be75c86b636f?auto=format&fit=crop&w=600&q=80" alt="Lanuza">
                <div class="muni-content">
                    <div class="muni-name">Lanuza</div>
                    <div class="muni-count"><i class="fa-solid fa-water"></i> <?php echo $muniCounts['Lanuza']; ?> Surf & Nature Lodges</div>
                </div>
            </a>

            <a href="/berot/accommodations.php?municipality=Madrid" class="muni-card">
                <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=600&q=80" alt="Madrid">
                <div class="muni-content">
                    <div class="muni-name">Madrid</div>
                    <div class="muni-count"><i class="fa-solid fa-building-user"></i> <?php echo $muniCounts['Madrid']; ?> Hotels & Suites</div>
                </div>
            </a>

            <a href="/berot/accommodations.php?municipality=Carmen" class="muni-card">
                <img src="https://images.unsplash.com/photo-1519046904884-53103b34b206?auto=format&fit=crop&w=600&q=80" alt="Carmen">
                <div class="muni-content">
                    <div class="muni-name">Carmen</div>
                    <div class="muni-count"><i class="fa-solid fa-spa"></i> <?php echo $muniCounts['Carmen']; ?> Spring Havens</div>
                </div>
            </a>

            <a href="/berot/accommodations.php?municipality=Carrascal" class="muni-card">
                <img src="https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?auto=format&fit=crop&w=600&q=80" alt="Carrascal">
                <div class="muni-content">
                    <div class="muni-name">Carrascal</div>
                    <div class="muni-count"><i class="fa-solid fa-umbrella-beach"></i> <?php echo $muniCounts['Carrascal']; ?> Bayfront Stays</div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Section 2: Featured Accommodations -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Top Recommended</span>
            <h2 class="section-title">Featured Local Accommodations</h2>
            <p class="section-desc">Handpicked stays offering exceptional hospitality, clean facilities, and authentic CarCanMadCarLan vibes.</p>
        </div>

        <div class="accommodations-grid">
            <?php foreach ($featuredStays as $stay): 
                // Fetch primary photo
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

        <div style="text-align: center; margin-top: 40px;">
            <a href="/berot/accommodations.php" class="btn-search" style="display: inline-flex; width: auto; padding: 14px 32px;">
                <i class="fa-solid fa-list-check"></i> View All Accommodations Directory
            </a>
        </div>
    </div>
</section>

<!-- Section 3: Accommodation Categories -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Find What Fits You</span>
            <h2 class="section-title">Accommodation Categories</h2>
            <p class="section-desc">Whether you are seeking a luxury beach resort, a quiet homestay, or a surf lodge, we have it listed.</p>
        </div>

        <div class="cat-grid">
            <a href="/berot/accommodations.php?type=Hotel" class="cat-card">
                <div class="cat-icon"><i class="fa-solid fa-hotel"></i></div>
                <div class="cat-name">Hotels & Suites</div>
            </a>

            <a href="/berot/accommodations.php?type=Beach+Resort" class="cat-card">
                <div class="cat-icon"><i class="fa-solid fa-umbrella-beach"></i></div>
                <div class="cat-name">Beach Resorts</div>
            </a>

            <a href="/berot/accommodations.php?type=Inn" class="cat-card">
                <div class="cat-icon"><i class="fa-solid fa-bed"></i></div>
                <div class="cat-name">Tourist Inns</div>
            </a>

            <a href="/berot/accommodations.php?type=Homestay" class="cat-card">
                <div class="cat-icon"><i class="fa-solid fa-house-user"></i></div>
                <div class="cat-name">Local Homestays</div>
            </a>

            <a href="/berot/accommodations.php?type=Guesthouse" class="cat-card">
                <div class="cat-icon"><i class="fa-solid fa-person-shelter"></i></div>
                <div class="cat-name">Guesthouses</div>
            </a>
        </div>
    </div>
</section>

<!-- Section 4: Why Use 5TownsStays? -->
<section class="section" style="background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Designed for Tourists</span>
            <h2 class="section-title">Why Use 5TownsStays?</h2>
            <p class="section-desc">Built as a dedicated research platform prioritizing clear, accurate accommodation information for tourists in CarCanMadCarLan.</p>
        </div>

        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3 class="why-title">Focused Accommodation Directory</h3>
                <p class="why-desc">Purely dedicated to hotels, resorts, inns, and homestays without cluttering you with unrelated travel tour packages.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><i class="fa-solid fa-map-location-dot"></i></div>
                <h3 class="why-title">Complete 5-Town Coverage</h3>
                <p class="why-desc">Comprehensive database spanning Carmen, Cantilan, Madrid, Lanuza, and Carrascal in Surigao del Sur.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><i class="fa-solid fa-paper-plane"></i></div>
                <h3 class="why-title">Direct Host Inquiry</h3>
                <p class="why-desc">Send immediate availability inquiries directly to the accommodation management with zero booking markups.</p>
            </div>

            <div class="why-card">
                <div class="why-icon"><i class="fa-solid fa-star-half-stroke"></i></div>
                <h3 class="why-title">Verified Tourist Reviews</h3>
                <p class="why-desc">Read feedback from actual visitors to make well-informed decisions for your trip.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section 5: Recent Tourist Reviews -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">Tourist Experiences</span>
            <h2 class="section-title">What Tourists Say</h2>
            <p class="section-desc">Real reviews submitted by visitors who found their stay in CarCanMadCarLan through 5TownsStays.</p>
        </div>

        <div class="why-grid">
            <?php foreach ($recentReviews as $rev): ?>
                <div class="why-card">
                    <div class="acc-rating" style="margin-bottom: 12px;">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="fa-solid fa-star <?php echo $i <= $rev['rating'] ? '' : 'fa-regular'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p style="font-style: italic; color: var(--text-main); margin-bottom: 16px;">
                        "<?php echo htmlspecialchars($rev['comment']); ?>"
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 12px;">
                        <div>
                            <strong><?php echo htmlspecialchars($rev['user_name']); ?></strong><br>
                            <small class="text-muted" style="font-size: 0.78rem; color: var(--text-muted);">
                                Stayed at <?php echo htmlspecialchars($rev['acc_name']); ?> (<?php echo htmlspecialchars($rev['municipality']); ?>)
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 6: About Project Section -->
<section class="section" id="about" style="background: linear-gradient(135deg, var(--dark-bg) 0%, #1E293B 100%); color: #FFFFFF;">
    <div class="container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center;">
        <div>
            <span class="section-subtitle" style="color: var(--accent);">Research Project Overview</span>
            <h2 class="section-title" style="color: #FFFFFF; font-size: 2.2rem; margin-bottom: 20px;">
                5TownsStays Platform
            </h2>
            <p style="color: #CBD5E1; margin-bottom: 16px; font-size: 1.05rem;">
                <strong>Research Title:</strong> “5TownsStays: A Web-Based Hotel and Resort Information Platform for Tourists in Finding Local Accommodations in CarCanMadCarLan”
            </p>
            <p style="color: #94A3B8; margin-bottom: 24px; font-size: 0.95rem; line-height: 1.7;">
                Developed following the <strong>Agile Software Development Model</strong>, this system empowers tourists visiting Surigao del Sur to search, compare rates, examine room amenities, view photo galleries, examine location maps, and directly submit inquiries to local accommodation establishments.
            </p>
            <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                <a href="/berot/accommodations.php" class="btn-search" style="background: var(--accent); width: auto;">
                    <i class="fa-solid fa-magnifying-glass"></i> Explore Accommodations
                </a>
                <a href="/berot/admin/login.php" class="btn-details" style="background: rgba(255,255,255,0.1); color: #FFFFFF;">
                    <i class="fa-solid fa-lock"></i> Access Admin Portal
                </a>
            </div>
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80" alt="CarCanMadCarLan Tourism" style="border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); border: 2px solid rgba(255,255,255,0.15);">
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

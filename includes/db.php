<?php
// includes/db.php - Database connection and automatic schema initialization for 5TownsStays

$db_dir = __DIR__ . '/../database';
if (!file_exists($db_dir)) {
    mkdir($db_dir, 0777, true);
}

$db_file = $db_dir . '/5townsstays.sqlite';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Enable foreign keys
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Create Tables if not exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'tourist',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS municipalities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL,
            description TEXT,
            image_url TEXT
        );

        CREATE TABLE IF NOT EXISTS accommodations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            type TEXT NOT NULL,
            municipality TEXT NOT NULL,
            address TEXT NOT NULL,
            contact_number TEXT NOT NULL,
            email TEXT NOT NULL,
            website TEXT,
            price_start REAL NOT NULL,
            rating REAL DEFAULT 5.0,
            featured INTEGER DEFAULT 0,
            status TEXT DEFAULT 'Active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS amenities (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT UNIQUE NOT NULL,
            icon TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS accommodation_amenities (
            accommodation_id INTEGER NOT NULL,
            amenity_id INTEGER NOT NULL,
            PRIMARY KEY (accommodation_id, amenity_id),
            FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE,
            FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS rooms (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            accommodation_id INTEGER NOT NULL,
            room_name TEXT NOT NULL,
            capacity INTEGER NOT NULL,
            price REAL NOT NULL,
            availability TEXT DEFAULT 'Available',
            FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS photos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            accommodation_id INTEGER NOT NULL,
            image_url TEXT NOT NULL,
            caption TEXT,
            is_primary INTEGER DEFAULT 0,
            FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS inquiries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_name TEXT NOT NULL,
            user_email TEXT NOT NULL,
            user_phone TEXT NOT NULL,
            accommodation_id INTEGER NOT NULL,
            check_in DATE NOT NULL,
            check_out DATE NOT NULL,
            guests INTEGER NOT NULL,
            message TEXT,
            status TEXT DEFAULT 'Pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            accommodation_id INTEGER NOT NULL,
            user_name TEXT NOT NULL,
            rating INTEGER NOT NULL,
            comment TEXT NOT NULL,
            status TEXT DEFAULT 'Approved',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (accommodation_id) REFERENCES accommodations(id) ON DELETE CASCADE
        );
    ");

    // Check if re-seeding is required or if tables exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM accommodations");
    $accCount = $stmt->fetch()['count'];

    // If count < 9, we reset and re-seed with full reference images & details!
    if ($accCount < 9) {
        $pdo->exec("DELETE FROM accommodation_amenities;");
        $pdo->exec("DELETE FROM rooms;");
        $pdo->exec("DELETE FROM photos;");
        $pdo->exec("DELETE FROM inquiries;");
        $pdo->exec("DELETE FROM reviews;");
        $pdo->exec("DELETE FROM accommodations;");
        $pdo->exec("DELETE FROM municipalities;");
        $pdo->exec("DELETE FROM amenities;");
        $pdo->exec("DELETE FROM users;");

        // Seed default Admin User (Password: admin123)
        $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['System Administrator', 'admin@5townsstays.ph', $adminPass, 'admin']);
        $stmt->execute(['Demo Tourist', 'tourist@gmail.com', password_hash('tourist123', PASSWORD_BCRYPT), 'tourist']);

        // Seed Municipalities using reference images
        $munis = [
            ['Cantilan', 'Known as the Cradle of Towns in Surigao del Sur, featuring pristine island beaches like Ayoke, Libtong Cove, Consuelo, and General Island.', '/berot/assets/images/ayoke4.jpg'],
            ['Lanuza', 'The surfing capital of Surigao del Sur, famed for Doot Poktoy surfing spot, marine sanctuaries, and coastal waterfalls.', '/berot/assets/images/option1.jpg'],
            ['Madrid', 'Home to rich agricultural valleys, scenic rivers, and serene eco-tourism nature spots perfect for relaxation.', '/berot/assets/images/option2.jpg'],
            ['Carmen', 'Blessed with natural freshwater cold springs like Hubasan, lush green hills, and warm hospitality.', '/berot/assets/images/option3.jpg'],
            ['Carrascal', 'Gateway town featuring scenic coastal bays, mining heritage, and tranquil island hopping adventures.', '/berot/assets/images/prends.jpg']
        ];
        $stmtM = $pdo->prepare("INSERT INTO municipalities (name, description, image_url) VALUES (?, ?, ?)");
        foreach ($munis as $m) {
            $stmtM->execute($m);
        }

        // Seed Amenities
        $amenities = [
            ['Wi-Fi', 'fa-wifi'],
            ['Swimming Pool', 'fa-swimming-pool'],
            ['Air Conditioning', 'fa-snowflake'],
            ['Free Parking', 'fa-parking'],
            ['Restaurant & Bar', 'fa-utensils'],
            ['Beachfront Access', 'fa-water'],
            ['Family Rooms', 'fa-users'],
            ['Complimentary Breakfast', 'fa-coffee'],
            ['Campsite & Picnic Area', 'fa-campground'],
            ['Island Tour Boat Transfer', 'fa-ship']
        ];
        $stmtA = $pdo->prepare("INSERT INTO amenities (name, icon) VALUES (?, ?)");
        foreach ($amenities as $a) {
            $stmtA->execute($a);
        }

        // Seed Real Accommodations from Reference Website
        $accommodations = [
            [
                'Ayoke Island Eco Lodge & Beach Resort',
                'Experience serene island living at Ayoke Island. Located at Sitio Ayoke, Barangay General Island, Cantilan. Offers pristine white sandy beaches, crystal-clear turquoise waters, boat access, and oceanfront cottages.',
                'Beach Resort',
                'Cantilan',
                'Sitio Ayoke, Barangay General Island, Cantilan, Surigao del Sur',
                '+63 917 888 1234',
                'ayokeisland@cantilan.ph',
                'https://cantilantravels.ph/ayoke',
                2500.00,
                5.0,
                1,
                '/berot/assets/images/ayoke4.jpg'
            ],
            [
                'Inijakan Beach Club & Resort',
                'Exclusive beachfront paradise at Inijakan, General Island, Cantilan. Features luxury seaside villas, private beach access, freshwater showers, and seafood dining.',
                'Beach Resort',
                'Cantilan',
                'Inijakan, General Island, Cantilan, Surigao del Sur',
                '+63 920 777 5678',
                'inijakanbeach@gmail.com',
                'https://cantilantravels.ph/inijakan',
                2800.00,
                4.9,
                1,
                '/berot/assets/images/inijakan1.jpg'
            ],
            [
                'Libtong Cove Resort & Lagoon Lodges',
                'Hidden lagoon sanctuary surrounded by emerald rock formations at General Island, Cantilan. Famous for tranquil waters, kayaking, and eco-lodge accommodation.',
                'Resort',
                'Cantilan',
                'General Island, Cantilan, Surigao del Sur',
                '+63 918 555 9911',
                'libtongcove@yahoo.com',
                '',
                2200.00,
                4.8,
                1,
                '/berot/assets/images/libtong4.jpg'
            ],
            [
                'Rockgem Treasure Island Resort',
                'Majestic island getaway at General Island, Cantilan. Offers panoramic views of rock formations, coral reef snorkeling, and comfortable guest suites.',
                'Beach Resort',
                'Cantilan',
                'General Island, Cantilan, Surigao del Sur',
                '+63 929 444 3322',
                'rockgemtreasure@gmail.com',
                '',
                3000.00,
                4.9,
                1,
                '/berot/assets/images/rockgem1.jpg'
            ],
            [
                'Casa Rica Islet Resort & Suites',
                'Charming coastal islet resort in Barangay Consuelo, Cantilan. Perfect for romantic getaways, family gatherings, and sunset watching.',
                'Resort',
                'Cantilan',
                'Consuelo, Cantilan, Surigao del Sur',
                '+63 912 333 4455',
                'casaricaislet@gmail.com',
                '',
                1800.00,
                4.7,
                0,
                '/berot/assets/images/casarica1.jpg'
            ],
            [
                'Huyamao Beach Park & Campsite',
                'A scenic oceanfront park and beach camping resort at Huyamao Island, Consuelo, Cantilan. Great for beach volleyball, outdoor barbecues, and starlit camping.',
                'Guesthouse',
                'Cantilan',
                'Huyamao Island, Consuelo, Cantilan, Surigao del Sur',
                '+63 908 666 7788',
                'huyamaobeach@gmail.com',
                '',
                1200.00,
                4.6,
                0,
                '/berot/assets/images/huyamao1.jpg'
            ],
            [
                'Mested Beach Resort',
                'Family-friendly seaside resort at Bahang-Bahang, Consuelo, Cantilan. Equipped with air-conditioned cottages, swimming pool, and event pavilions.',
                'Beach Resort',
                'Cantilan',
                'Bahang-Bahang, Consuelo, Cantilan, Surigao del Sur',
                '+63 919 222 1100',
                'mestedbeach@gmail.com',
                '',
                1600.00,
                4.6,
                0,
                '/berot/assets/images/mested5.jpg'
            ],
            [
                'Consuelo Rock Formation Haven & Inn',
                'Unique eco-tourist inn located right by the natural Consuelo Rock Formations in Cantilan. Offers comfortable budget rooms and boat tour packages.',
                'Inn',
                'Cantilan',
                'Barangay Consuelo, Cantilan, Surigao del Sur',
                '+63 928 111 9988',
                'consuelorock@gmail.com',
                '',
                1400.00,
                4.5,
                0,
                '/berot/assets/images/consuelo1.jpg'
            ],
            [
                'Agila White Beach Resort',
                'Pristine white sand beach resort at Sitio Cabitoonan, Consuelo, Cantilan. Offers air-conditioned beachfront rooms, beach umbrella lounges, and island hopping boat transfers.',
                'Beach Resort',
                'Cantilan',
                'Sitio Cabitoonan, Consuelo, Cantilan, Surigao del Sur',
                '+63 917 555 8899',
                'agilawhitebeach@gmail.com',
                '',
                2400.00,
                4.8,
                1,
                '/berot/assets/images/agila1.jpg'
            ],
            [
                'Lanuza Surf & Ocean Lodge',
                'Premier accommodation for surfers and eco-tourists in Lanuza right next to Doot Poktoy surf point. Offers cozy bamboo cottages and surfboard rentals.',
                'Resort',
                'Lanuza',
                'Doot Poktoy Beachfront, Lanuza, Surigao del Sur',
                '+63 920 987 6543',
                'stay@lanuzasurf.com',
                '',
                1850.00,
                4.9,
                1,
                '/berot/assets/images/option1.jpg'
            ],
            [
                'Madrid Green Oasis Hotel',
                'Modern eco-hotel located in Madrid town proper with function halls and local restaurant serving Surigao del Sur delicacies.',
                'Hotel',
                'Madrid',
                'Poblacion Main Street, Madrid, Surigao del Sur',
                '+63 918 333 4455',
                'reservations@madridoasis.com',
                '',
                2100.00,
                4.6,
                0,
                '/berot/assets/images/option2.jpg'
            ],
            [
                'Hubasan Cold Spring Haven',
                'Nestled near the famous cold springs of Carmen, featuring crystal-clear spring water pools and garden pavilions.',
                'Inn',
                'Carmen',
                'Brgy. Hubasan, Carmen, Surigao del Sur',
                '+63 929 555 6677',
                'hubasaninn@gmail.com',
                '',
                1500.00,
                4.7,
                0,
                '/berot/assets/images/option3.jpg'
            ],
            [
                'Carrascal Bayfront Hotel',
                'Overlooking the vibrant bay of Carrascal, offering oceanfront balcony rooms and island hopping boat tour arrangements.',
                'Hotel',
                'Carrascal',
                'Bayfront Drive, Carrascal, Surigao del Sur',
                '+63 939 444 1122',
                'info@carrascalbayhotel.com',
                '',
                2600.00,
                4.7,
                0,
                '/berot/assets/images/prends.jpg'
            ]
        ];

        $stmtAcc = $pdo->prepare("INSERT INTO accommodations (name, description, type, municipality, address, contact_number, email, website, price_start, rating, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($accommodations as $acc) {
            $stmtAcc->execute([
                $acc[0], $acc[1], $acc[2], $acc[3], $acc[4], $acc[5], $acc[6], $acc[7], $acc[8], $acc[9], $acc[10]
            ]);
            $accId = $pdo->lastInsertId();

            // Link Primary Photo
            $stmtPh = $pdo->prepare("INSERT INTO photos (accommodation_id, image_url, caption, is_primary) VALUES (?, ?, ?, ?)");
            $stmtPh->execute([$accId, $acc[11], $acc[0] . ' Primary Photo', 1]);

            // Secondary photos
            $stmtPh->execute([$accId, '/berot/assets/images/option1.jpg', 'Beach View 2', 0]);
            $stmtPh->execute([$accId, '/berot/assets/images/prends.jpg', 'Lounge View 3', 0]);

            // Link Amenities
            $amenityIds = [1, 3, 4, 6, 10];
            $stmtAA = $pdo->prepare("INSERT INTO accommodation_amenities (accommodation_id, amenity_id) VALUES (?, ?)");
            foreach ($amenityIds as $aid) {
                $stmtAA->execute([$accId, $aid]);
            }

            // Seed Rooms
            $stmtRm = $pdo->prepare("INSERT INTO rooms (accommodation_id, room_name, capacity, price, availability) VALUES (?, ?, ?, ?, ?)");
            $stmtRm->execute([$accId, 'Standard Aircon Cottage', 2, $acc[8], 'Available']);
            $stmtRm->execute([$accId, 'Deluxe Family Suite', 4, $acc[8] * 1.5, 'Available']);

            // Seed Reviews from Reference Website (using clean text & star ratings as requested)
            if ($accId == 1 || $accId == 2 || $accId == 3) {
                $stmtRev = $pdo->prepare("INSERT INTO reviews (accommodation_id, user_name, rating, comment, status) VALUES (?, ?, ?, ?, 'Approved')");
                $stmtRev->execute([$accId, 'Liezel Dalisay Ruaza', 5, 'The locations are wonderful and the site is in some ways handy and cost-effective. Best experience in Cantilan!']);
                $stmtRev->execute([$accId, 'Emily Sotoniel', 4, 'Convenient and affordable accommodations close to pristine islands. Highly recommended.']);
                $stmtRev->execute([$accId, 'Angela Ejos', 5, 'The locations they highlight are in some ways suitable for couples who wished to conduct their picture session.']);
                $stmtRev->execute([$accId, 'Carmela Manzano', 4, 'Such a perfect places to unwind and enjoy nature.']);
                $stmtRev->execute([$accId, 'Jessiane Llido', 5, 'Perfect! Pristine waters, clean rooms, and warm hospitality.']);
            }
        }
    }
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}

-- 5TownsStays Database Schema & Data Dump for MySQL / MariaDB
-- Research Project Title: 5TownsStays: A Web-Based Hotel and Resort Information Platform for Tourists in Finding Local Accommodations in CarCanMadCarLan

CREATE DATABASE IF NOT EXISTS `5townsstays` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `5townsstays`;

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tourist') DEFAULT 'tourist',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'System Administrator', 'admin@5townsstays.ph', '$2y$10$e8wF0iB8zO8Bv0s1E2m3n.W5x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'admin'),
(2, 'Demo Tourist', 'tourist@gmail.com', '$2y$10$e8wF0iB8zO8Bv0s1E2m3n.W5x7y8z9a0b1c2d3e4f5g6h7i8j9k', 'tourist');

-- --------------------------------------------------------

-- Table structure for table `municipalities`
CREATE TABLE IF NOT EXISTS `municipalities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL UNIQUE,
  `description` text,
  `image_url` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `municipalities` (`id`, `name`, `description`, `image_url`) VALUES
(1, 'Cantilan', 'Cradle of Towns in Surigao del Sur with pristine white beaches like Ayoke, Libtong Cove, Consuelo, and General Island.', '/berot/assets/images/ayoke4.jpg'),
(2, 'Lanuza', 'Surfing capital of Surigao del Sur famed for Doot Poktoy surf spot.', '/berot/assets/images/option1.jpg'),
(3, 'Madrid', 'Home to rich agricultural valleys, scenic rivers, and serene eco-tourism spots.', '/berot/assets/images/option2.jpg'),
(4, 'Carmen', 'Blessed with natural freshwater cold springs like Hubasan and lush green hills.', '/berot/assets/images/option3.jpg'),
(5, 'Carrascal', 'Gateway town featuring scenic coastal bays and island hopping adventures.', '/berot/assets/images/prends.jpg');

-- --------------------------------------------------------

-- Table structure for table `accommodations`
CREATE TABLE IF NOT EXISTS `accommodations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `municipality` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `website` varchar(150),
  `price_start` decimal(10,2) NOT NULL,
  `rating` decimal(3,1) DEFAULT 5.0,
  `featured` tinyint(1) DEFAULT 0,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `accommodations` (`id`, `name`, `description`, `type`, `municipality`, `address`, `contact_number`, `email`, `website`, `price_start`, `rating`, `featured`, `status`) VALUES
(1, 'Ayoke Island Eco Lodge & Beach Resort', 'Pristine white sandy beaches, crystal-clear turquoise waters, boat access, and oceanfront cottages in Sitio Ayoke, Barangay General Island, Cantilan.', 'Beach Resort', 'Cantilan', 'Sitio Ayoke, Barangay General Island, Cantilan, Surigao del Sur', '+63 917 888 1234', 'ayokeisland@cantilan.ph', 'https://cantilantravels.ph/ayoke', 2500.00, 5.0, 1, 'Active'),
(2, 'Inijakan Beach Club & Resort', 'Exclusive beachfront paradise at Inijakan, General Island, Cantilan with luxury seaside villas and private beach access.', 'Beach Resort', 'Cantilan', 'Inijakan, General Island, Cantilan, Surigao del Sur', '+63 920 777 5678', 'inijakanbeach@gmail.com', 'https://cantilantravels.ph/inijakan', 2800.00, 4.9, 1, 'Active'),
(3, 'Libtong Cove Resort & Lagoon Lodges', 'Hidden lagoon sanctuary surrounded by emerald rock formations at General Island, Cantilan.', 'Resort', 'Cantilan', 'General Island, Cantilan, Surigao del Sur', '+63 918 555 9911', 'libtongcove@yahoo.com', '', 2200.00, 4.8, 1, 'Active'),
(4, 'Rockgem Treasure Island Resort', 'Majestic island getaway at General Island, Cantilan with panoramic views of rock formations and coral reef snorkeling.', 'Beach Resort', 'Cantilan', 'General Island, Cantilan, Surigao del Sur', '+63 929 444 3322', 'rockgemtreasure@gmail.com', '', 3000.00, 4.9, 1, 'Active'),
(5, 'Casa Rica Islet Resort & Suites', 'Charming coastal islet resort in Barangay Consuelo, Cantilan. Perfect for romantic getaways and sunset watching.', 'Resort', 'Cantilan', 'Consuelo, Cantilan, Surigao del Sur', '+63 912 333 4455', 'casaricaislet@gmail.com', '', 1800.00, 4.7, 0, 'Active'),
(6, 'Huyamao Beach Park & Campsite', 'Oceanfront park and beach camping resort at Huyamao Island, Consuelo, Cantilan.', 'Guesthouse', 'Cantilan', 'Huyamao Island, Consuelo, Cantilan, Surigao del Sur', '+63 908 666 7788', 'huyamaobeach@gmail.com', '', 1200.00, 4.6, 0, 'Active'),
(7, 'Mested Beach Resort', 'Family-friendly seaside resort at Bahang-Bahang, Consuelo, Cantilan with swimming pool and event pavilions.', 'Beach Resort', 'Cantilan', 'Bahang-Bahang, Consuelo, Cantilan, Surigao del Sur', '+63 919 222 1100', 'mestedbeach@gmail.com', '', 1600.00, 4.6, 0, 'Active'),
(8, 'Consuelo Rock Formation Haven & Inn', 'Eco-tourist inn located right by the natural Consuelo Rock Formations in Cantilan.', 'Inn', 'Cantilan', 'Barangay Consuelo, Cantilan, Surigao del Sur', '+63 928 111 9988', 'consuelorock@gmail.com', '', 1400.00, 4.5, 0, 'Active'),
(9, 'Agila White Beach Resort', 'Pristine white sand beach resort at Sitio Cabitoonan, Consuelo, Cantilan.', 'Beach Resort', 'Cantilan', 'Sitio Cabitoonan, Consuelo, Cantilan, Surigao del Sur', '+63 917 555 8899', 'agilawhitebeach@gmail.com', '', 2400.00, 4.8, 1, 'Active');

-- --------------------------------------------------------

-- Table structure for table `photos`
CREATE TABLE IF NOT EXISTS `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `caption` varchar(150),
  `is_primary` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `photos` (`accommodation_id`, `image_url`, `caption`, `is_primary`) VALUES
(1, '/berot/assets/images/ayoke4.jpg', 'Ayoke Island View', 1),
(2, '/berot/assets/images/inijakan1.jpg', 'Inijakan Beach Club', 1),
(3, '/berot/assets/images/libtong4.jpg', 'Libtong Cove View', 1),
(4, '/berot/assets/images/rockgem1.jpg', 'Rockgem Treasure Island', 1),
(5, '/berot/assets/images/casarica1.jpg', 'Casa Rica Islet View', 1),
(6, '/berot/assets/images/huyamao1.jpg', 'Huyamao Beach Park', 1),
(7, '/berot/assets/images/mested5.jpg', 'Mested Beach Resort', 1),
(8, '/berot/assets/images/consuelo1.jpg', 'Consuelo Rock Formation', 1),
(9, '/berot/assets/images/agila1.jpg', 'Agila White Beach', 1);

-- --------------------------------------------------------

-- Table structure for table `reviews`
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accommodation_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('Approved','Pending','Rejected') DEFAULT 'Approved',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `reviews` (`accommodation_id`, `user_name`, `rating`, `comment`, `status`) VALUES
(1, 'Liezel Dalisay Ruaza', 5, 'The locations are wonderful and the site is in some ways handy and cost-effective.', 'Approved'),
(1, 'Emily Sotoniel', 4, 'Convenient and affordable', 'Approved'),
(1, 'Angela Ejos', 5, 'The locations they highlight are in some ways suitable for couples who wished to conduct their picture session.', 'Approved'),
(1, 'Carmela Manzano', 4, 'Such a perfect places to unwind.', 'Approved'),
(1, 'Jessiane Llido', 5, 'Perfect', 'Approved');

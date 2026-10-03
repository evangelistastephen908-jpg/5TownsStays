<?php
// includes/header.php - Public Header Navigation
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | 5TownsStays' : '5TownsStays - Local Accommodation Platform for CarCanMadCarLan'; ?></title>
    <meta name="description" content="Discover, compare, and inquire about hotels, resorts, inns, homestays, and guesthouses across Carmen, Cantilan, Madrid, Lanuza, and Carrascal.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="/berot/assets/css/style.css">
</head>
<body>

<!-- Announcement Topbar -->
<div class="top-announcement">
    <div class="container top-announcement-content">
        <span><i class="fa-solid => fa-location-dot"></i> Official Tourism Accommodation Information Directory for <strong>CarCanMadCarLan</strong> (Carmen, Cantilan, Madrid, Lanuza, Carrascal)</span>
        <div class="top-right-links">
            <span class="demo-badge"><i class="fa-solid fa-flask"></i> Research Project Prototype</span>
            <?php if(is_admin()): ?>
                <a href="/berot/admin/dashboard.php" class="admin-top-link"><i class="fa-solid fa-gauge"></i> Admin Dashboard</a>
            <?php else: ?>
                <a href="/berot/admin/login.php" class="admin-top-link"><i class="fa-solid fa-lock"></i> Admin Portal</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<header class="site-header">
    <div class="container header-inner">
        <a href="/berot/index.php" class="brand-logo">
            <div class="logo-icon"><i class="fa-solid fa-compass"></i></div>
            <div class="logo-text">
                <span class="brand-name">5Towns<span>Stays</span></span>
                <span class="brand-tagline">CarCanMadCarLan Directory</span>
            </div>
        </a>

        <button class="mobile-toggle" id="mobileMenuBtn" aria-label="Toggle Navigation Menu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="/berot/index.php" class="<?php echo isset($active_nav) && $active_nav == 'home' ? 'active' : ''; ?>"><i class="fa-solid fa-house"></i> Home</a></li>
                <li><a href="/berot/accommodations.php" class="<?php echo isset($active_nav) && $active_nav == 'accommodations' ? 'active' : ''; ?>"><i class="fa-solid fa-hotel"></i> Accommodations</a></li>
                <li class="nav-dropdown">
                    <a href="#" class="dropdown-trigger"><i class="fa-solid fa-map-location-dot"></i> Municipalities <i class="fa-solid fa-chevron-down arrow-icon"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="/berot/accommodations.php?municipality=Cantilan"><i class="fa-solid fa-umbrella-beach"></i> Cantilan</a></li>
                        <li><a href="/berot/accommodations.php?municipality=Lanuza"><i class="fa-solid fa-water"></i> Lanuza</a></li>
                        <li><a href="/berot/accommodations.php?municipality=Madrid"><i class="fa-solid fa-tree"></i> Madrid</a></li>
                        <li><a href="/berot/accommodations.php?municipality=Carmen"><i class="fa-solid fa-spa"></i> Carmen</a></li>
                        <li><a href="/berot/accommodations.php?municipality=Carrascal"><i class="fa-solid fa-mountain-sun"></i> Carrascal</a></li>
                    </ul>
                </li>
                <li><a href="/berot/index.php#about" class="<?php echo isset($active_nav) && $active_nav == 'about' ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> About Project</a></li>
                <li><a href="/berot/accommodations.php#inquiry-section" class="btn-nav-action"><i class="fa-solid fa-paper-plane"></i> Quick Inquiry</a></li>
            </ul>
        </nav>
    </div>
</header>

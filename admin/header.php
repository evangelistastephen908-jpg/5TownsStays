<?php
// admin/header.php - Admin Template Header & Sidebar
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$currentUser = get_logged_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($admin_page_title) ? $admin_page_title . ' | Admin 5TownsStays' : 'Admin Panel | 5TownsStays'; ?></title>
    
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/berot/assets/css/style.css">
    <link rel="stylesheet" href="/berot/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- Sidebar Navigation -->
<aside class="admin-sidebar">
    <div class="admin-brand">
        <div class="logo-icon"><i class="fa-solid fa-compass"></i></div>
        <div class="logo-text">
            <span class="brand-name" style="color: #FFFFFF; font-size: 1.2rem;">5Towns<span>Stays</span></span>
            <span class="brand-tagline" style="color: #64748B;">Admin Portal</span>
        </div>
    </div>

    <div class="admin-menu">
        <div class="admin-menu-heading">Main Overview</div>
        <a href="/berot/admin/dashboard.php" class="<?php echo isset($admin_active) && $admin_active == 'dashboard' ? 'active' : ''; ?>">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>

        <div class="admin-menu-heading">Management</div>
        <a href="/berot/admin/accommodations.php" class="<?php echo isset($admin_active) && $admin_active == 'accommodations' ? 'active' : ''; ?>">
            <i class="fa-solid fa-hotel"></i> Accommodations
        </a>
        <a href="/berot/admin/rooms.php" class="<?php echo isset($admin_active) && $admin_active == 'rooms' ? 'active' : ''; ?>">
            <i class="fa-solid fa-bed"></i> Rooms & Rates
        </a>
        <a href="/berot/admin/inquiries.php" class="<?php echo isset($admin_active) && $admin_active == 'inquiries' ? 'active' : ''; ?>">
            <i class="fa-solid fa-paper-plane"></i> Inquiries
        </a>
        <a href="/berot/admin/reviews.php" class="<?php echo isset($admin_active) && $admin_active == 'reviews' ? 'active' : ''; ?>">
            <i class="fa-solid fa-comments"></i> Reviews & Ratings
        </a>
        <a href="/berot/admin/amenities.php" class="<?php echo isset($admin_active) && $admin_active == 'amenities' ? 'active' : ''; ?>">
            <i class="fa-solid fa-list-check"></i> Amenities & Towns
        </a>
        <a href="/berot/admin/users.php" class="<?php echo isset($admin_active) && $admin_active == 'users' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users-gear"></i> System Users
        </a>

        <div class="admin-menu-heading">Quick Links</div>
        <a href="/berot/index.php" target="_blank">
            <i class="fa-solid fa-globe"></i> View Public Site
        </a>
        <a href="/berot/admin/logout.php" style="color: #EF4444;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout Session
        </a>
    </div>
</aside>

<!-- Main Wrapper -->
<div class="admin-main">
    <!-- Topbar -->
    <header class="admin-topbar">
        <div>
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 700;">
                <?php echo isset($admin_page_title) ? $admin_page_title : 'Admin Dashboard'; ?>
            </h3>
        </div>

        <div class="admin-user-info">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($currentUser['name'], 0, 1)); ?>
            </div>
            <div>
                <strong style="font-size: 0.9rem; display: block;"><?php echo htmlspecialchars($currentUser['name']); ?></strong>
                <small style="color: #64748B; font-size: 0.75rem;"><?php echo htmlspecialchars($currentUser['email']); ?></small>
            </div>
        </div>
    </header>

    <div class="admin-content">

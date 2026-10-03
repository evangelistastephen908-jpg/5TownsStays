<?php
// admin/logout.php - Destroy Admin Session
require_once __DIR__ . '/../includes/auth.php';

session_unset();
session_destroy();

header("Location: /berot/admin/login.php?logged_out=1");
exit();

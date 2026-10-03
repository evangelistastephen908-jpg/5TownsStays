<?php
// admin/login.php - Admin Authentication Page
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if (is_admin()) {
    header("Location: /berot/admin/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            header("Location: /berot/admin/dashboard.php");
            exit();
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    } else {
        $error = 'Please fill in both email and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | 5TownsStays Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/berot/assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); color: #FFFFFF; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">

<div style="background: #FFFFFF; color: #1E293B; width: 100%; max-width: 440px; border-radius: 20px; padding: 36px; box-shadow: 0 20px 50px rgba(0,0,0,0.4);">
    <div style="text-align: center; margin-bottom: 28px;">
        <div style="width: 56px; height: 56px; background: #0288D1; color: #FFFFFF; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 14px auto;">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800;">5TownsStays Admin</h2>
        <p style="color: #64748B; font-size: 0.9rem;">Sign in to access accommodation management</p>
    </div>

    <?php if (!empty($error)): ?>
        <div style="background: #FEE2E2; color: #991B1B; padding: 12px 16px; border-radius: 8px; font-size: 0.88rem; margin-bottom: 20px; font-weight: 600;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="/berot/admin/login.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
        <div class="form-group">
            <label style="color: #64748B;">Admin Email Address</label>
            <input type="email" name="email" class="form-control" required placeholder="admin@5townsstays.ph" value="admin@5townsstays.ph">
        </div>

        <div class="form-group">
            <label style="color: #64748B;">Password</label>
            <input type="password" name="password" class="form-control" required placeholder="••••••••" value="admin123">
        </div>

        <button type="submit" class="btn-search" style="width: 100%; margin-top: 10px;">
            <i class="fa-solid fa-right-to-bracket"></i> Sign In to Dashboard
        </button>
    </form>

    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #E2E8F0; text-align: center;">
        <p style="font-size: 0.82rem; color: #64748B;">
            <strong>Demo Admin Credentials:</strong><br>
            Email: <code style="background:#F1F5F9; padding:2px 6px; border-radius:4px; color:#0288D1;">admin@5townsstays.ph</code><br>
            Password: <code style="background:#F1F5F9; padding:2px 6px; border-radius:4px; color:#0288D1;">admin123</code>
        </p>
        <div style="margin-top: 14px;">
            <a href="/berot/index.php" style="font-size: 0.88rem; color: #0288D1; font-weight: 600;"><i class="fa-solid fa-arrow-left"></i> Back to Tourist Website</a>
        </div>
    </div>
</div>

</body>
</html>

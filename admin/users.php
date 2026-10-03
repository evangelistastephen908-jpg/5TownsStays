<?php
// admin/users.php - Manage System Users
$admin_page_title = "Manage System Users";
$admin_active = "users";

require_once __DIR__ . '/header.php';

// Add User Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    if (!empty($name) && !empty($email) && !empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmtIns = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmtIns->execute([$name, $email, $hash, $role]);
        header("Location: /berot/admin/users.php?msg=added");
        exit();
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-users-gear" style="color:#0288D1;"></i> System Accounts</h3>
    </div>

    <!-- Add Admin User Form -->
    <div style="background: #F8FAFC; padding: 20px; border-radius: 8px; border: 1px solid #E2E8F0; margin-bottom: 24px;">
        <h4 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; margin-bottom: 14px;"><i class="fa-solid fa-user-plus" style="color:#0288D1;"></i> Create New Admin / User Account</h4>
        <form action="/berot/admin/users.php" method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr 140px 120px; gap: 12px; align-items: flex-end;">
            <input type="hidden" name="add_user" value="1">

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Name">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="email@domain.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="admin">Admin</option>
                    <option value="tourist">Tourist</option>
                </select>
            </div>

            <button type="submit" class="btn-sm btn-primary" style="height: 48px; justify-content: center;">
                <i class="fa-solid fa-plus"></i> Create
            </button>
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>System Role</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td>#<?php echo $u['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <span class="badge-status <?php echo $u['role'] === 'admin' ? 'active' : 'pending'; ?>">
                            <?php echo strtoupper(htmlspecialchars($u['role'])); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

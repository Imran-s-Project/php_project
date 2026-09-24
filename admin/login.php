<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: ' . BASE_URL . '/admin/dashboard.php');
        exit;
    } else {
        $error = 'ইউজারনেম অথবা পাসওয়ার্ড ভুল হয়েছে।';
    }
}

$pageTitle = 'অ্যাডমিন লগইন';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="card form-narrow">
  <h2><i class="fa-solid fa-user-shield"></i> অ্যাডমিন লগইন</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="field">
      <label for="username">ইউজারনেম</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="field">
      <label for="password">পাসওয়ার্ড</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">লগইন</button>
  </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

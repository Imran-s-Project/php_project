<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch();

    if ($student && password_verify($password, $student['password'])) {
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['name'];
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit;
    } else {
        $error = 'ইমেইল অথবা পাসওয়ার্ড ভুল হয়েছে।';
    }
}

$pageTitle = 'লগইন';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card form-narrow">
  <h2>লগইন করুন</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="field">
      <label for="email">ইমেইল</label>
      <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="field">
      <label for="password">পাসওয়ার্ড</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">লগইন</button>
  </form>
  <p style="margin-top:16px; font-size:0.9rem;">অ্যাকাউন্ট নেই? <a href="<?= BASE_URL ?>/register.php">রেজিস্ট্রেশন করুন</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

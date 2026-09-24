<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'সবগুলো ফিল্ড পূরণ করুন।';
    } elseif (strlen($password) < 6) {
        $error = 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।';
    } else {
        $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'এই ইমেইল দিয়ে আগেই রেজিস্ট্রেশন করা হয়েছে।';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO students (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            $_SESSION['student_id'] = $pdo->lastInsertId();
            $_SESSION['student_name'] = $name;
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit;
        }
    }
}

$pageTitle = 'রেজিস্ট্রেশন';
require_once __DIR__ . '/includes/header.php';
?>
<div class="card form-narrow">
  <h2>নতুন অ্যাকাউন্ট তৈরি করুন</h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="field">
      <label for="name">পূর্ণ নাম</label>
      <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
    </div>
    <div class="field">
      <label for="email">ইমেইল</label>
      <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
    </div>
    <div class="field">
      <label for="password">পাসওয়ার্ড</label>
      <input type="password" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary btn-block">রেজিস্ট্রেশন করুন</button>
  </form>
  <p style="margin-top:16px; font-size:0.9rem;">ইতিমধ্যে অ্যাকাউন্ট আছে? <a href="<?= BASE_URL ?>/login.php">লগইন করুন</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

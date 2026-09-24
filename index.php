<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$exams = $pdo->query("SELECT id, title, description, duration_minutes, pass_percentage FROM exams WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'সকল পরীক্ষা';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <h1>নিজেকে যাচাই করুন, প্রস্তুতি নিন পরবর্তী ধাপের জন্য</h1>
  <p>টাইমার সহ MCQ পরীক্ষা দিন, সাথে সাথে ফলাফল ও প্রতিটি প্রশ্নের ব্যাখ্যা দেখুন।</p>
  <?php if (!isStudentLoggedIn()): ?>
    <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary">শুরু করুন — ফ্রি রেজিস্ট্রেশন</a>
  <?php endif; ?>
</section>

<h2>চলমান পরীক্ষাসমূহ</h2>
<?php if (empty($exams)): ?>
  <p>এই মুহূর্তে কোনো পরীক্ষা চালু নেই। পরে আবার দেখুন।</p>
<?php else: ?>
  <div class="exam-grid">
    <?php foreach ($exams as $exam): ?>
      <div class="exam-card">
        <h3><?= e($exam['title']) ?></h3>
        <p><?= e($exam['description']) ?></p>
        <div class="exam-meta">
          <span><i class="fa-regular fa-clock"></i> <?= (int)$exam['duration_minutes'] ?> মিনিট</span>
          <span><i class="fa-solid fa-check-circle"></i> পাস <?= (int)$exam['pass_percentage'] ?>%</span>
        </div>
        <?php if (isStudentLoggedIn()): ?>
          <a href="<?= BASE_URL ?>/exam.php?id=<?= $exam['id'] ?>" class="btn btn-primary btn-block">পরীক্ষা শুরু করুন</a>
        <?php else: ?>
          <a href="<?= BASE_URL ?>/login.php" class="btn btn-outline btn-block">দিতে লগইন করুন</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

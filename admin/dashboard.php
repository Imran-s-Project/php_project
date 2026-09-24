<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$totalExams = $pdo->query("SELECT COUNT(*) FROM exams")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalAttempts = $pdo->query("SELECT COUNT(*) FROM attempts WHERE submitted_at IS NOT NULL")->fetchColumn();
$totalQuestions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();

$pageTitle = 'অ্যাডমিন ড্যাশবোর্ড';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1>অ্যাডমিন ড্যাশবোর্ড</h1>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-value"><?= $totalExams ?></div><div class="stat-label">মোট পরীক্ষা</div></div>
  <div class="stat-card"><div class="stat-value"><?= $totalQuestions ?></div><div class="stat-label">মোট প্রশ্ন</div></div>
  <div class="stat-card"><div class="stat-value"><?= $totalStudents ?></div><div class="stat-label">রেজিস্টার্ড শিক্ষার্থী</div></div>
  <div class="stat-card"><div class="stat-value"><?= $totalAttempts ?></div><div class="stat-label">মোট পরীক্ষা প্রদান</div></div>
</div>

<div class="exam-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px,1fr));">
  <a href="<?= BASE_URL ?>/admin/exams.php" class="exam-card" style="text-decoration:none;">
    <h3><i class="fa-solid fa-file-lines"></i> পরীক্ষা পরিচালনা</h3>
    <p style="color:var(--graphite); font-size:0.9rem;">নতুন পরীক্ষা তৈরি, সম্পাদনা ও প্রশ্ন যোগ করুন</p>
  </a>
  <a href="<?= BASE_URL ?>/admin/results.php" class="exam-card" style="text-decoration:none;">
    <h3><i class="fa-solid fa-chart-simple"></i> ফলাফল দেখুন</h3>
    <p style="color:var(--graphite); font-size:0.9rem;">সকল শিক্ষার্থীর ফলাফল ও পারফরম্যান্স</p>
  </a>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

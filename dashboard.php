<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireStudent();

$studentId = $_SESSION['student_id'];

$exams = $pdo->query("SELECT id, title, description, duration_minutes, pass_percentage FROM exams WHERE is_active = 1 ORDER BY created_at DESC")->fetchAll();

$historyStmt = $pdo->prepare("
    SELECT a.id, a.score, a.max_score, a.correct_count, a.wrong_count, a.submitted_at, e.title, e.pass_percentage
    FROM attempts a JOIN exams e ON a.exam_id = e.id
    WHERE a.student_id = ? AND a.submitted_at IS NOT NULL
    ORDER BY a.submitted_at DESC
");
$historyStmt->execute([$studentId]);
$history = $historyStmt->fetchAll();

$pageTitle = 'ড্যাশবোর্ড';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-head">
  <h1>স্বাগতম, <?= e($_SESSION['student_name']) ?></h1>
</div>

<h2>উপলব্ধ পরীক্ষা</h2>
<?php if (empty($exams)): ?>
  <p>এই মুহূর্তে কোনো পরীক্ষা চালু নেই।</p>
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
        <a href="<?= BASE_URL ?>/exam.php?id=<?= $exam['id'] ?>" class="btn btn-primary btn-block">পরীক্ষা শুরু করুন</a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<h2 style="margin-top:40px;">আমার কার্যক্রম</h2>
<?php if (empty($history)): ?>
  <p>এখনো কোনো পরীক্ষা দেননি।</p>
<?php else: ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>পরীক্ষা</th><th>স্কোর</th><th>সঠিক/ভুল</th><th>ফলাফল</th><th>তারিখ</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($history as $h):
        $percent = $h['max_score'] > 0 ? ($h['score'] / $h['max_score']) * 100 : 0;
        $passed = $percent >= $h['pass_percentage'];
      ?>
        <tr>
          <td><?= e($h['title']) ?></td>
          <td><?= $h['score'] ?> / <?= $h['max_score'] ?></td>
          <td><?= $h['correct_count'] ?> / <?= $h['wrong_count'] ?></td>
          <td><span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>"><?= $passed ? 'পাস' : 'ফেইল' ?></span></td>
          <td><?= date('d M Y', strtotime($h['submitted_at'])) ?></td>
          <td><a href="<?= BASE_URL ?>/result.php?id=<?= $h['id'] ?>">বিস্তারিত</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

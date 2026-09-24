<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireStudent();

$studentId = $_SESSION['student_id'];
$attemptId = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT a.*, e.title, e.pass_percentage
    FROM attempts a JOIN exams e ON a.exam_id = e.id
    WHERE a.id = ? AND a.student_id = ?
");
$stmt->execute([$attemptId, $studentId]);
$attempt = $stmt->fetch();

if (!$attempt) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$ansStmt = $pdo->prepare("
    SELECT q.*, aa.selected_option, aa.is_correct
    FROM attempt_answers aa JOIN questions q ON aa.question_id = q.id
    WHERE aa.attempt_id = ?
    ORDER BY q.id ASC
");
$ansStmt->execute([$attemptId]);
$reviews = $ansStmt->fetchAll();

$percent = $attempt['max_score'] > 0 ? round(($attempt['score'] / $attempt['max_score']) * 100, 1) : 0;
$passed = $percent >= $attempt['pass_percentage'];

$pageTitle = 'ফলাফল - ' . $attempt['title'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="result-hero">
  <div style="color:var(--graphite); margin-bottom:8px;"><?= e($attempt['title']) ?></div>
  <div class="result-score <?= $passed ? 'result-pass' : 'result-fail' ?>"><?= $attempt['score'] ?> / <?= $attempt['max_score'] ?></div>
  <div style="margin-top:6px; font-size:1.1rem;">
    <span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>"><?= $passed ? 'পাস করেছেন' : 'ফেইল করেছেন' ?></span>
    <span style="color:var(--graphite); margin-left:8px;"><?= $percent ?>%</span>
  </div>
  <div style="margin-top:20px; display:flex; gap:24px; justify-content:center; color:var(--graphite); font-size:0.9rem; flex-wrap:wrap;">
    <span><i class="fa-solid fa-check" style="color:var(--mark-green);"></i> সঠিক: <?= $attempt['correct_count'] ?></span>
    <span><i class="fa-solid fa-xmark" style="color:var(--pen-red);"></i> ভুল: <?= $attempt['wrong_count'] ?></span>
    <span><i class="fa-regular fa-circle"></i> উত্তর দেননি: <?= $attempt['unanswered_count'] ?></span>
  </div>
  <button onclick="window.print()" class="btn btn-outline no-print" style="margin-top:20px;">
    <i class="fa-solid fa-download"></i> PDF ডাউনলোড করুন
  </button>
</div>

<h2>বিস্তারিত পর্যালোচনা</h2>
<?php foreach ($reviews as $i => $q): ?>
  <div class="question-block">
    <span class="question-number">প্রশ্ন <?= $i + 1 ?></span>
    <div class="question-text"><?= e($q['question_text']) ?></div>
    <div class="options">
      <?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $key => $col):
        $cls = '';
        if ($key === $q['correct_option']) { $cls = 'is-correct'; }
        elseif ($key === $q['selected_option']) { $cls = 'is-wrong'; }
      ?>
        <div class="option-bubble <?= $cls ?>">
          <span class="bubble"></span>
          <span class="option-label"><?= e($q[$col]) ?></span>
          <?php if ($key === $q['correct_option']): ?><i class="fa-solid fa-check" style="color:var(--mark-green); margin-left:auto;"></i><?php endif; ?>
          <?php if ($key === $q['selected_option'] && $key !== $q['correct_option']): ?><i class="fa-solid fa-xmark" style="color:var(--pen-red); margin-left:auto;"></i><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <?php if (!empty($q['explanation'])): ?>
      <div class="explanation"><i class="fa-regular fa-lightbulb"></i> <?= e($q['explanation']) ?></div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
requireStudent();

$studentId = $_SESSION['student_id'];
$examId = (int)($_GET['id'] ?? $_POST['exam_id'] ?? 0);

$examStmt = $pdo->prepare("SELECT * FROM exams WHERE id = ? AND is_active = 1");
$examStmt->execute([$examId]);
$exam = $examStmt->fetch();

if (!$exam) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

$qStmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ? ORDER BY id ASC");
$qStmt->execute([$examId]);
$questions = $qStmt->fetchAll();

// Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = $_POST['answer'] ?? [];
    $correctCount = 0;
    $wrongCount = 0;
    $unansweredCount = 0;
    $score = 0;
    $maxScore = 0;
    $answerRows = [];

    foreach ($questions as $q) {
        $maxScore += (int)$q['marks'];
        $selected = $answers[$q['id']] ?? null;
        if ($selected === null || $selected === '') {
            $unansweredCount++;
            $answerRows[] = [$q['id'], null, 0];
            continue;
        }
        if ($selected === $q['correct_option']) {
            $correctCount++;
            $score += (int)$q['marks'];
            $answerRows[] = [$q['id'], $selected, 1];
        } else {
            $wrongCount++;
            $score -= (float)$exam['negative_marking'];
            $answerRows[] = [$q['id'], $selected, 0];
        }
    }
    if ($score < 0) { $score = 0; }

    $pdo->beginTransaction();
    $insAttempt = $pdo->prepare("
        INSERT INTO attempts (student_id, exam_id, total_questions, correct_count, wrong_count, unanswered_count, score, max_score, submitted_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $insAttempt->execute([$studentId, $examId, count($questions), $correctCount, $wrongCount, $unansweredCount, $score, $maxScore]);
    $attemptId = $pdo->lastInsertId();

    $insAns = $pdo->prepare("INSERT INTO attempt_answers (attempt_id, question_id, selected_option, is_correct) VALUES (?, ?, ?, ?)");
    foreach ($answerRows as $row) {
        $insAns->execute([$attemptId, $row[0], $row[1], $row[2]]);
    }
    $pdo->commit();

    header('Location: ' . BASE_URL . '/result.php?id=' . $attemptId);
    exit;
}

$pageTitle = $exam['title'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="timer-bar">
  <div>
    <strong><?= e($exam['title']) ?></strong>
    <div style="color:var(--graphite); font-size:0.85rem;"><?= count($questions) ?>টি প্রশ্ন &middot; নেগেটিভ মার্কিং <?= e($exam['negative_marking']) ?></div>
  </div>
  <div id="timer-display" class="timer-display" data-seconds="<?= (int)$exam['duration_minutes'] * 60 ?>">--:--</div>
</div>

<?php if (empty($questions)): ?>
  <p>এই পরীক্ষায় এখনো কোনো প্রশ্ন যোগ করা হয়নি।</p>
<?php else: ?>
<form id="exam-form" method="post">
  <input type="hidden" name="exam_id" value="<?= $examId ?>">
  <?php foreach ($questions as $i => $q): ?>
    <div class="question-block">
      <span class="question-number">প্রশ্ন <?= $i + 1 ?> / <?= count($questions) ?></span>
      <div class="question-text"><?= e($q['question_text']) ?></div>
      <div class="options">
        <?php foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $key => $col): ?>
          <label class="option-bubble">
            <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $key ?>">
            <span class="bubble"></span>
            <span class="option-label"><?= e($q[$col]) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <button type="submit" class="btn btn-primary btn-block">পরীক্ষা জমা দিন</button>
</form>
<script src="<?= BASE_URL ?>/assets/js/exam.js"></script>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

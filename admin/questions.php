<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$examId = (int)($_GET['exam_id'] ?? 0);
$examStmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
$examStmt->execute([$examId]);
$exam = $examStmt->fetch();
if (!$exam) {
    header('Location: ' . BASE_URL . '/admin/exams.php');
    exit;
}

$error = '';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM questions WHERE id = ? AND exam_id = ?")->execute([(int)$_GET['delete'], $examId]);
    header('Location: ' . BASE_URL . '/admin/questions.php?exam_id=' . $examId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qText = trim($_POST['question_text'] ?? '');
    $a = trim($_POST['option_a'] ?? '');
    $b = trim($_POST['option_b'] ?? '');
    $c = trim($_POST['option_c'] ?? '');
    $d = trim($_POST['option_d'] ?? '');
    $correct = $_POST['correct_option'] ?? 'A';
    $explanation = trim($_POST['explanation'] ?? '');
    $marks = (int)($_POST['marks'] ?? 1);

    if ($qText === '' || $a === '' || $b === '' || $c === '' || $d === '') {
        $error = 'সবগুলো ফিল্ড পূরণ করুন।';
    } else {
        $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question_text, option_a, option_b, option_c, option_d, correct_option, explanation, marks) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$examId, $qText, $a, $b, $c, $d, $correct, $explanation, $marks]);
        header('Location: ' . BASE_URL . '/admin/questions.php?exam_id=' . $examId);
        exit;
    }
}

$qStmt = $pdo->prepare("SELECT * FROM questions WHERE exam_id = ? ORDER BY id ASC");
$qStmt->execute([$examId]);
$questions = $qStmt->fetchAll();

$pageTitle = 'প্রশ্ন: ' . $exam['title'];
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1><?= e($exam['title']) ?> — প্রশ্নসমূহ</h1>
  <a href="<?= BASE_URL ?>/admin/exams.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> পরীক্ষার তালিকা</a>
</div>

<div class="card" style="margin-bottom:30px;">
  <h3>নতুন প্রশ্ন যোগ করুন</h3>
  <p style="color:var(--graphite); font-size:0.88rem;">অনেকগুলো প্রশ্ন একসাথে যোগ করতে চাইলে প্রজেক্টের সাথে দেওয়া <code>bulk_import.py</code> স্ক্রিপ্ট ব্যবহার করুন।</p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="field">
      <label>প্রশ্ন</label>
      <textarea name="question_text" rows="2" required></textarea>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
      <div class="field"><label>অপশন A</label><input type="text" name="option_a" required></div>
      <div class="field"><label>অপশন B</label><input type="text" name="option_b" required></div>
      <div class="field"><label>অপশন C</label><input type="text" name="option_c" required></div>
      <div class="field"><label>অপশন D</label><input type="text" name="option_d" required></div>
    </div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
      <div class="field">
        <label>সঠিক উত্তর</label>
        <select name="correct_option">
          <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
        </select>
      </div>
      <div class="field"><label>মার্ক</label><input type="number" name="marks" value="1" min="1"></div>
    </div>
    <div class="field">
      <label>ব্যাখ্যা (ঐচ্ছিক)</label>
      <textarea name="explanation" rows="2" placeholder="ফলাফলে শিক্ষার্থীকে দেখানো হবে, কেন এই উত্তরটি সঠিক"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">প্রশ্ন যোগ করুন</button>
  </form>
</div>

<div class="table-wrap">
  <table>
    <thead><tr><th>প্রশ্ন</th><th>সঠিক উত্তর</th><th>মার্ক</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($questions as $q): ?>
      <tr>
        <td><?= e(mb_substr($q['question_text'], 0, 70)) ?><?= mb_strlen($q['question_text']) > 70 ? '…' : '' ?></td>
        <td><span class="badge badge-success"><?= e($q['correct_option']) ?></span></td>
        <td><?= $q['marks'] ?></td>
        <td><a href="?exam_id=<?= $examId ?>&delete=<?= $q['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('মুছে ফেলবেন?')">মুছুন</a></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($questions)): ?><tr><td colspan="4">এখনো কোনো প্রশ্ন যোগ করা হয়নি।</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

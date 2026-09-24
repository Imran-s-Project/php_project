<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$error = '';
$editExam = null;

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM exams WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: ' . BASE_URL . '/admin/exams.php');
    exit;
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM exams WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editExam = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $duration = (int)($_POST['duration_minutes'] ?? 30);
    $negMark = (float)($_POST['negative_marking'] ?? 0);
    $passPct = (int)($_POST['pass_percentage'] ?? 40);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $examId = (int)($_POST['exam_id'] ?? 0);

    if ($title === '') {
        $error = 'পরীক্ষার নাম আবশ্যক।';
    } elseif ($examId > 0) {
        $stmt = $pdo->prepare("UPDATE exams SET title=?, description=?, duration_minutes=?, negative_marking=?, pass_percentage=?, is_active=? WHERE id=?");
        $stmt->execute([$title, $description, $duration, $negMark, $passPct, $isActive, $examId]);
        header('Location: ' . BASE_URL . '/admin/exams.php');
        exit;
    } else {
        $stmt = $pdo->prepare("INSERT INTO exams (title, description, duration_minutes, negative_marking, pass_percentage, is_active) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$title, $description, $duration, $negMark, $passPct, $isActive]);
        header('Location: ' . BASE_URL . '/admin/exams.php');
        exit;
    }
}

$exams = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM questions q WHERE q.exam_id = e.id) as question_count FROM exams e ORDER BY e.created_at DESC")->fetchAll();

$pageTitle = 'পরীক্ষা পরিচালনা';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1>পরীক্ষা পরিচালনা</h1>
  <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> ড্যাশবোর্ড</a>
</div>

<div class="card" style="margin-bottom:30px;">
  <h3><?= $editExam ? 'পরীক্ষা সম্পাদনা করুন' : 'নতুন পরীক্ষা তৈরি করুন' ?></h3>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <input type="hidden" name="exam_id" value="<?= $editExam['id'] ?? 0 ?>">
    <div class="field">
      <label>পরীক্ষার নাম</label>
      <input type="text" name="title" value="<?= e($editExam['title'] ?? '') ?>" required>
    </div>
    <div class="field">
      <label>বিবরণ</label>
      <textarea name="description" rows="2"><?= e($editExam['description'] ?? '') ?></textarea>
    </div>
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:16px;">
      <div class="field">
        <label>সময় (মিনিট)</label>
        <input type="number" name="duration_minutes" value="<?= e($editExam['duration_minutes'] ?? 30) ?>" min="1" required>
      </div>
      <div class="field">
        <label>নেগেটিভ মার্কিং</label>
        <input type="number" step="0.01" name="negative_marking" value="<?= e($editExam['negative_marking'] ?? 0) ?>" min="0">
      </div>
      <div class="field">
        <label>পাস শতাংশ (%)</label>
        <input type="number" name="pass_percentage" value="<?= e($editExam['pass_percentage'] ?? 40) ?>" min="0" max="100">
      </div>
    </div>
    <div class="field">
      <label style="display:flex; align-items:center; gap:8px;"><input type="checkbox" name="is_active" <?= (!isset($editExam) || $editExam['is_active']) ? 'checked' : '' ?> style="width:auto;"> সক্রিয় (শিক্ষার্থীরা দেখতে পাবে)</label>
    </div>
    <button type="submit" class="btn btn-primary"><?= $editExam ? 'আপডেট করুন' : 'তৈরি করুন' ?></button>
    <?php if ($editExam): ?><a href="<?= BASE_URL ?>/admin/exams.php" class="btn btn-outline">বাতিল</a><?php endif; ?>
  </form>
</div>

<div class="table-wrap">
  <table>
    <thead><tr><th>নাম</th><th>প্রশ্ন</th><th>সময়</th><th>স্ট্যাটাস</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($exams as $ex): ?>
      <tr>
        <td><?= e($ex['title']) ?></td>
        <td><?= $ex['question_count'] ?></td>
        <td><?= $ex['duration_minutes'] ?> মিনিট</td>
        <td><span class="badge <?= $ex['is_active'] ? 'badge-success' : 'badge-muted' ?>"><?= $ex['is_active'] ? 'সক্রিয়' : 'নিষ্ক্রিয়' ?></span></td>
        <td style="white-space:nowrap;">
          <a href="<?= BASE_URL ?>/admin/questions.php?exam_id=<?= $ex['id'] ?>" class="btn btn-outline btn-sm">প্রশ্ন</a>
          <a href="?edit=<?= $ex['id'] ?>" class="btn btn-outline btn-sm">সম্পাদনা</a>
          <a href="?delete=<?= $ex['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('আপনি কি নিশ্চিত? এই পরীক্ষার সকল প্রশ্ন ও ফলাফলও মুছে যাবে।')">মুছুন</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

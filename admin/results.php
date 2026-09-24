<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$results = $pdo->query("
    SELECT a.id, a.score, a.max_score, a.correct_count, a.wrong_count, a.submitted_at,
           s.name as student_name, s.email, e.title as exam_title, e.pass_percentage
    FROM attempts a
    JOIN students s ON a.student_id = s.id
    JOIN exams e ON a.exam_id = e.id
    WHERE a.submitted_at IS NOT NULL
    ORDER BY a.submitted_at DESC
    LIMIT 200
")->fetchAll();

$pageTitle = 'সকল ফলাফল';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1>সকল ফলাফল</h1>
  <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> ড্যাশবোর্ড</a>
</div>

<div class="table-wrap">
  <table>
    <thead><tr><th>শিক্ষার্থী</th><th>পরীক্ষা</th><th>স্কোর</th><th>ফলাফল</th><th>তারিখ</th></tr></thead>
    <tbody>
    <?php foreach ($results as $r):
      $percent = $r['max_score'] > 0 ? ($r['score'] / $r['max_score']) * 100 : 0;
      $passed = $percent >= $r['pass_percentage'];
    ?>
      <tr>
        <td><?= e($r['student_name']) ?><br><span style="color:var(--graphite); font-size:0.82rem;"><?= e($r['email']) ?></span></td>
        <td><?= e($r['exam_title']) ?></td>
        <td><?= $r['score'] ?> / <?= $r['max_score'] ?></td>
        <td><span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>"><?= $passed ? 'পাস' : 'ফেইল' ?></span></td>
        <td><?= date('d M Y, h:i A', strtotime($r['submitted_at'])) ?></td>
      </tr>
    <?php endforeach; ?>
    <?php if (empty($results)): ?><tr><td colspan="5">এখনো কেউ পরীক্ষা দেয়নি।</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php if (!isset($pageTitle)) { $pageTitle = 'পরীক্ষা'; } ?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — Exam Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Spectral:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600&family=Noto+Sans+Bengali:wght@400;500;600&family=Noto+Serif+Bengali:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="navbar-inner">
    <a href="<?= BASE_URL ?>/index.php" class="brand"><i class="fa-solid fa-graduation-cap"></i> Exam Portal</a>
    <div class="nav-links">
      <?php if (isStudentLoggedIn()): ?>
        <a href="<?= BASE_URL ?>/dashboard.php">ড্যাশবোর্ড</a>
        <a href="<?= BASE_URL ?>/logout.php">লগআউট</a>
      <?php elseif (isAdminLoggedIn()): ?>
        <a href="<?= BASE_URL ?>/admin/dashboard.php">অ্যাডমিন প্যানেল</a>
        <a href="<?= BASE_URL ?>/admin/logout.php">লগআউট</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/login.php">লগইন</a>
        <a href="<?= BASE_URL ?>/register.php" class="nav-cta">রেজিস্ট্রেশন</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<main class="main-content">

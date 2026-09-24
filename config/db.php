<?php
// Database connection settings — update for your server
define('DB_HOST', 'localhost');
define('DB_NAME', 'exam_system');
define('DB_USER', 'root');
define('DB_PASS', '');

// If the site lives in a subfolder (e.g. localhost/exam_website), set that
// path here, e.g. define('BASE_URL', '/exam_website'); Leave empty for root.
define('BASE_URL', '');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("ডাটাবেস কানেকশন ব্যর্থ হয়েছে: " . $e->getMessage());
}

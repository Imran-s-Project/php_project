<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isStudentLoggedIn() {
    return isset($_SESSION['student_id']);
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireStudent() {
    if (!isStudentLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

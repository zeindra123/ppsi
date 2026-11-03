<?php
/**
 * LangkapuraLokal - Helper Autentikasi & Sesi
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'Pengguna',
        'email' => $_SESSION['user_email'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'owner',
    ];
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

function isOwner() {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'owner';
}

function requireLogin($redirect = '/login.php') {
    if (!isLoggedIn()) {
        setFlash('warning', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
        header("Location: $redirect");
        exit;
    }
}

function requireAdmin($redirect = '/login.php') {
    requireLogin($redirect);
    if (!isAdmin()) {
        setFlash('danger', 'Akses ditolak. Halaman ini hanya diperuntukkan bagi Administrator.');
        header("Location: /index.php");
        exit;
    }
}

function requireOwner($redirect = '/login.php') {
    requireLogin($redirect);
    if (!isOwner() && !isAdmin()) {
        setFlash('danger', 'Akses ditolak. Halaman ini hanya untuk Pemilik Usaha.');
        header("Location: /index.php");
        exit;
    }
}

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

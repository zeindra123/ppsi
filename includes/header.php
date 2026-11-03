<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$base = getBaseUrl();
$currentUser = getCurrentUser();
$flash = getFlash();

$pageTitle = isset($pageTitle) ? $pageTitle . ' — LangkapuraLokal' : 'LangkapuraLokal — Direktori Digital UMKM Langkapura';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= sanitize($pageTitle) ?></title>
  
  <!-- SEO & Meta -->
  <meta name="description" content="Direktori resmi digital UMKM Kecamatan Langkapura, Kota Bandar Lampung. Temukan usaha kuliner, toko, dan jasa lokal lalu hubungi langsung via WhatsApp.">
  <meta name="theme-color" content="#176B45">
  
  <!-- Google Fonts: Plus Jakarta Sans & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5.3 CSS & Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.php" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" onerror="this.onerror=null;this.href='https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css';">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Custom Design System -->
  <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>

  <!-- Banner Prototipe Data Dummy Sesuai desaind.md -->
  <div class="prototype-banner">
    <div class="container d-flex align-items-center justify-content-center gap-2">
      <i class="bi bi-info-circle-fill"></i>
      <span><strong>Mode Prototipe:</strong> Data yang ditampilkan merupakan data simulasi/dummy untuk pengujian dan demonstrasi sistem direktori UMKM Langkapura.</span>
    </div>
  </div>

  <!-- Navbar Utama -->
  <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
      <a class="navbar-brand" href="<?= $base ?>/index.php">
        <i class="bi bi-shop-window text-success fs-3"></i>
        <span>Langkapura<span style="color: #2E8B57;">Lokal</span></span>
        <span class="brand-badge d-none d-sm-inline-block">Direktori UMKM</span>
      </a>

      <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigasi">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
          <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>" href="<?= $base ?>/index.php">Beranda</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'katalog.php') ? 'active' : '' ?>" href="<?= $base ?>/katalog.php">Jelajahi Usaha</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $base ?>/index.php#kategori">Kategori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= $base ?>/index.php#cara-kerja">Cara Kerja</a>
          </li>
        </ul>

        <div class="d-flex align-items-center gap-2 flex-wrap">
          <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
              <a href="<?= $base ?>/admin/index.php" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-shield-lock-fill me-1"></i> Dashboard Admin
              </a>
            <?php else: ?>
              <a href="<?= $base ?>/owner/index.php" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-grid-fill me-1"></i> Dashboard Usaha
              </a>
              <a href="<?= $base ?>/owner/tambah.php" class="btn btn-primary-custom btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Daftarkan Usaha
              </a>
            <?php endif; ?>
            <div class="dropdown">
              <button class="btn btn-light btn-sm border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle me-1 text-success"></i> <?= sanitize($currentUser['name']) ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li><span class="dropdown-item-text small text-muted"><?= sanitize($currentUser['email']) ?> (<?= ucfirst($currentUser['role']) ?>)</span></li>
                <li><hr class="dropdown-divider"></li>
                <?php if (isAdmin()): ?>
                  <li><a class="dropdown-item" href="<?= $base ?>/admin/index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</a></li>
                  <li><a class="dropdown-item" href="<?= $base ?>/admin/usaha.php"><i class="bi bi-card-checklist me-2"></i>Moderasi Usaha</a></li>
                <?php else: ?>
                  <li><a class="dropdown-item" href="<?= $base ?>/owner/index.php"><i class="bi bi-shop me-2"></i>Kelola Usaha Saya</a></li>
                <?php endif; ?>
                <li><a class="dropdown-item text-danger" href="<?= $base ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="<?= $base ?>/login.php" class="btn btn-outline-custom btn-sm">
              <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Pemilik
            </a>
            <a href="<?= $base ?>/register.php" class="btn btn-primary-custom btn-sm">
              <i class="bi bi-plus-circle me-1"></i> Daftarkan Usaha
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <!-- Flash Message Notifikasi -->
  <?php if ($flash): ?>
    <div class="container mt-3">
      <div class="alert alert-<?= sanitize($flash['type']) ?> alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="bi bi-<?= ($flash['type'] === 'success') ? 'check-circle-fill' : 'exclamation-circle-fill' ?> me-2"></i>
        <?= sanitize($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>
  <?php endif; ?>

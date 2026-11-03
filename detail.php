<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDB();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$slug = sanitize($_GET['slug'] ?? '');

if ($id > 0) {
    $stmt = $pdo->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon, u.name as owner_name
        FROM businesses b
        JOIN categories c ON b.category_id = c.id
        JOIN users u ON b.owner_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
} elseif (!empty($slug)) {
    $stmt = $pdo->prepare("
        SELECT b.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon, u.name as owner_name
        FROM businesses b
        JOIN categories c ON b.category_id = c.id
        JOIN users u ON b.owner_id = u.id
        WHERE b.slug = ?
    ");
    $stmt->execute([$slug]);
} else {
    header("Location: " . getBaseUrl() . "/katalog.php");
    exit;
}

$business = $stmt->fetch();

if (!$business) {
    $pageTitle = 'Usaha Tidak Ditemukan';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <div class="container py-5 text-center">
      <div class="card border-0 shadow-sm p-5 mx-auto bg-white rounded-4" style="max-width: 520px;">
        <i class="bi bi-shop-window text-muted display-3 mb-3"></i>
        <h2 class="h4 fw-bold">Usaha Tidak Ditemukan</h2>
        <p class="text-muted small">Profil usaha yang Anda cari tidak tersedia atau mungkin tautan sudah berubah.</p>
        <a href="<?= getBaseUrl() ?>/katalog.php" class="btn btn-primary-custom btn-sm mt-2">
          Kembali ke Katalog Usaha
        </a>
      </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Hak akses: Jika belum disetujui, hanya admin atau pemilik asli yang dapat melihat
$currentUser = getCurrentUser();
$isOwnerOrAdmin = false;
if ($currentUser) {
    if (isAdmin() || $currentUser['id'] == $business['owner_id']) {
        $isOwnerOrAdmin = true;
    }
}

if ($business['status'] !== 'approved' && !$isOwnerOrAdmin) {
    $pageTitle = 'Usaha Sedang Dalam Moderasi';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <div class="container py-5 text-center">
      <div class="card border-0 shadow-sm p-5 mx-auto bg-white rounded-4" style="max-width: 520px;">
        <i class="bi bi-hourglass-split text-warning display-3 mb-3"></i>
        <h2 class="h4 fw-bold">Profil Usaha Belum Publik</h2>
        <p class="text-muted small">Usaha ini sedang dalam tahap moderasi atau verifikasi oleh administrator LangkapuraLokal.</p>
        <a href="<?= getBaseUrl() ?>/katalog.php" class="btn btn-outline-custom btn-sm mt-2">
          Jelajahi Usaha Lainnya
        </a>
      </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $business['name'];
require_once __DIR__ . '/includes/header.php';

// Parse produk / jasa jika ada
$products = [];
if (!empty($business['products_list'])) {
    $lines = explode("\n", trim($business['products_list']));
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $products[] = $line;
        }
    }
}
?>

<!-- Breadcrumbs -->
<div class="py-3 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-0">
        <li class="breadcrumb-item"><a href="<?= $base ?>/index.php" class="text-decoration-none text-muted">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= $base ?>/katalog.php" class="text-decoration-none text-muted">Katalog</a></li>
        <li class="breadcrumb-item"><a href="<?= $base ?>/katalog.php?kategori=<?= urlencode($business['category_slug']) ?>" class="text-decoration-none text-muted"><?= sanitize($business['category_name']) ?></a></li>
        <li class="breadcrumb-item active text-success" aria-current="page"><?= sanitize($business['name']) ?></li>
      </ol>
    </nav>
  </div>
</div>

<div class="container py-4">
  <?php if ($business['status'] !== 'approved'): ?>
    <div class="alert alert-warning mb-4 shadow-sm border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div>
        <i class="bi bi-info-circle-fill me-2"></i>
        <strong>Perhatian Pengelola:</strong> Usaha ini saat ini berstatus <strong><?= getStatusText($business['status']) ?></strong> dan belum ditampilkan kepada publik luas.
        <?php if (!empty($business['moderation_note'])): ?>
          <div class="mt-1 small"><strong>Catatan Admin:</strong> <em>"<?= sanitize($business['moderation_note']) ?>"</em></div>
        <?php endif; ?>
      </div>
      <?php if (isLoggedIn() && $currentUser['id'] == $business['owner_id']): ?>
        <a href="<?= $base ?>/owner/edit.php?id=<?= $business['id'] ?>" class="btn btn-sm btn-warning">
          <i class="bi bi-pencil-square me-1"></i> Edit Data Usaha
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Konten Utama Usaha -->
    <div class="col-lg-8">
      <div class="bg-white rounded-4 border p-4 shadow-sm mb-4">
        <!-- Foto Utama -->
        <div class="mb-4 text-center">
          <img src="<?= sanitize($business['image_path']) ?>" alt="<?= sanitize($business['name']) ?>" class="detail-img-main" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80';">
        </div>

        <!-- Header Info -->
        <div class="d-flex flex-wrap gap-2 mb-2 align-items-center">
          <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill small fw-semibold">
            <i class="bi <?= sanitize($business['category_icon']) ?> me-1"></i> <?= sanitize($business['category_name']) ?>
          </span>
          <span class="badge-kelurahan">
            <i class="bi bi-geo-alt"></i> Kel. <?= sanitize($business['kelurahan']) ?>
          </span>
          <?= getStatusBadge($business['status']) ?>
        </div>

        <h1 class="h2 fw-bold text-dark mb-3"><?= sanitize($business['name']) ?></h1>

        <div class="text-muted small mb-4 d-flex flex-wrap gap-3">
          <span><i class="bi bi-person me-1"></i> Pemilik: <?= sanitize($business['owner_name']) ?></span>
          <span><i class="bi bi-calendar3 me-1"></i> Terdaftar: <?= date('d M Y', strtotime($business['created_at'])) ?></span>
        </div>

        <hr>

        <!-- Deskripsi Usaha -->
        <h4 class="h5 fw-bold mb-3">Tentang Usaha</h4>
        <div class="text-secondary mb-4" style="line-height: 1.8;">
          <?= nl2br(sanitize($business['description'])) ?>
        </div>

        <!-- Daftar Produk / Jasa Unggulan Sesuai desaind.md -->
        <h4 class="h5 fw-bold mb-3">Daftar Produk / Jasa & Perkiraan Harga</h4>
        <?php if (!empty($products)): ?>
          <div class="list-group list-group-flush border rounded-3 mb-4">
            <?php foreach ($products as $prod): ?>
              <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-check2-circle text-success me-2 fs-5"></i>
                  <span class="fw-medium text-dark"><?= sanitize($prod) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="alert alert-light border small text-muted mb-4">
            <i class="bi bi-info-circle me-1"></i> Informasi rincian produk belum dicantumkan secara detail oleh pemilik. Silakan hubungi via WhatsApp untuk katalog lengkap.
          </div>
        <?php endif; ?>

        <!-- Catatan Keaslian Data Sesuai desaind.md -->
        <div class="p-3 bg-light rounded-3 border small text-muted">
          <i class="bi bi-shield-check text-success me-1"></i>
          <strong>Informasi Fakta:</strong> Seluruh informasi profil, harga, dan jam operasional disusun berdasarkan pengajuan pemilik usaha di Kecamatan Langkapura dan telah melalui tahap pemeriksaan awal direktori.
        </div>
      </div>
    </div>

    <!-- Sidebar Kontak & Operasional Sesuai desaind.md -->
    <div class="col-lg-4">
      <div class="info-sidebar-card mb-4 sticky-lg-top" style="top: 90px;">
        <h4 class="h5 fw-bold mb-3">Hubungi Langsung</h4>
        <p class="small text-muted mb-3">
          Terhubung tanpa perantara untuk menanyakan ketersediaan produk, layanan, atau janji temu.
        </p>

        <!-- Tombol WhatsApp Paling Menonjol -->
        <?php if (!empty($business['whatsapp_number'])): ?>
          <a href="<?= buildWhatsAppUrl($business['whatsapp_number'], $business['name']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-wa w-100 justify-content-center py-3 mb-3">
            <i class="bi bi-whatsapp fs-5"></i>
            <span>Hubungi via WhatsApp</span>
          </a>
          <div class="text-center small text-muted mb-4">
            <i class="bi bi-telephone-fill me-1"></i> <?= sanitize($business['whatsapp_number']) ?>
          </div>
        <?php else: ?>
          <div class="alert alert-secondary small text-center mb-3">
            <i class="bi bi-telephone-x me-1"></i> Nomor kontak belum tersedia
          </div>
        <?php endif; ?>

        <hr>

        <h5 class="h6 fw-bold mb-3">Informasi Operasional</h5>

        <div class="mb-3">
          <div class="small fw-semibold text-dark mb-1">
            <i class="bi bi-clock-history text-success me-1"></i> Jam Operasional:
          </div>
          <div class="small text-muted ps-3">
            <?= sanitize($business['operating_hours'] ?: 'Informasi jam buka fleksibel / hubungi penjual') ?>
          </div>
        </div>

        <div class="mb-3">
          <div class="small fw-semibold text-dark mb-1">
            <i class="bi bi-geo-alt-fill text-success me-1"></i> Alamat Lokasi:
          </div>
          <div class="small text-muted ps-3 mb-2">
            <?= sanitize($business['address']) ?>
            <div class="mt-1">
              <strong>Kelurahan:</strong> <?= sanitize($business['kelurahan']) ?>, Kec. Langkapura, Kota Bandar Lampung
            </div>
          </div>

          <?php if (!empty($business['maps_url'])): ?>
            <div class="ps-3">
              <a href="<?= sanitize($business['maps_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary w-100 text-start">
                <i class="bi bi-map-fill text-danger me-1"></i> Buka Petunjuk Arah di Google Maps
              </a>
            </div>
          <?php endif; ?>
        </div>

        <hr>

        <div class="small text-muted">
          <i class="bi bi-info-circle me-1"></i> Butuh bantuan dengan data ini? Hubungi admin kelurahan atau pengelola direktori LangkapuraLokal.
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

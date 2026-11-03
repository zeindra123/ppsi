<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$currentUser = getCurrentUser();
$pdo = getDB();

// Ambil semua usaha milik pengguna
$stmt = $pdo->prepare("
    SELECT b.*, c.name as category_name, c.icon as category_icon
    FROM businesses b
    JOIN categories c ON b.category_id = c.id
    WHERE b.owner_id = ?
    ORDER BY b.id DESC
");
$stmt->execute([$currentUser['id']]);
$myBusinesses = $stmt->fetchAll();

// Hitung statistik
$total = count($myBusinesses);
$approved = 0;
$pending = 0;
$rejected = 0;

foreach ($myBusinesses as $biz) {
    if ($biz['status'] === 'approved') $approved++;
    elseif ($biz['status'] === 'pending') $pending++;
    elseif ($biz['status'] === 'rejected') $rejected++;
}

$pageTitle = 'Dashboard Pemilik Usaha';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill small fw-semibold">Dashboard Pemilik</span>
        <h1 class="h3 fw-bold mb-1">Kelola Profil Usaha Anda</h1>
        <p class="text-muted small mb-0">Selamat datang, <strong><?= sanitize($currentUser['name']) ?></strong>. Pantau status pengajuan dan perbarui informasi toko Anda.</p>
      </div>
      <div>
        <a href="<?= $base ?>/owner/tambah.php" class="btn btn-primary-custom">
          <i class="bi bi-plus-circle me-1"></i> Ajukan Usaha Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <!-- Ringkasan Statistik Metrik Sesuai desaind.md -->
  <div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-light text-primary">
          <i class="bi bi-shop"></i>
        </div>
        <div>
          <div class="text-muted small">Total Usaha</div>
          <div class="h4 fw-bold mb-0"><?= $total ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-success-soft text-success">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
          <div class="text-muted small">Disetujui Publik</div>
          <div class="h4 fw-bold mb-0 text-success"><?= $approved ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-warning-soft text-warning">
          <i class="bi bi-clock-history"></i>
        </div>
        <div>
          <div class="text-muted small">Menunggu Moderasi</div>
          <div class="h4 fw-bold mb-0 text-warning"><?= $pending ?></div>
        </div>
      </div>
    </div>

    <div class="col-md-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-danger-soft text-danger">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
          <div class="text-muted small">Perlu Perbaikan</div>
          <div class="h4 fw-bold mb-0 text-danger"><?= $rejected ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Petunjuk Sederhana untuk Pengguna Nonteknis -->
  <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 mb-4 rounded-3">
    <i class="bi bi-lightbulb-fill text-info fs-3"></i>
    <div class="small">
      <strong>Alur Moderasi:</strong> Setiap usaha baru atau pembaruan informasi akan ditinjau oleh tim admin Kelurahan Langkapura untuk menjaga keabsahan data sebelum tayang di katalog publik.
    </div>
  </div>

  <!-- Daftar Usaha Milik Pengguna Sesuai desaind.md -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0 h6">Daftar Usaha Anda</h5>
      <span class="badge bg-light text-muted border"><?= count($myBusinesses) ?> Data</span>
    </div>

    <?php if (empty($myBusinesses)): ?>
      <div class="text-center py-5 px-3">
        <i class="bi bi-shop text-muted display-4"></i>
        <h5 class="mt-3 fw-bold">Belum Ada Usaha yang Didaftarkan</h5>
        <p class="text-muted small mx-auto mb-3" style="max-width: 420px;">
          Anda belum memiliki profil usaha yang terdaftar di LangkapuraLokal. Daftarkan usaha Anda sekarang agar warga sekitar mudah menemukan toko Anda!
        </p>
        <a href="<?= $base ?>/owner/tambah.php" class="btn btn-primary-custom btn-sm">
          <i class="bi bi-plus-circle me-1"></i> Ajukan Usaha Pertama
        </a>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Usaha</th>
              <th>Kategori</th>
              <th>Kelurahan</th>
              <th>Kontak WhatsApp</th>
              <th>Status Moderasi</th>
              <th>Catatan Admin</th>
              <th class="text-end pe-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($myBusinesses as $biz): ?>
              <tr>
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?= sanitize($biz['image_path']) ?>" alt="<?= sanitize($biz['name']) ?>" class="rounded-3" style="width: 52px; height: 52px; object-fit: cover;" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&auto=format&fit=crop&q=80';">
                    <div>
                      <div class="fw-bold text-dark"><?= sanitize($biz['name']) ?></div>
                      <div class="small text-muted text-truncate" style="max-width: 220px;"><?= sanitize($biz['address']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    <i class="bi <?= sanitize($biz['category_icon']) ?> me-1"></i> <?= sanitize($biz['category_name']) ?>
                  </span>
                </td>
                <td>
                  <span class="small fw-medium">Kel. <?= sanitize($biz['kelurahan']) ?></span>
                </td>
                <td>
                  <a href="<?= buildWhatsAppUrl($biz['whatsapp_number'], $biz['name']) ?>" target="_blank" class="btn btn-sm btn-outline-success text-nowrap py-1">
                    <i class="bi bi-whatsapp me-1"></i> <?= sanitize($biz['whatsapp_number']) ?>
                  </a>
                </td>
                <td>
                  <?= getStatusBadge($biz['status']) ?>
                </td>
                <td>
                  <?php if (!empty($biz['moderation_note'])): ?>
                    <span class="small text-danger bg-danger-soft p-1 px-2 rounded-2 d-inline-block" style="max-width: 250px;">
                      <i class="bi bi-info-circle me-1"></i> <?= sanitize($biz['moderation_note']) ?>
                    </span>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-end pe-4 text-nowrap">
                  <a href="<?= $base ?>/detail.php?id=<?= $biz['id'] ?>" class="btn btn-sm btn-light border" title="Lihat Tampilan">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a href="<?= $base ?>/owner/edit.php?id=<?= $biz['id'] ?>" class="btn btn-sm btn-outline-custom" title="Edit Data">
                    <i class="bi bi-pencil-square me-1"></i> Edit
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

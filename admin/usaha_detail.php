<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$currentUser = getCurrentUser();
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: " . getBaseUrl() . "/admin/usaha.php");
    exit;
}

// Ambil data usaha lengkap dengan pemilik dan kategori
$stmt = $pdo->prepare("
    SELECT b.*, c.name as category_name, c.icon as category_icon, 
           u.name as owner_name, u.email as owner_email, u.phone as owner_phone
    FROM businesses b
    JOIN categories c ON b.category_id = c.id
    JOIN users u ON b.owner_id = u.id
    WHERE b.id = ?
");
$stmt->execute([$id]);
$biz = $stmt->fetch();

if (!$biz) {
    setFlash('danger', 'Data usaha tidak ditemukan.');
    header("Location: " . getBaseUrl() . "/admin/usaha.php");
    exit;
}

// Proses form moderasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'pending';
    $note = trim($_POST['moderation_note'] ?? '');

    $stmtUpdate = $pdo->prepare("
        UPDATE businesses SET
            status = :status,
            moderation_note = :note,
            reviewed_by = :admin_id,
            reviewed_at = CURRENT_TIMESTAMP,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");

    $stmtUpdate->execute([
        ':status' => $status,
        ':note' => $note,
        ':admin_id' => $currentUser['id'],
        ':id' => $id
    ]);

    setFlash('success', 'Keputusan moderasi untuk ' . $biz['name'] . ' berhasil disimpan!');
    header("Location: " . getBaseUrl() . "/admin/usaha.php");
    exit;
}

$pageTitle = 'Periksa Usaha: ' . $biz['name'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-1">
        <li class="breadcrumb-item"><a href="<?= $base ?>/admin/index.php" class="text-decoration-none text-muted">Dashboard Admin</a></li>
        <li class="breadcrumb-item"><a href="<?= $base ?>/admin/usaha.php" class="text-decoration-none text-muted">Data Usaha</a></li>
        <li class="breadcrumb-item active text-success" aria-current="page">Periksa Usaha</li>
      </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
      <h1 class="h3 fw-bold mb-0">Pemeriksaan & Moderasi Usaha</h1>
      <?= getStatusBadge($biz['status']) ?>
    </div>
  </div>
</div>

<div class="container py-4">
  <div class="row g-4">
    <!-- Kolom Detail Data Usaha -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Profil Pengajuan</h5>

        <div class="text-center mb-4">
          <img src="<?= sanitize($biz['image_path']) ?>" alt="" class="rounded-3 shadow-sm" style="max-height: 260px; width: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80';">
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-borderless">
            <tr>
              <th style="width: 170px;" class="text-muted">Nama Usaha:</th>
              <td class="fw-bold text-dark"><?= sanitize($biz['name']) ?></td>
            </tr>
            <tr>
              <th class="text-muted">Kategori:</th>
              <td><i class="bi <?= sanitize($biz['category_icon']) ?> text-success me-1"></i> <?= sanitize($biz['category_name']) ?></td>
            </tr>
            <tr>
              <th class="text-muted">Wilayah Kelurahan:</th>
              <td><span class="badge-kelurahan">Kel. <?= sanitize($biz['kelurahan']) ?></span></td>
            </tr>
            <tr>
              <th class="text-muted">Alamat Lengkap:</th>
              <td><?= sanitize($biz['address']) ?></td>
            </tr>
            <tr>
              <th class="text-muted">WhatsApp Usaha:</th>
              <td>
                <a href="<?= buildWhatsAppUrl($biz['whatsapp_number'], $biz['name']) ?>" target="_blank" class="text-success fw-medium">
                  <i class="bi bi-whatsapp me-1"></i> <?= sanitize($biz['whatsapp_number']) ?>
                </a>
              </td>
            </tr>
            <tr>
              <th class="text-muted">Jam Operasional:</th>
              <td><?= sanitize($biz['operating_hours'] ?: '-') ?></td>
            </tr>
            <tr>
              <th class="text-muted">Google Maps URL:</th>
              <td>
                <?php if (!empty($biz['maps_url'])): ?>
                  <a href="<?= sanitize($biz['maps_url']) ?>" target="_blank" class="small text-truncate d-inline-block" style="max-width: 320px;">
                    <?= sanitize($biz['maps_url']) ?>
                  </a>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
            </tr>
          </table>
        </div>

        <h6 class="fw-bold mt-3 mb-2">Deskripsi Usaha:</h6>
        <div class="p-3 bg-light rounded-3 text-secondary small mb-3">
          <?= nl2br(sanitize($biz['description'])) ?>
        </div>

        <h6 class="fw-bold mb-2">Daftar Produk / Jasa:</h6>
        <div class="p-3 bg-light rounded-3 text-secondary small">
          <?= nl2br(sanitize($biz['products_list'] ?: 'Tidak ada rincian produk/jasa yang dicantumkan.')) ?>
        </div>
      </div>

      <!-- Informasi Akun Pemilik -->
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Informasi Akun Pemilik</h5>
        <div class="row g-2 small">
          <div class="col-sm-4 text-muted">Nama Pemilik:</div>
          <div class="col-sm-8 fw-semibold text-dark"><?= sanitize($biz['owner_name']) ?></div>

          <div class="col-sm-4 text-muted">Email Akun:</div>
          <div class="col-sm-8 text-dark"><?= sanitize($biz['owner_email']) ?></div>

          <div class="col-sm-4 text-muted">No. HP Pemilik:</div>
          <div class="col-sm-8 text-dark"><?= sanitize($biz['owner_phone'] ?: '-') ?></div>
        </div>
      </div>
    </div>

    <!-- Kolom Keputusan Moderasi -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-lg-top" style="top: 90px;">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Formulir Keputusan Moderasi</h5>

        <form action="<?= $base ?>/admin/usaha_detail.php?id=<?= $biz['id'] ?>" method="POST">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Pilih Status Publikasi <span class="text-danger">*</span></label>

            <div class="form-check p-3 rounded-3 border mb-2 <?= ($biz['status'] === 'approved') ? 'border-success bg-success-soft' : '' ?>">
              <input class="form-check-input" type="radio" name="status" id="statusApproved" value="approved" <?= ($biz['status'] === 'approved') ? 'checked' : '' ?> required>
              <label class="form-check-label fw-bold text-success" for="statusApproved">
                <i class="bi bi-check-circle-fill me-1"></i> Disetujui (Tayang di Publik)
              </label>
              <div class="small text-muted ps-4">Usaha memenuhi kriteria dan langsung dapat dicari di katalog umum.</div>
            </div>

            <div class="form-check p-3 rounded-3 border mb-2 <?= ($biz['status'] === 'rejected') ? 'border-danger bg-danger-soft' : '' ?>">
              <input class="form-check-input" type="radio" name="status" id="statusRejected" value="rejected" <?= ($biz['status'] === 'rejected') ? 'checked' : '' ?>>
              <label class="form-check-label fw-bold text-danger" for="statusRejected">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Perlu Perbaikan (Ditolak Sementara)
              </label>
              <div class="small text-muted ps-4">Data kurang lengkap atau perlu diperbaiki pemilik sebelum dapat disetujui.</div>
            </div>

            <div class="form-check p-3 rounded-3 border mb-2 <?= ($biz['status'] === 'inactive') ? 'border-secondary bg-light' : '' ?>">
              <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" <?= ($biz['status'] === 'inactive') ? 'checked' : '' ?>>
              <label class="form-check-label fw-bold text-secondary" for="statusInactive">
                <i class="bi bi-slash-circle me-1"></i> Nonaktifkan
              </label>
              <div class="small text-muted ps-4">Sembunyikan dari katalog publik (misal: usaha tutup sementara).</div>
            </div>

            <div class="form-check p-3 rounded-3 border mb-2 <?= ($biz['status'] === 'pending') ? 'border-warning bg-warning-soft' : '' ?>">
              <input class="form-check-input" type="radio" name="status" id="statusPending" value="pending" <?= ($biz['status'] === 'pending') ? 'checked' : '' ?>>
              <label class="form-check-label fw-bold text-warning" for="statusPending">
                <i class="bi bi-clock-history me-1"></i> Tunda / Tetap Menunggu Moderasi
              </label>
              <div class="small text-muted ps-4">Simpan untuk ditinjau lebih lanjut nanti.</div>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label small fw-semibold">Catatan Moderasi untuk Pemilik Usaha</label>
            <textarea name="moderation_note" class="form-control" rows="4" placeholder="Tuliskan catatan verifikasi atau alasan perbaikan jika ditolak..."><?= sanitize($biz['moderation_note']) ?></textarea>
            <div class="form-text" style="font-size: 0.75rem;">Catatan ini akan dapat dibaca langsung oleh pemilik pada dashboard mereka.</div>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary-custom py-2">
              <i class="bi bi-save2-fill me-1"></i> Simpan Keputusan Moderasi
            </button>
            <a href="<?= $base ?>/admin/usaha.php" class="btn btn-outline-secondary">
              Kembali ke Daftar Usaha
            </a>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$currentUser = getCurrentUser();
$pdo = getDB();

// Menangani aksi cepat persetujuan dari dashboard jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $bizId = (int)($_POST['biz_id'] ?? 0);
    $action = $_POST['action'];
    $note = trim($_POST['moderation_note'] ?? '');

    if ($bizId > 0) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE businesses SET status = 'approved', moderation_note = 'Disetujui oleh admin', reviewed_by = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$currentUser['id'], $bizId]);
            setFlash('success', 'Usaha berhasil disetujui dan kini tampil di katalog publik!');
        } elseif ($action === 'reject') {
            $note = !empty($note) ? $note : 'Data usaha belum lengkap atau membutuhkan perbaikan alamat/kontak.';
            $stmt = $pdo->prepare("UPDATE businesses SET status = 'rejected', moderation_note = ?, reviewed_by = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$note, $currentUser['id'], $bizId]);
            setFlash('warning', 'Pengajuan usaha ditolak / diminta perbaikan kepada pemilik.');
        }
        header("Location: " . getBaseUrl() . "/admin/index.php");
        exit;
    }
}

// Hitung metrik ringkasan
$totalBiz = $pdo->query("SELECT COUNT(*) FROM businesses")->fetchColumn();
$pendingBiz = $pdo->query("SELECT COUNT(*) FROM businesses WHERE status = 'pending'")->fetchColumn();
$approvedBiz = $pdo->query("SELECT COUNT(*) FROM businesses WHERE status = 'approved'")->fetchColumn();
$rejectedBiz = $pdo->query("SELECT COUNT(*) FROM businesses WHERE status = 'rejected'")->fetchColumn();
$totalCats = $pdo->query("SELECT COUNT(*) FROM categories WHERE status = 'active'")->fetchColumn();

// Ambil antrean usaha yang sedang MENUNGGU MODERASI (pending)
$stmtQueue = $pdo->query("
    SELECT b.*, c.name as category_name, c.icon as category_icon, u.name as owner_name, u.email as owner_email
    FROM businesses b
    JOIN categories c ON b.category_id = c.id
    JOIN users u ON b.owner_id = u.id
    WHERE b.status = 'pending'
    ORDER BY b.id ASC
");
$pendingQueue = $stmtQueue->fetchAll();

$pageTitle = 'Dashboard Admin & Moderasi';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
      <div>
        <span class="badge bg-dark text-white px-2 py-1 rounded-pill small fw-semibold">Panel Administrator</span>
        <h1 class="h3 fw-bold mb-1">Pusat Kendali Direktori Langkapura</h1>
        <p class="text-muted small mb-0">Moderasi pengajuan UMKM, kelola kategori, dan pantau status publikasi direktori.</p>
      </div>
      <div class="d-flex gap-2">
        <a href="<?= $base ?>/admin/usaha.php" class="btn btn-outline-custom btn-sm">
          <i class="bi bi-card-checklist me-1"></i> Semua Data Usaha
        </a>
        <a href="<?= $base ?>/admin/kategori.php" class="btn btn-outline-dark btn-sm">
          <i class="bi bi-tags me-1"></i> Kelola Kategori
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <!-- Ringkasan Statistik Metrik Sesuai desaind.md -->
  <div class="row g-3 mb-4">
    <div class="col-lg-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-warning-soft text-warning">
          <i class="bi bi-clock-history"></i>
        </div>
        <div>
          <div class="text-muted small">Menunggu Moderasi</div>
          <div class="h3 fw-bold mb-0 text-warning"><?= $pendingBiz ?></div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-success-soft text-success">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
          <div class="text-muted small">Usaha Disetujui</div>
          <div class="h3 fw-bold mb-0 text-success"><?= $approvedBiz ?></div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-danger-soft text-danger">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        <div>
          <div class="text-muted small">Perlu Perbaikan</div>
          <div class="h3 fw-bold mb-0 text-danger"><?= $rejectedBiz ?></div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-6">
      <div class="dashboard-card d-flex align-items-center gap-3">
        <div class="stat-icon bg-light text-primary">
          <i class="bi bi-tags-fill"></i>
        </div>
        <div>
          <div class="text-muted small">Kategori Aktif</div>
          <div class="h3 fw-bold mb-0"><?= $totalCats ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Antrean Pengajuan yang Menunggu Moderasi -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-inbox-fill text-warning fs-5"></i>
        <h5 class="fw-bold mb-0 h6">Antrean Moderasi Pengajuan Baru (<?= count($pendingQueue) ?>)</h5>
      </div>
      <span class="badge bg-warning-soft text-warning fw-semibold">Prioritas Tindakan</span>
    </div>

    <?php if (empty($pendingQueue)): ?>
      <div class="text-center py-5 px-3">
        <i class="bi bi-check-circle text-success display-4"></i>
        <h5 class="mt-3 fw-bold text-success">Semua Pengajuan Telah Dimoderasi</h5>
        <p class="text-muted small mx-auto mb-0" style="max-width: 420px;">
          Tidak ada pengajuan usaha yang sedang menunggu peninjauan saat ini.
        </p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Usaha & Pemilik</th>
              <th>Kategori</th>
              <th>Kelurahan</th>
              <th>Tanggal Ajuan</th>
              <th>Kontak WA</th>
              <th class="text-end pe-4">Keputusan Cepat</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($pendingQueue as $item): ?>
              <tr>
                <td class="ps-4 py-3">
                  <div class="fw-bold text-dark"><?= sanitize($item['name']) ?></div>
                  <div class="small text-muted">
                    <i class="bi bi-person me-1"></i> <?= sanitize($item['owner_name']) ?> (<?= sanitize($item['owner_email']) ?>)
                  </div>
                  <div class="small text-muted text-truncate" style="max-width: 280px;">
                    <i class="bi bi-geo-alt me-1"></i> <?= sanitize($item['address']) ?>
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    <i class="bi <?= sanitize($item['category_icon']) ?> me-1"></i> <?= sanitize($item['category_name']) ?>
                  </span>
                </td>
                <td>
                  <span class="small fw-medium">Kel. <?= sanitize($item['kelurahan']) ?></span>
                </td>
                <td>
                  <span class="small text-muted"><?= date('d M Y, H:i', strtotime($item['created_at'])) ?></span>
                </td>
                <td>
                  <span class="small text-success fw-medium"><?= sanitize($item['whatsapp_number']) ?></span>
                </td>
                <td class="text-end pe-4 text-nowrap">
                  <a href="<?= $base ?>/admin/usaha_detail.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-light border" title="Periksa Lengkap">
                    <i class="bi bi-search me-1"></i> Periksa
                  </a>

                  <!-- Form Quick Approve -->
                  <form action="<?= $base ?>/admin/index.php" method="POST" class="d-inline" onsubmit="return confirm('Setujui usaha ini untuk ditampilkan ke direktori publik?');">
                    <input type="hidden" name="biz_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn btn-sm btn-success">
                      <i class="bi bi-check-lg me-1"></i> Setujui
                    </button>
                  </form>

                  <!-- Button Trigger Modal Reject -->
                  <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $item['id'] ?>">
                    <i class="bi bi-x-lg me-1"></i> Tolak
                  </button>

                  <!-- Modal Catatan Tolak / Perlu Perbaikan -->
                  <div class="modal fade" id="rejectModal<?= $item['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog text-start">
                      <div class="modal-content">
                        <form action="<?= $base ?>/admin/index.php" method="POST">
                          <input type="hidden" name="biz_id" value="<?= $item['id'] ?>">
                          <input type="hidden" name="action" value="reject">
                          <div class="modal-header">
                            <h5 class="modal-title h6 fw-bold">Tolak / Minta Perbaikan Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <p class="small text-muted mb-3">
                              Beri catatan kepada pemilik <strong><?= sanitize($item['name']) ?></strong> mengenai apa yang perlu diperbaiki:
                            </p>
                            <div class="mb-3">
                              <label class="form-label small fw-semibold">Alasan / Catatan Perbaikan <span class="text-danger">*</span></label>
                              <textarea name="moderation_note" class="form-control" rows="3" required placeholder="contoh: Alamat belum mencantumkan RT/RW, atau foto produk kurang jelas..."></textarea>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-danger">Simpan Keputusan Tolak</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Tautan Cepat Admin -->
  <div class="row g-3">
    <div class="col-md-6">
      <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
        <h5 class="fw-bold mb-2"><i class="bi bi-card-checklist text-success me-2"></i>Manajemen Data Usaha</h5>
        <p class="small text-muted mb-3">
          Lihat daftar lengkap seluruh usaha UMKM yang terdaftar di Langkapura, aktifkan atau nonaktifkan usaha, dan cari data dengan filter.
        </p>
        <a href="<?= $base ?>/admin/usaha.php" class="btn btn-outline-custom btn-sm">
          Kelola Seluruh UMKM <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>

    <div class="col-md-6">
      <div class="p-4 bg-white rounded-4 border shadow-sm h-100">
        <h5 class="fw-bold mb-2"><i class="bi bi-tags text-success me-2"></i>Manajemen Kategori UMKM</h5>
        <p class="small text-muted mb-3">
          Tambah bidang usaha baru, ubah nama atau ikon kategori, dan sesuaikan pengelompokan usaha warga Langkapura.
        </p>
        <a href="<?= $base ?>/admin/kategori.php" class="btn btn-outline-custom btn-sm">
          Kelola Kategori <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

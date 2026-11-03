<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$currentUser = getCurrentUser();
$pdo = getDB();

// Menangani perubahan status langsung atau hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $bizId = (int)($_POST['biz_id'] ?? 0);
    $action = $_POST['action'];

    if ($bizId > 0) {
        if ($action === 'set_status') {
            $newStatus = $_POST['status'] ?? 'pending';
            $note = trim($_POST['moderation_note'] ?? '');
            
            $stmt = $pdo->prepare("UPDATE businesses SET status = ?, moderation_note = ?, reviewed_by = ?, reviewed_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$newStatus, $note, $currentUser['id'], $bizId]);
            setFlash('success', 'Status usaha berhasil diubah menjadi: ' . getStatusText($newStatus));
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM businesses WHERE id = ?");
            $stmt->execute([$bizId]);
            setFlash('info', 'Data usaha berhasil dihapus dari direktori.');
        }
        header("Location: " . getBaseUrl() . "/admin/usaha.php");
        exit;
    }
}

// Filter dan pencarian
$q = sanitize($_GET['q'] ?? '');
$statusFilter = sanitize($_GET['status'] ?? '');
$kelurahanFilter = sanitize($_GET['kelurahan'] ?? '');

$sql = "
    SELECT b.*, c.name as category_name, c.icon as category_icon, u.name as owner_name, u.email as owner_email
    FROM businesses b
    JOIN categories c ON b.category_id = c.id
    JOIN users u ON b.owner_id = u.id
    WHERE 1=1
";
$params = [];

if (!empty($q)) {
    $sql .= " AND (b.name LIKE :q OR u.name LIKE :q2 OR b.address LIKE :q3)";
    $params[':q'] = "%$q%";
    $params[':q2'] = "%$q%";
    $params[':q3'] = "%$q%";
}

if (!empty($statusFilter)) {
    $sql .= " AND b.status = :status";
    $params[':status'] = $statusFilter;
}

if (!empty($kelurahanFilter)) {
    $sql .= " AND b.kelurahan = :kelurahan";
    $params[':kelurahan'] = $kelurahanFilter;
}

$sql .= " ORDER BY b.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$businesses = $stmt->fetchAll();

$kelurahanList = getKelurahanList();

$pageTitle = 'Manajemen Seluruh Usaha — Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-1">
        <li class="breadcrumb-item"><a href="<?= $base ?>/admin/index.php" class="text-decoration-none text-muted">Dashboard Admin</a></li>
        <li class="breadcrumb-item active text-success" aria-current="page">Manajemen Usaha</li>
      </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
      <div>
        <h1 class="h3 fw-bold mb-1">Manajemen Seluruh Usaha UMKM</h1>
        <p class="text-muted small mb-0">Kelola status moderasi, verifikasi kelayakan informasi, dan hapus entri data.</p>
      </div>
      <span class="badge bg-light text-dark border px-3 py-2">Total: <?= count($businesses) ?> Usaha</span>
    </div>
  </div>
</div>

<div class="container py-4">
  <!-- Baris Filter -->
  <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="<?= $base ?>/admin/usaha.php" method="GET" class="row g-2 align-items-center">
      <div class="col-md-5 col-12">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Cari nama usaha, pemilik, alamat..." value="<?= sanitize($q) ?>">
        </div>
      </div>

      <div class="col-md-3 col-6">
        <select name="status" class="form-select bg-light">
          <option value="">Semua Status</option>
          <option value="pending" <?= ($statusFilter === 'pending') ? 'selected' : '' ?>>Menunggu Moderasi</option>
          <option value="approved" <?= ($statusFilter === 'approved') ? 'selected' : '' ?>>Disetujui</option>
          <option value="rejected" <?= ($statusFilter === 'rejected') ? 'selected' : '' ?>>Perlu Perbaikan</option>
          <option value="inactive" <?= ($statusFilter === 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
        </select>
      </div>

      <div class="col-md-2 col-6">
        <select name="kelurahan" class="form-select bg-light">
          <option value="">Semua Kelurahan</option>
          <?php foreach ($kelurahanList as $kel): ?>
            <option value="<?= sanitize($kel) ?>" <?= ($kelurahanFilter === $kel) ? 'selected' : '' ?>>Kel. <?= sanitize($kel) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2 col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
        <?php if (!empty($q) || !empty($statusFilter) || !empty($kelurahanFilter)): ?>
          <a href="<?= $base ?>/admin/usaha.php" class="btn btn-outline-secondary" title="Reset Filter"><i class="bi bi-x-lg"></i></a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Tabel Usaha -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <?php if (empty($businesses)): ?>
      <div class="text-center py-5">
        <i class="bi bi-inbox text-muted display-4"></i>
        <h5 class="mt-3 fw-bold">Tidak Ada Data Usaha yang Sesuai</h5>
        <p class="text-muted small">Coba ubah kata kunci pencarian atau reset filter.</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light small text-muted">
            <tr>
              <th class="ps-4">Usaha</th>
              <th>Pemilik</th>
              <th>Kategori</th>
              <th>Kelurahan</th>
              <th>Status</th>
              <th>Catatan Moderasi</th>
              <th class="text-end pe-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($businesses as $biz): ?>
              <tr>
                <td class="ps-4 py-3">
                  <div class="d-flex align-items-center gap-3">
                    <img src="<?= sanitize($biz['image_path']) ?>" alt="" class="rounded-3" style="width: 48px; height: 48px; object-fit: cover;" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=200&auto=format&fit=crop&q=80';">
                    <div>
                      <div class="fw-bold text-dark"><?= sanitize($biz['name']) ?></div>
                      <div class="small text-muted text-truncate" style="max-width: 200px;"><?= sanitize($biz['address']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="small fw-semibold"><?= sanitize($biz['owner_name']) ?></div>
                  <div class="small text-muted"><?= sanitize($biz['owner_email']) ?></div>
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
                  <?= getStatusBadge($biz['status']) ?>
                </td>
                <td>
                  <?php if (!empty($biz['moderation_note'])): ?>
                    <span class="small text-muted text-truncate d-inline-block" style="max-width: 200px;" title="<?= sanitize($biz['moderation_note']) ?>">
                      <?= sanitize($biz['moderation_note']) ?>
                    </span>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-end pe-4 text-nowrap">
                  <a href="<?= $base ?>/admin/usaha_detail.php?id=<?= $biz['id'] ?>" class="btn btn-sm btn-light border" title="Periksa Detail">
                    <i class="bi bi-eye"></i>
                  </a>

                  <!-- Dropdown Ubah Status -->
                  <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-custom dropdown-toggle" data-bs-toggle="dropdown">
                      Status
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                      <li>
                        <form action="<?= $base ?>/admin/usaha.php" method="POST">
                          <input type="hidden" name="biz_id" value="<?= $biz['id'] ?>">
                          <input type="hidden" name="action" value="set_status">
                          <input type="hidden" name="status" value="approved">
                          <input type="hidden" name="moderation_note" value="Disetujui oleh admin">
                          <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-2"></i>Setujui Publik</button>
                        </form>
                      </li>
                      <li>
                        <button type="button" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#modalNote<?= $biz['id'] ?>">
                          <i class="bi bi-exclamation-triangle me-2"></i>Minta Perbaikan
                        </button>
                      </li>
                      <li>
                        <form action="<?= $base ?>/admin/usaha.php" method="POST">
                          <input type="hidden" name="biz_id" value="<?= $biz['id'] ?>">
                          <input type="hidden" name="action" value="set_status">
                          <input type="hidden" name="status" value="inactive">
                          <input type="hidden" name="moderation_note" value="Dinonaktifkan oleh administrator">
                          <button type="submit" class="dropdown-item text-secondary"><i class="bi bi-slash-circle me-2"></i>Nonaktifkan</button>
                        </form>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <form action="<?= $base ?>/admin/usaha.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data usaha ini secara permanen?');">
                          <input type="hidden" name="biz_id" value="<?= $biz['id'] ?>">
                          <input type="hidden" name="action" value="delete">
                          <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>Hapus Usaha</button>
                        </form>
                      </li>
                    </ul>
                  </div>

                  <!-- Modal Input Catatan Perbaikan -->
                  <div class="modal fade" id="modalNote<?= $biz['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog text-start">
                      <div class="modal-content">
                        <form action="<?= $base ?>/admin/usaha.php" method="POST">
                          <input type="hidden" name="biz_id" value="<?= $biz['id'] ?>">
                          <input type="hidden" name="action" value="set_status">
                          <input type="hidden" name="status" value="rejected">
                          <div class="modal-header">
                            <h5 class="modal-title h6 fw-bold">Minta Perbaikan Usaha</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <label class="form-label small fw-semibold">Catatan untuk pemilik:</label>
                            <textarea name="moderation_note" class="form-control" rows="3" required placeholder="Jelaskan data apa yang perlu diperbaiki..."><?= sanitize($biz['moderation_note']) ?></textarea>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-warning">Simpan Catatan</button>
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
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

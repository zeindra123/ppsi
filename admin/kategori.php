<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();
$currentUser = getCurrentUser();
$pdo = getDB();

$error = '';

// Proses Tambah Kategori Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $icon = trim($_POST['icon'] ?? 'bi-tag');
        $desc = trim($_POST['description'] ?? '');

        if (empty($name)) {
            $error = 'Nama kategori wajib diisi.';
        } else {
            $slug = slugify($name);
            // Cek keunikan slug
            $stmtSlug = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $stmtSlug->execute([$slug]);
            if ($stmtSlug->fetch()) {
                $slug .= '-' . time();
            }

            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description, status) VALUES (?, ?, ?, ?, 'active')");
            $stmt->execute([$name, $slug, $icon, $desc]);
            setFlash('success', 'Kategori baru "' . $name . '" berhasil ditambahkan!');
            header("Location: " . getBaseUrl() . "/admin/kategori.php");
            exit;
        }
    } elseif ($action === 'toggle_status') {
        $catId = (int)($_POST['cat_id'] ?? 0);
        $currentStatus = $_POST['current_status'] ?? 'active';
        $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

        $stmt = $pdo->prepare("UPDATE categories SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $catId]);
        setFlash('info', 'Status kategori berhasil diubah.');
        header("Location: " . getBaseUrl() . "/admin/kategori.php");
        exit;
    } elseif ($action === 'delete') {
        $catId = (int)($_POST['cat_id'] ?? 0);
        // Cek apakah ada usaha yang terikat
        $count = $pdo->prepare("SELECT COUNT(*) FROM businesses WHERE category_id = ?");
        $count->execute([$catId]);
        if ($count->fetchColumn() > 0) {
            setFlash('danger', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh data usaha aktif.');
        } else {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$catId]);
            setFlash('success', 'Kategori berhasil dihapus.');
        }
        header("Location: " . getBaseUrl() . "/admin/kategori.php");
        exit;
    }
}

// Ambil daftar seluruh kategori beserta hitungan usaha
$stmtCats = $pdo->query("
    SELECT c.*, COUNT(b.id) as total_businesses
    FROM categories c
    LEFT JOIN businesses b ON c.id = b.category_id
    GROUP BY c.id
    ORDER BY c.name ASC
");
$categories = $stmtCats->fetchAll();

$pageTitle = 'Manajemen Kategori UMKM — Admin';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-1">
        <li class="breadcrumb-item"><a href="<?= $base ?>/admin/index.php" class="text-decoration-none text-muted">Dashboard Admin</a></li>
        <li class="breadcrumb-item active text-success" aria-current="page">Manajemen Kategori</li>
      </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h1 class="h3 fw-bold mb-1">Manajemen Kategori UMKM</h1>
        <p class="text-muted small mb-0">Kelola kelompok bidang usaha untuk memudahkan pencarian oleh warga.</p>
      </div>
      <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
      </button>
    </div>
  </div>
</div>

<div class="container py-4">
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger small border-0 py-2 d-flex align-items-center mb-4">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div><?= sanitize($error) ?></div>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Tabel Kategori -->
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-muted">
              <tr>
                <th class="ps-4">Kategori & Ikon</th>
                <th>Slug URL</th>
                <th>Jumlah Usaha</th>
                <th>Status</th>
                <th class="text-end pe-4">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $cat): ?>
                <tr>
                  <td class="ps-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                      <div class="category-icon-box m-0" style="width: 38px; height: 38px; font-size: 1.1rem; border-radius: 10px;">
                        <i class="bi <?= sanitize($cat['icon']) ?>"></i>
                      </div>
                      <div>
                        <div class="fw-bold text-dark"><?= sanitize($cat['name']) ?></div>
                        <div class="small text-muted text-truncate" style="max-width: 260px;"><?= sanitize($cat['description']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <code><?= sanitize($cat['slug']) ?></code>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border"><?= $cat['total_businesses'] ?> Usaha</span>
                  </td>
                  <td>
                    <?php if ($cat['status'] === 'active'): ?>
                      <span class="badge bg-success-soft text-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                    <?php else: ?>
                      <span class="badge bg-secondary-soft text-secondary"><i class="bi bi-slash-circle me-1"></i>Nonaktif</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end pe-4 text-nowrap">
                    <!-- Toggle Status -->
                    <form action="<?= $base ?>/admin/kategori.php" method="POST" class="d-inline">
                      <input type="hidden" name="action" value="toggle_status">
                      <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                      <input type="hidden" name="current_status" value="<?= $cat['status'] ?>">
                      <button type="submit" class="btn btn-sm btn-light border" title="<?= ($cat['status'] === 'active') ? 'Nonaktifkan' : 'Aktifkan' ?>">
                        <i class="bi bi-power <?= ($cat['status'] === 'active') ? 'text-success' : 'text-danger' ?>"></i>
                      </button>
                    </form>

                    <!-- Hapus Kategori jika 0 usaha -->
                    <?php if ($cat['total_businesses'] == 0): ?>
                      <form action="<?= $base ?>/admin/kategori.php" method="POST" class="d-inline" onsubmit="return confirm('Hapus kategori ini?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Kategori">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Informasi dan Tambah Cepat -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <h5 class="fw-bold mb-3 border-bottom pb-2">Tambah Kategori Baru</h5>
        <form action="<?= $base ?>/admin/kategori.php" method="POST">
          <input type="hidden" name="action" value="create">

          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="contoh: Percetakan & Fotokopi" required>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Class Ikon Bootstrap Icons</label>
            <input type="text" name="icon" class="form-control" placeholder="bi-printer" value="bi-tag">
            <div class="form-text" style="font-size: 0.75rem;">Gunakan class ikon <a href="https://icons.getbootstrap.com" target="_blank" class="text-success">Bootstrap Icons</a>, misal: <code>bi-printer</code>, <code>bi-cup-hot</code>, <code>bi-cart</code>.</div>
          </div>

          <div class="mb-4">
            <label class="form-label small fw-semibold">Deskripsi Singkat</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan jenis usaha yang masuk kategori ini..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary-custom w-100 py-2">
            <i class="bi bi-plus-circle me-1"></i> Simpan Kategori
          </button>
        </form>
      </div>

      <div class="p-3 bg-light rounded-3 border small text-muted">
        <i class="bi bi-info-circle me-1 text-primary"></i>
        Kategori yang dinonaktifkan tidak akan muncul pada pilihan formulir pengajuan baru, namun data usaha lama yang sudah terkait akan tetap tersimpan secara konsisten.
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

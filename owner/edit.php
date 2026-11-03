<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$currentUser = getCurrentUser();
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: " . getBaseUrl() . "/owner/index.php");
    exit;
}

// Cek usaha dan pastikan milik user yang sedang login (atau admin)
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->execute([$id]);
$biz = $stmt->fetch();

if (!$biz) {
    setFlash('danger', 'Data usaha tidak ditemukan.');
    header("Location: " . getBaseUrl() . "/owner/index.php");
    exit;
}

if (!isAdmin() && $biz['owner_id'] != $currentUser['id']) {
    setFlash('danger', 'Akses ditolak. Anda tidak memiliki izin untuk mengedit usaha ini.');
    header("Location: " . getBaseUrl() . "/owner/index.php");
    exit;
}

$error = '';
$stmtCats = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");
$categories = $stmtCats->fetchAll();
$kelurahanList = getKelurahanList();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $kelurahan = trim($_POST['kelurahan'] ?? 'Langkapura');
    $address = trim($_POST['address'] ?? '');
    $whatsapp_number = trim($_POST['whatsapp_number'] ?? '');
    $operating_hours = trim($_POST['operating_hours'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $products_list = trim($_POST['products_list'] ?? '');
    $image_path = trim($_POST['image_path'] ?? '');
    $maps_url = trim($_POST['maps_url'] ?? '');

    if (empty($name) || empty($category_id) || empty($address) || empty($whatsapp_number) || empty($description)) {
        $error = 'Nama usaha, kategori, kelurahan, alamat, kontak WhatsApp, dan deskripsi wajib diisi.';
    } else {
        if (empty($image_path)) {
            $image_path = $biz['image_path'];
        }

        // Jika bukan admin yang mengedit, status kembali ke 'pending' untuk moderasi ulang
        $newStatus = isAdmin() ? $biz['status'] : 'pending';

        $stmtUpdate = $pdo->prepare("
            UPDATE businesses SET
                name = :name,
                category_id = :category_id,
                kelurahan = :kelurahan,
                address = :address,
                whatsapp_number = :whatsapp_number,
                operating_hours = :operating_hours,
                description = :description,
                products_list = :products_list,
                image_path = :image_path,
                maps_url = :maps_url,
                status = :status,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $stmtUpdate->execute([
            ':name' => $name,
            ':category_id' => $category_id,
            ':kelurahan' => $kelurahan,
            ':address' => $address,
            ':whatsapp_number' => $whatsapp_number,
            ':operating_hours' => $operating_hours,
            ':description' => $description,
            ':products_list' => $products_list,
            ':image_path' => $image_path,
            ':maps_url' => $maps_url,
            ':status' => $newStatus,
            ':id' => $id
        ]);

        setFlash('success', 'Perubahan informasi usaha berhasil disimpan' . (!isAdmin() ? ' dan diajukan untuk peninjauan moderasi.' : '.'));
        header("Location: " . getBaseUrl() . "/owner/index.php");
        exit;
    }
}

$pageTitle = 'Edit Usaha — ' . $biz['name'];
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-1">
        <li class="breadcrumb-item"><a href="<?= $base ?>/owner/index.php" class="text-decoration-none text-muted">Dashboard Usaha</a></li>
        <li class="breadcrumb-item active text-success" aria-current="page">Edit Usaha</li>
      </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
      <h1 class="h3 fw-bold mb-0">Edit Profil Usaha</h1>
      <?= getStatusBadge($biz['status']) ?>
    </div>
  </div>
</div>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">

        <?php if (!empty($biz['moderation_note'])): ?>
          <div class="alert alert-warning small border-0 py-3 mb-4 rounded-3">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan dari Admin:</h6>
            <div><?= sanitize($biz['moderation_note']) ?></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger small border-0 py-2 d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= sanitize($error) ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= $base ?>/owner/edit.php?id=<?= $biz['id'] ?>" method="POST">
          <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">Informasi Dasar Usaha</h5>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Usaha UMKM <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?= sanitize($_POST['name'] ?? $biz['name']) ?>">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Kategori Usaha <span class="text-danger">*</span></label>
              <select name="category_id" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= ((int)($_POST['category_id'] ?? $biz['category_id']) == $cat['id']) ? 'selected' : '' ?>>
                    <?= sanitize($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Kelurahan <span class="text-danger">*</span></label>
              <select name="kelurahan" class="form-select" required>
                <?php foreach ($kelurahanList as $kel): ?>
                  <option value="<?= sanitize($kel) ?>" <?= (($_POST['kelurahan'] ?? $biz['kelurahan']) === $kel) ? 'selected' : '' ?>>
                    Kel. <?= sanitize($kel) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="address" class="form-control" rows="2" required><?= sanitize($_POST['address'] ?? $biz['address']) ?></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Nomor WhatsApp Usaha <span class="text-danger">*</span></label>
              <input type="text" name="whatsapp_number" class="form-control" required value="<?= sanitize($_POST['whatsapp_number'] ?? $biz['whatsapp_number']) ?>">
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Jam & Hari Operasional</label>
              <input type="text" name="operating_hours" class="form-control" value="<?= sanitize($_POST['operating_hours'] ?? $biz['operating_hours']) ?>">
            </div>
          </div>

          <h5 class="fw-bold mb-3 text-dark border-bottom pb-2 mt-4">Profil & Produk</h5>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Deskripsi Lengkap Usaha <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="4" required><?= sanitize($_POST['description'] ?? $biz['description']) ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Daftar Produk / Jasa & Estimasi Harga</label>
            <textarea name="products_list" class="form-control" rows="4"><?= sanitize($_POST['products_list'] ?? $biz['products_list']) ?></textarea>
            <div class="form-text" style="font-size: 0.75rem;">1 produk per baris, contoh: Nama Produk - Rp 15.000</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">URL Foto Usaha / Produk</label>
            <input type="url" name="image_path" class="form-control" value="<?= sanitize($_POST['image_path'] ?? $biz['image_path']) ?>">
          </div>

          <div class="mb-4">
            <label class="form-label small fw-semibold">Tautan Google Maps</label>
            <input type="url" name="maps_url" class="form-control" value="<?= sanitize($_POST['maps_url'] ?? $biz['maps_url']) ?>">
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="<?= $base ?>/owner/index.php" class="btn btn-outline-secondary">
              Kembali
            </a>
            <button type="submit" class="btn btn-primary-custom px-4">
              <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

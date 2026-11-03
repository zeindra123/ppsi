<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
$currentUser = getCurrentUser();
$pdo = getDB();

$error = '';

// Ambil daftar kategori aktif
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

    // Validasi
    if (empty($name) || empty($category_id) || empty($address) || empty($whatsapp_number) || empty($description)) {
        $error = 'Nama usaha, kategori, kelurahan, alamat, kontak WhatsApp, dan deskripsi wajib diisi.';
    } else {
        // Default cover image jika kosong
        if (empty($image_path)) {
            $image_path = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80';
        }

        $slug = slugify($name);
        // Pastikan slug unik
        $stmtSlug = $pdo->prepare("SELECT id FROM businesses WHERE slug = ?");
        $stmtSlug->execute([$slug]);
        if ($stmtSlug->fetch()) {
            $slug .= '-' . time();
        }

        $stmtInsert = $pdo->prepare("
            INSERT INTO businesses (
                owner_id, category_id, name, slug, description, whatsapp_number,
                kelurahan, address, operating_hours, image_path, maps_url, products_list,
                status, moderation_note, created_at, updated_at
            ) VALUES (
                :owner_id, :category_id, :name, :slug, :description, :whatsapp_number,
                :kelurahan, :address, :operating_hours, :image_path, :maps_url, :products_list,
                'pending', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            )
        ");

        $stmtInsert->execute([
            ':owner_id' => $currentUser['id'],
            ':category_id' => $category_id,
            ':name' => $name,
            ':slug' => $slug,
            ':description' => $description,
            ':whatsapp_number' => $whatsapp_number,
            ':kelurahan' => $kelurahan,
            ':address' => $address,
            ':operating_hours' => $operating_hours,
            ':image_path' => $image_path,
            ':maps_url' => $maps_url,
            ':products_list' => $products_list
        ]);

        setFlash('success', 'Usaha berhasil didaftarkan! Status saat ini: Menunggu Moderasi oleh Admin.');
        header("Location: " . getBaseUrl() . "/owner/index.php");
        exit;
    }
}

$pageTitle = 'Ajukan Usaha Baru';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-1">
        <li class="breadcrumb-item"><a href="<?= $base ?>/owner/index.php" class="text-decoration-none text-muted">Dashboard Usaha</a></li>
        <li class="breadcrumb-item active text-success" aria-current="page">Ajukan Usaha Baru</li>
      </ol>
    </nav>
    <h1 class="h3 fw-bold mb-0">Formulir Pengajuan Usaha UMKM</h1>
  </div>
</div>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger small border-0 py-2 d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= sanitize($error) ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= $base ?>/owner/tambah.php" method="POST">
          <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">Informasi Dasar Usaha</h5>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Usaha UMKM <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="contoh: Keripik Pisang Berkah Langkapura" required value="<?= isset($_POST['name']) ? sanitize($_POST['name']) : '' ?>">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Kategori Usaha <span class="text-danger">*</span></label>
              <select name="category_id" class="form-select" required>
                <option value="">Pilih Kategori</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= sanitize($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Kelurahan <span class="text-danger">*</span></label>
              <select name="kelurahan" class="form-select" required>
                <?php foreach ($kelurahanList as $kel): ?>
                  <option value="<?= sanitize($kel) ?>" <?= (isset($_POST['kelurahan']) && $_POST['kelurahan'] == $kel) ? 'selected' : '' ?>>
                    Kel. <?= sanitize($kel) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="address" class="form-control" rows="2" placeholder="Nama jalan, nomor rumah/ruko, RT/RW, dan patokan lokasi..." required><?= isset($_POST['address']) ? sanitize($_POST['address']) : '' ?></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Nomor WhatsApp Usaha <span class="text-danger">*</span></label>
              <input type="text" name="whatsapp_number" class="form-control" placeholder="contoh: 082188997711" required value="<?= isset($_POST['whatsapp_number']) ? sanitize($_POST['whatsapp_number']) : '' ?>">
              <div class="form-text" style="font-size: 0.75rem;">Digunakan pengunjung untuk langsung menghubungi Anda.</div>
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Jam & Hari Operasional</label>
              <input type="text" name="operating_hours" class="form-control" placeholder="contoh: Senin - Sabtu: 08.00 - 18.00 WIB" value="<?= isset($_POST['operating_hours']) ? sanitize($_POST['operating_hours']) : '' ?>">
            </div>
          </div>

          <h5 class="fw-bold mb-3 text-dark border-bottom pb-2 mt-4">Profil & Produk</h5>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Deskripsi Lengkap Usaha <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control" rows="4" placeholder="Ceritakan keunggulan usaha Anda, bahan yang digunakan, layanan khusus, dll..." required><?= isset($_POST['description']) ? sanitize($_POST['description']) : '' ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Daftar Produk / Jasa & Estimasi Harga</label>
            <textarea name="products_list" class="form-control" rows="4" placeholder="Tuliskan 1 produk per baris, contoh:&#10;Keripik Pisang Coklat (250gr) - Rp 18.000&#10;Keripik Pisang Keju (250gr) - Rp 18.000"><?= isset($_POST['products_list']) ? sanitize($_POST['products_list']) : '' ?></textarea>
            <div class="form-text" style="font-size: 0.75rem;">Format: Nama Produk / Jasa - Harga (Opsional). Pisahkan dengan baris baru (Enter).</div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">URL Foto Usaha / Produk</label>
            <input type="url" name="image_path" class="form-control" placeholder="https://images.unsplash.com/..." value="<?= isset($_POST['image_path']) ? sanitize($_POST['image_path']) : '' ?>">
            <div class="form-text" style="font-size: 0.75rem;">Masukkan URL gambar produk Anda. Jika dikosongkan, gambar contoh yang rapi akan digunakan otomatis.</div>
          </div>

          <div class="mb-4">
            <label class="form-label small fw-semibold">Tautan Google Maps (Opsional)</label>
            <input type="url" name="maps_url" class="form-control" placeholder="https://maps.google.com/?q=..." value="<?= isset($_POST['maps_url']) ? sanitize($_POST['maps_url']) : '' ?>">
          </div>

          <div class="p-3 bg-light rounded-3 border small mb-4">
            <i class="bi bi-info-circle-fill text-primary me-1"></i>
            Setelah dikirimkan, usaha Anda akan masuk ke status <strong>Menunggu Moderasi</strong> dan akan diperiksa oleh admin sebelum tampil di direktori umum.
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <a href="<?= $base ?>/owner/index.php" class="btn btn-outline-secondary">
              Batal
            </a>
            <button type="submit" class="btn btn-primary-custom px-4">
              <i class="bi bi-send-fill me-1"></i> Kirim Pengajuan Usaha
            </button>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

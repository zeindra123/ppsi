<?php
$pageTitle = 'Jelajahi UMKM Langkapura';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// Input filter & pencarian
$q = sanitize($_GET['q'] ?? '');
$kategoriSlug = sanitize($_GET['kategori'] ?? '');
$kelurahan = sanitize($_GET['kelurahan'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'terbaru');

// Ambil daftar kategori aktif untuk dropdown
$stmtCats = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC");
$categories = $stmtCats->fetchAll();
$kelurahanList = getKelurahanList();

// Bangun query dinamis dengan prepared statement
$sql = "
    SELECT b.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
    FROM businesses b
    JOIN categories c ON b.category_id = c.id
    WHERE b.status = 'approved'
";
$params = [];

if (!empty($q)) {
    $sql .= " AND (b.name LIKE :q OR b.description LIKE :q2 OR b.products_list LIKE :q3)";
    $params[':q'] = "%$q%";
    $params[':q2'] = "%$q%";
    $params[':q3'] = "%$q%";
}

if (!empty($kategoriSlug)) {
    $sql .= " AND c.slug = :cat_slug";
    $params[':cat_slug'] = $kategoriSlug;
}

if (!empty($kelurahan)) {
    $sql .= " AND b.kelurahan = :kelurahan";
    $params[':kelurahan'] = $kelurahan;
}

// Pengurutan
switch ($sort) {
    case 'nama-asc':
        $sql .= " ORDER BY b.name ASC";
        break;
    case 'nama-desc':
        $sql .= " ORDER BY b.name DESC";
        break;
    case 'terbaru':
    default:
        $sql .= " ORDER BY b.id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$businesses = $stmt->fetchAll();
$totalResults = count($businesses);
?>

<div class="py-4 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small mb-2">
        <li class="breadcrumb-item"><a href="<?= $base ?>/index.php" class="text-decoration-none text-muted">Beranda</a></li>
        <li class="breadcrumb-item active text-success" aria-current="page">Katalog Usaha</li>
      </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
      <div>
        <h1 class="h3 fw-bold mb-1">Jelajahi Usaha di Langkapura</h1>
        <p class="text-muted small mb-0">Temukan ragam UMKM pilihan warga di seluruh penjuru Kecamatan Langkapura</p>
      </div>
      <div class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-semibold">
        <i class="bi bi-shop me-1"></i> <?= $totalResults ?> Usaha Ditemukan
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <!-- Baris Filter & Pencarian -->
  <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
    <form action="<?= $base ?>/katalog.php" method="GET" class="row g-2 align-items-center">
      <!-- Input Search -->
      <div class="col-lg-4 col-md-6 col-12">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0 text-muted">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Cari nama usaha / produk..." value="<?= sanitize($q) ?>">
        </div>
      </div>

      <!-- Filter Kategori -->
      <div class="col-lg-3 col-md-6 col-12">
        <select name="kategori" class="form-select bg-light">
          <option value="">Semua Kategori</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= sanitize($cat['slug']) ?>" <?= ($kategoriSlug === $cat['slug']) ? 'selected' : '' ?>>
              <?= sanitize($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Filter Kelurahan -->
      <div class="col-lg-3 col-md-6 col-12">
        <select name="kelurahan" class="form-select bg-light">
          <option value="">Semua Kelurahan</option>
          <?php foreach ($kelurahanList as $kel): ?>
            <option value="<?= sanitize($kel) ?>" <?= ($kelurahan === $kel) ? 'selected' : '' ?>>
              Kel. <?= sanitize($kel) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Tombol Aksi Filter & Reset -->
      <div class="col-lg-2 col-md-6 col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary-custom w-100">
          <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>
        <?php if (!empty($q) || !empty($kategoriSlug) || !empty($kelurahan) || $sort !== 'terbaru'): ?>
          <a href="<?= $base ?>/katalog.php" class="btn btn-outline-secondary" title="Reset Filter">
            <i class="bi bi-x-lg"></i>
          </a>
        <?php endif; ?>
      </div>
    </form>

    <!-- Urutan & Status Filter Aktif -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3 pt-3 border-top small">
      <div class="d-flex flex-wrap gap-1 align-items-center">
        <?php if (!empty($q) || !empty($kategoriSlug) || !empty($kelurahan)): ?>
          <span class="text-muted me-1">Filter aktif:</span>
          <?php if (!empty($q)): ?>
            <span class="badge bg-light text-dark border">Kata kunci: "<?= sanitize($q) ?>"</span>
          <?php endif; ?>
          <?php if (!empty($kategoriSlug)): ?>
            <span class="badge bg-light text-dark border">Kategori: <?= sanitize($kategoriSlug) ?></span>
          <?php endif; ?>
          <?php if (!empty($kelurahan)): ?>
            <span class="badge bg-light text-dark border">Kelurahan: <?= sanitize($kelurahan) ?></span>
          <?php endif; ?>
          <a href="<?= $base ?>/katalog.php" class="text-danger ms-2 text-decoration-none"><i class="bi bi-trash"></i> Hapus Semua</a>
        <?php else: ?>
          <span class="text-muted">Menampilkan seluruh UMKM aktif di Langkapura</span>
        <?php endif; ?>
      </div>

      <!-- Opsi Sorting -->
      <form action="<?= $base ?>/katalog.php" method="GET" class="d-flex align-items-center gap-2">
        <?php if (!empty($q)): ?><input type="hidden" name="q" value="<?= sanitize($q) ?>"><?php endif; ?>
        <?php if (!empty($kategoriSlug)): ?><input type="hidden" name="kategori" value="<?= sanitize($kategoriSlug) ?>"><?php endif; ?>
        <?php if (!empty($kelurahan)): ?><input type="hidden" name="kelurahan" value="<?= sanitize($kelurahan) ?>"><?php endif; ?>
        <label for="sortSelect" class="text-muted text-nowrap">Urutkan:</label>
        <select id="sortSelect" name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
          <option value="terbaru" <?= ($sort === 'terbaru') ? 'selected' : '' ?>>Paling Baru</option>
          <option value="nama-asc" <?= ($sort === 'nama-asc') ? 'selected' : '' ?>>Nama (A - Z)</option>
          <option value="nama-desc" <?= ($sort === 'nama-desc') ? 'selected' : '' ?>>Nama (Z - A)</option>
        </select>
      </form>
    </div>
  </div>

  <!-- Grid Hasil Usaha -->
  <?php if (empty($businesses)): ?>
    <!-- Empty State Sesuai desaind.md -->
    <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3 bg-white">
      <div class="mb-3">
        <i class="bi bi-search text-muted display-4"></i>
      </div>
      <h4 class="h5 fw-bold mb-2">Tidak Ada Usaha yang Ditemukan</h4>
      <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
        Kami tidak dapat menemukan usaha dengan kata kunci atau filter yang Anda pilih. Silakan coba kata kunci lain atau hapus filter.
      </p>
      <div>
        <a href="<?= $base ?>/katalog.php" class="btn btn-outline-custom btn-sm">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Semua Filter
        </a>
      </div>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($businesses as $biz): ?>
        <div class="col-lg-4 col-md-6">
          <div class="business-card">
            <div class="business-card-img-wrapper">
              <img src="<?= sanitize($biz['image_path']) ?>" alt="<?= sanitize($biz['name']) ?>" class="business-card-img" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80';">
              <div class="position-absolute top-0 start-0 m-3">
                <span class="badge-kelurahan">
                  <i class="bi bi-geo-alt"></i> <?= sanitize($biz['kelurahan']) ?>
                </span>
              </div>
            </div>

            <div class="business-card-body">
              <div class="small text-success fw-semibold mb-1">
                <i class="bi <?= sanitize($biz['category_icon']) ?> me-1"></i> <?= sanitize($biz['category_name']) ?>
              </div>
              <h3 class="business-card-title"><?= sanitize($biz['name']) ?></h3>
              <p class="business-card-desc"><?= sanitize($biz['description']) ?></p>

              <div class="small text-muted mb-2">
                <i class="bi bi-clock me-1 text-secondary"></i> <?= sanitize($biz['operating_hours'] ?: 'Jam operasional fleksibel') ?>
              </div>

              <div class="small text-muted mb-3 text-truncate">
                <i class="bi bi-pin-map me-1 text-secondary"></i> <?= sanitize($biz['address']) ?>
              </div>

              <div class="business-card-footer">
                <a href="<?= $base ?>/detail.php?id=<?= $biz['id'] ?>" class="btn btn-outline-custom btn-sm">
                  Lihat Detail
                </a>
                <?php if (!empty($biz['whatsapp_number'])): ?>
                  <a href="<?= buildWhatsAppUrl($biz['whatsapp_number'], $biz['name']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-wa btn-sm py-1 px-3">
                    <i class="bi bi-whatsapp"></i> Hubungi
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

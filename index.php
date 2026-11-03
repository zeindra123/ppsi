<?php
$pageTitle = 'Temukan Usaha Lokal di Sekitarmu';
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();

// Ambil kategori aktif beserta jumlah usaha yang disetujui
$stmtCats = $pdo->query("
    SELECT c.*, COUNT(b.id) as total_biz 
    FROM categories c 
    LEFT JOIN businesses b ON c.id = b.category_id AND b.status = 'approved'
    WHERE c.status = 'active'
    GROUP BY c.id
    ORDER BY total_biz DESC, c.name ASC
");
$categories = $stmtCats->fetchAll();

// Ambil usaha pilihan yang berstatus 'approved'
$stmtBiz = $pdo->query("
    SELECT b.*, c.name as category_name, c.icon as category_icon
    FROM businesses b
    JOIN categories c ON b.category_id = c.id
    WHERE b.status = 'approved'
    ORDER BY b.id DESC
    LIMIT 6
");
$featuredBusinesses = $stmtBiz->fetchAll();
$kelurahanList = getKelurahanList();
?>

<!-- Hero Section Sesuai desaind.md -->
<section class="hero-section">
  <div class="container text-center">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill fw-semibold mb-3">
          <i class="bi bi-geo-alt-fill me-1"></i> Direktori Resmi UMKM Kecamatan Langkapura
        </span>
        <h1 class="hero-title mb-3">Temukan Usaha Lokal di Sekitarmu</h1>
        <p class="hero-subtitle mb-4">
          Cari kuliner lezat, toko kelontong, dan berbagai layanan jasa terpercaya di Langkapura, Bandar Lampung. Hubungi pedagang secara langsung tanpa biaya perantara.
        </p>

        <!-- Search Bar Sesuai desaind.md -->
        <div class="search-card mx-auto text-start mb-3" style="max-width: 820px;">
          <form action="<?= $base ?>/katalog.php" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6 col-12">
              <div class="input-group">
                <span class="input-group-text bg-transparent border-0 text-muted ps-2">
                  <i class="bi bi-search fs-5"></i>
                </span>
                <input type="text" name="q" class="form-control search-input" placeholder="Cari nama usaha, keripik, servis motor..." aria-label="Kata kunci pencarian">
              </div>
            </div>
            <div class="col-md-4 col-sm-7 col-12">
              <div class="input-group">
                <span class="input-group-text bg-transparent border-0 text-muted ps-2">
                  <i class="bi bi-geo-alt fs-5"></i>
                </span>
                <select name="kelurahan" class="form-select border-0 search-input" aria-label="Pilih Kelurahan">
                  <option value="">Semua Kelurahan</option>
                  <?php foreach ($kelurahanList as $kel): ?>
                    <option value="<?= sanitize($kel) ?>"><?= sanitize($kel) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-2 col-sm-5 col-12">
              <button type="submit" class="btn btn-primary-custom w-100 py-2">
                Cari Usaha
              </button>
            </div>
          </form>
        </div>

        <!-- Kata Kunci Populer -->
        <div class="d-flex align-items-center justify-content-center flex-wrap gap-1 mt-3">
          <span class="text-muted small me-2"><i class="bi bi-fire text-danger me-1"></i>Populer:</span>
          <a href="<?= $base ?>/katalog.php?q=Keripik+Pisang" class="keyword-badge">Keripik Pisang</a>
          <a href="<?= $base ?>/katalog.php?q=Kopi+Robusta" class="keyword-badge">Kopi Robusta</a>
          <a href="<?= $base ?>/katalog.php?q=Bengkel+Motor" class="keyword-badge">Bengkel Motor</a>
          <a href="<?= $base ?>/katalog.php?q=Sembako" class="keyword-badge">Toko Sembako</a>
          <a href="<?= $base ?>/katalog.php?q=Jahit" class="keyword-badge">Jasa Jahit</a>
          <a href="<?= $base ?>/katalog.php?q=Tanaman+Hias" class="keyword-badge">Tanaman Hias</a>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- Grid Kategori Usaha -->
<section id="kategori" class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <h2 class="h3 fw-bold mb-1">Kategori Usaha</h2>
        <p class="text-muted mb-0 small">Telusuri UMKM berdasarkan bidang usaha di Langkapura</p>
      </div>
      <a href="<?= $base ?>/katalog.php" class="btn btn-sm btn-outline-custom d-none d-md-inline-block">
        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="row g-3">
      <?php foreach ($categories as $cat): ?>
        <div class="col-lg-2 col-md-4 col-6">
          <a href="<?= $base ?>/katalog.php?kategori=<?= urlencode($cat['slug']) ?>" class="category-card">
            <div class="category-icon-box">
              <i class="bi <?= sanitize($cat['icon']) ?>"></i>
            </div>
            <div class="category-title"><?= sanitize($cat['name']) ?></div>
            <div class="category-count"><?= $cat['total_biz'] ?> Usaha</div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Usaha Pilihan / Unggulan -->
<section class="py-5 bg-white border-top border-bottom">
  <div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill small fw-semibold mb-2">Terverifikasi</span>
        <h2 class="h3 fw-bold mb-1">Pilihan UMKM Langkapura</h2>
        <p class="text-muted mb-0 small">Profil usaha lokal yang telah disetujui dan aktif melayani warga</p>
      </div>
      <a href="<?= $base ?>/katalog.php" class="btn btn-outline-custom btn-sm">
        Jelajahi Semua Usaha <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>

    <?php if (empty($featuredBusinesses)): ?>
      <div class="text-center py-5">
        <i class="bi bi-inbox text-muted display-4"></i>
        <h5 class="mt-3 text-muted">Belum ada usaha yang dipublikasikan</h5>
        <p class="small text-muted">Jadilah yang pertama mendaftarkan usaha Anda di Kecamatan Langkapura!</p>
        <a href="<?= $base ?>/register.php" class="btn btn-primary-custom btn-sm mt-2">Daftarkan Usaha Sekarang</a>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($featuredBusinesses as $biz): ?>
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
                <div class="small text-success fw-semibold">
                  <i class="bi <?= sanitize($biz['category_icon']) ?> me-1"></i> <?= sanitize($biz['category_name']) ?>
                </div>
                <h3 class="business-card-title"><?= sanitize($biz['name']) ?></h3>
                <p class="business-card-desc"><?= sanitize($biz['description']) ?></p>

                <div class="small text-muted mb-3">
                  <i class="bi bi-clock me-1 text-secondary"></i> <?= sanitize($biz['operating_hours'] ?: 'Jam operasional fleksibel') ?>
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
</section>

<!-- Cara Kerja LangkapuraLokal Sesuai desaind.md -->
<section id="cara-kerja" class="py-5">
  <div class="container text-center">
    <div class="mb-5">
      <span class="badge bg-success-soft text-success px-3 py-1 rounded-pill fw-semibold mb-2">Mudah & Cepat</span>
      <h2 class="h3 fw-bold">Cara Kerja LangkapuraLokal</h2>
      <p class="text-muted small">Tiga langkah mudah terhubung dengan UMKM tetangga di sekitar Langkapura</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 bg-white rounded-4 border h-100 shadow-sm text-start">
          <div class="step-number">1</div>
          <h4 class="h5 fw-bold mb-2">Cari Usaha</h4>
          <p class="text-muted small mb-0">
            Temukan produk olahan, toko kelontong, atau jasa perbaikan yang kamu butuhkan berdasarkan nama, kata kunci, atau kelurahan.
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 bg-white rounded-4 border h-100 shadow-sm text-start">
          <div class="step-number">2</div>
          <h4 class="h5 fw-bold mb-2">Lihat Detail & Jam Buka</h4>
          <p class="text-muted small mb-0">
            Periksa informasi alamat lengkap, jam operasional, foto profil usaha, serta daftar produk atau layanan yang ditawarkan.
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 bg-white rounded-4 border h-100 shadow-sm text-start">
          <div class="step-number">3</div>
          <h4 class="h5 fw-bold mb-2">Hubungi Langsung via WA</h4>
          <p class="text-muted small mb-0">
            Klik tombol WhatsApp untuk langsung terhubung dengan pemilik usaha. Tanya stok, pesan, atau atur janji secara privat dan aman.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Daftarkan Usaha Pemilik UMKM -->
<section class="py-5 bg-white border-top">
  <div class="container">
    <div class="p-5 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #176B45 0%, #115335 100%); color: #FFFFFF;">
      <div class="row align-items-center">
        <div class="col-lg-8 mb-4 mb-lg-0">
          <h2 class="h2 fw-bold text-white mb-2">Punya Usaha di Wilayah Langkapura?</h2>
          <p class="text-light-50 mb-0 fs-6" style="color: #E2E8F0;">
            Daftarkan tokomu ke direktori digital LangkapuraLokal sekarang. Gratis, tanpa biaya admin atau komisi penjualan, dan langsung tampil untuk warga sekitar setelah dimoderasi.
          </p>
        </div>
        <div class="col-lg-4 text-lg-end">
          <a href="<?= $base ?>/register.php" class="btn btn-light fw-bold px-4 py-3 text-success shadow-sm rounded-3">
            <i class="bi bi-shop me-2"></i> Daftarkan Usaha Saya
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

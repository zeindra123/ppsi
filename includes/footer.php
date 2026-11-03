<?php
$base = getBaseUrl();
?>
  <!-- Footer Sesuai desaind.md -->
  <footer class="footer-custom mt-auto">
    <div class="container">
      <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
          <h5 class="text-white mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-shop-window text-success fs-4"></i>
            <span>Langkapura<span style="color: #2E8B57;">Lokal</span></span>
          </h5>
          <p class="small text-light-50">
            Platform direktori digital yang menghubungkan warga dengan pelaku UMKM di seluruh wilayah Kecamatan Langkapura, Kota Bandar Lampung.
          </p>
          <div class="small text-muted">
            <i class="bi bi-geo-alt-fill text-success me-1"></i> Langkapura, Bandar Lampung, Lampung 35153
          </div>
        </div>

        <div class="col-lg-2 col-md-6 col-6">
          <h6 class="text-white mb-3">Navigasi</h6>
          <ul class="list-unstyled small">
            <li class="mb-2"><a href="<?= $base ?>/index.php">Beranda</a></li>
            <li class="mb-2"><a href="<?= $base ?>/katalog.php">Jelajahi UMKM</a></li>
            <li class="mb-2"><a href="<?= $base ?>/index.php#kategori">Kategori Usaha</a></li>
            <li class="mb-2"><a href="<?= $base ?>/index.php#cara-kerja">Cara Kerja</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 col-6">
          <h6 class="text-white mb-3">Wilayah Layanan</h6>
          <ul class="list-unstyled small">
            <li class="mb-2"><a href="<?= $base ?>/katalog.php?kelurahan=Langkapura"><i class="bi bi-check2 text-success me-1"></i>Kel. Langkapura</a></li>
            <li class="mb-2"><a href="<?= $base ?>/katalog.php?kelurahan=Langkapura+Baru"><i class="bi bi-check2 text-success me-1"></i>Kel. Langkapura Baru</a></li>
            <li class="mb-2"><a href="<?= $base ?>/katalog.php?kelurahan=Gunung+Terang"><i class="bi bi-check2 text-success me-1"></i>Kel. Gunung Terang</a></li>
            <li class="mb-2"><a href="<?= $base ?>/katalog.php?kelurahan=Bilabong+Jaya"><i class="bi bi-check2 text-success me-1"></i>Kel. Bilabong Jaya</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="text-white mb-3">Pemilik UMKM</h6>
          <p class="small text-light-50 mb-3">
            Punya usaha di Langkapura? Publikasikan profil tokomu agar lebih mudah ditemukan pelanggan.
          </p>
          <a href="<?= $base ?>/register.php" class="btn btn-outline-light btn-sm w-100 mb-2">
            <i class="bi bi-pencil-square me-1"></i> Daftarkan Usaha Gratis
          </a>
          <a href="<?= $base ?>/login.php" class="btn btn-sm btn-link text-white-50 text-decoration-none d-block text-center">
            Sudah terdaftar? Masuk di sini
          </a>
        </div>
      </div>

      <hr style="border-color: rgba(255, 255, 255, 0.1);">

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small py-2 text-white-50">
        <div>
          &copy; <?= date('Y') ?> <strong>LangkapuraLokal</strong>. Hak Cipta Dilindungi. Proyek PPSI Prototipe MVP.
        </div>
        <div class="mt-2 mt-md-0">
          <span>Direct Contact via WhatsApp • Tanpa Biaya Transaksi</span>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap 5.3 JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <!-- Client JS -->
  <script src="<?= $base ?>/assets/js/main.js"></script>
</body>
</html>

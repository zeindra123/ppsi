<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header("Location: " . getBaseUrl() . "/owner/index.php");
    exit;
}

$error = '';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Nama lengkap, email, dan kata sandi wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format alamat email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Kata sandi minimal 6 karakter.';
    } elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } else {
        // Cek apakah email sudah terdaftar
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        if ($stmtCheck->fetch()) {
            $error = 'Alamat email sudah terdaftar. Silakan gunakan email lain atau masuk.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $stmtInsert = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, 'owner', 'active')");
            $stmtInsert->execute([$name, $email, $phone, $passwordHash]);
            $newUserId = $pdo->lastInsertId();

            // Login otomatis
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'owner';

            setFlash('success', 'Pendaftaran akun pemilik usaha berhasil! Silakan daftarkan profil usaha Anda.');
            header("Location: " . getBaseUrl() . "/owner/tambah.php");
            exit;
        }
    }
}

$pageTitle = 'Pendaftaran Akun Pemilik UMKM';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="text-center mb-4">
          <div class="category-icon-box mx-auto mb-3" style="width: 52px; height: 52px;">
            <i class="bi bi-shop text-success fs-3"></i>
          </div>
          <h1 class="h3 fw-bold mb-1">Daftar Akun Pemilik Usaha</h1>
          <p class="text-muted small">Promosikan produk dan layanan UMKM Anda ke seluruh warga Langkapura</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger small border-0 py-2 d-flex align-items-center mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= sanitize($error) ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= $base ?>/register.php" method="POST">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nama Lengkap Pemilik <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control bg-light" placeholder="contoh: Hendra Setiawan" required value="<?= isset($_POST['name']) ? sanitize($_POST['name']) : '' ?>">
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Alamat Email Aktif <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control bg-light" placeholder="nama@email.com" required value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Nomor WhatsApp / HP</label>
            <input type="text" name="phone" class="form-control bg-light" placeholder="contoh: 081234567890" value="<?= isset($_POST['phone']) ? sanitize($_POST['phone']) : '' ?>">
            <div class="form-text" style="font-size: 0.75rem;">Nomor ini untuk komunikasi akun dan koordinasi verifikasi admin.</div>
          </div>

          <div class="row g-2 mb-4">
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Kata Sandi <span class="text-danger">*</span></label>
              <input type="password" name="password" class="form-control bg-light" placeholder="Min. 6 karakter" required>
            </div>
            <div class="col-md-6 col-12">
              <label class="form-label small fw-semibold">Ulangi Kata Sandi <span class="text-danger">*</span></label>
              <input type="password" name="password_confirm" class="form-control bg-light" placeholder="Ulangi kata sandi" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
            <i class="bi bi-person-plus-fill me-1"></i> Buat Akun & Lanjut Daftarkan Usaha
          </button>
        </form>

        <div class="text-center small text-muted">
          Sudah memiliki akun? <a href="<?= $base ?>/login.php" class="text-success fw-semibold text-decoration-none">Masuk di sini</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: " . getBaseUrl() . "/admin/index.php");
    } else {
        header("Location: " . getBaseUrl() . "/owner/index.php");
    }
    exit;
}

$error = '';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Quick demo login handling
    if (isset($_POST['quick_demo'])) {
        $demoRole = $_POST['quick_demo'];
        if ($demoRole === 'admin') {
            $email = 'admin@langkapuralokal.id';
            $password = 'admin123';
        } elseif ($demoRole === 'owner') {
            $email = 'budi@umkm.id';
            $password = 'pemilik123';
        }
    }

    if (empty($email) || empty($password)) {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            setFlash('success', 'Selamat datang kembali, ' . $user['name'] . '!');

            if ($user['role'] === 'admin') {
                header("Location: " . getBaseUrl() . "/admin/index.php");
            } else {
                header("Location: " . getBaseUrl() . "/owner/index.php");
            }
            exit;
        } else {
            $error = 'Email atau kata sandi yang Anda masukkan tidak sesuai.';
        }
    }
}

$pageTitle = 'Masuk ke Akun';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7 col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <div class="text-center mb-4">
          <div class="category-icon-box mx-auto mb-3" style="width: 52px; height: 52px;">
            <i class="bi bi-person-lock fs-3"></i>
          </div>
          <h1 class="h3 fw-bold mb-1">Masuk ke Akun</h1>
          <p class="text-muted small">Kelola profil usaha dan moderasi UMKM Langkapura</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger small border-0 py-2 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= sanitize($error) ?></div>
          </div>
        <?php endif; ?>

        <form action="<?= $base ?>/login.php" method="POST">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Alamat Email</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control bg-light border-start-0" placeholder="nama@email.com" required value="<?= isset($_POST['email']) ? sanitize($_POST['email']) : '' ?>">
            </div>
          </div>

          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <label class="form-label small fw-semibold mb-0">Kata Sandi</label>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" required>
            </div>
          </div>

          <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
          </button>
        </form>

        <div class="text-center small text-muted mb-4">
          Belum punya akun usaha? <a href="<?= $base ?>/register.php" class="text-success fw-semibold text-decoration-none">Daftar di sini</a>
        </div>

        <!-- Kartu Demo Quick Login untuk Pengujian Dosen/Evaluator -->
        <div class="p-3 bg-light rounded-3 border small">
          <div class="fw-semibold text-dark mb-2 text-center">
            <i class="bi bi-stars text-warning me-1"></i> Mode Demo Cepat (1-Klik)
          </div>
          <div class="row g-2">
            <div class="col-6">
              <form action="<?= $base ?>/login.php" method="POST">
                <input type="hidden" name="quick_demo" value="owner">
                <button type="submit" class="btn btn-sm btn-outline-success w-100 py-2 text-nowrap">
                  <i class="bi bi-shop me-1"></i> Login Pemilik
                </button>
              </form>
            </div>
            <div class="col-6">
              <form action="<?= $base ?>/login.php" method="POST">
                <input type="hidden" name="quick_demo" value="admin">
                <button type="submit" class="btn btn-sm btn-outline-dark w-100 py-2 text-nowrap">
                  <i class="bi bi-shield-lock me-1"></i> Login Admin
                </button>
              </form>
            </div>
          </div>
          <div class="text-muted mt-2 text-center" style="font-size: 0.75rem;">
            Akun admin: <code>admin@langkapuralokal.id</code> / <code>admin123</code>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM karyawan WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    if ($u && password_verify($pass, $u['password'])) {
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['nama']    = $u['nama'];
        $_SESSION['role']    = $u['role'];
        redirect($u['role'] === 'admin' ? '/karyawan-wa/admin/index.php' : '/karyawan-wa/absensi/index.php');
    } else {
        $error = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — Sistem Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo">
      <i data-lucide="user-round"></i>
    </div>
    <h1 class="auth-title">Selamat Datang</h1>
    <p class="auth-sub">Masuk ke Sistem Karyawan</p>

    <?php if($error): ?>
      <div class="alert alert-error">
        <i data-lucide="alert-circle"></i> <?= $error ?>
      </div>
    <?php endif; ?>
    <?php if(!empty($_SESSION['sukses'])): ?>
      <div class="alert alert-success">
        <i data-lucide="check-circle"></i> <?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" placeholder="nama@email.com" required>
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Masukkan password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        Masuk <i data-lucide="arrow-right"></i>
      </button>
    </form>

    <p class="text-center text-muted" style="margin-top:20px;font-size:13.5px">
      Belum punya akun?
      <a href="register.php" style="color:var(--mahogany);font-weight:700;text-decoration:none">Daftar sekarang</a>
    </p>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
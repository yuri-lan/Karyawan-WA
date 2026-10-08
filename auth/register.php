<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../api/fonnte.php';

$modal = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik      = trim($_POST['nik']);
    $nama     = trim($_POST['nama']);
    $jabatan  = trim($_POST['jabatan']);
    $no_hp    = formatNomor($_POST['no_hp']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $konfirm  = $_POST['konfirmasi'];

    if (!$nik || !$nama || !$jabatan || !$no_hp || !$email || !$password || !$konfirm) {
        $modal = ['type'=>'warning', 'title'=>'Data Belum Lengkap', 'text'=>'Semua field wajib diisi.'];
    } elseif ($password !== $konfirm) {
        $modal = ['type'=>'warning', 'title'=>'Password Tidak Sama', 'text'=>'Password dan konfirmasi password harus sama.'];
    } elseif (strlen($password) < 6) {
        $modal = ['type'=>'warning', 'title'=>'Password Terlalu Pendek', 'text'=>'Password minimal 6 karakter.'];
    } elseif (!preg_match('/^62[0-9]{8,13}$/', $no_hp)) {
        $modal = ['type'=>'warning', 'title'=>'Nomor WhatsApp Tidak Valid', 'text'=>'Gunakan format: 08xxxxxxxxxx'];
    } else {
        // Cek duplikat NIK
        $stmt = $conn->prepare("SELECT nik FROM karyawan WHERE nik=?");
        $stmt->bind_param('s', $nik);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $modal = ['type'=>'danger', 'title'=>'NIK Sudah Terdaftar',
                      'text'=>'NIK <b>'.htmlspecialchars($nik).'</b> sudah digunakan oleh karyawan lain. Silakan gunakan NIK yang berbeda.'];
        } else {
            // Cek duplikat Email
            $stmt = $conn->prepare("SELECT email FROM karyawan WHERE email=?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $modal = ['type'=>'danger', 'title'=>'Email Sudah Terdaftar',
                          'text'=>'Email <b>'.htmlspecialchars($email).'</b> sudah digunakan. Silakan gunakan email lain atau login.'];
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "INSERT INTO karyawan (nik,nama,jabatan,no_hp,email,password) VALUES (?,?,?,?,?,?)"
                );
                $stmt->bind_param('ssssss', $nik, $nama, $jabatan, $no_hp, $email, $hash);

                if ($stmt->execute()) {
                    $pesanKaryawan = "REGISTRASI BERHASIL\n\nHalo $nama,\n\nData Anda telah berhasil terdaftar sebagai karyawan.\n\nNIK     : $nik\nJabatan : $jabatan\n\nSilakan login menggunakan akun yang telah dibuat.\n\nTerima kasih.";
                    kirimWA($no_hp, $pesanKaryawan);

                    $pesanAdmin = "NOTIFIKASI KARYAWAN BARU\n\nTelah terdaftar karyawan baru:\n\nNama    : $nama\nNIK     : $nik\nJabatan : $jabatan\nNo. HP  : $no_hp\n\nSilakan cek sistem untuk melihat data lengkap.";
                    kirimWA(WA_ADMIN, $pesanAdmin);

                    $_SESSION['sukses'] = 'Registrasi berhasil! Silakan login.';
                    redirect('login.php');
                } else {
                    $modal = ['type'=>'danger', 'title'=>'Gagal Menyimpan', 'text'=>'Terjadi kesalahan saat menyimpan data.'];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — Sistem Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../config/modal.php'; ?>

<div class="auth-wrap" style="padding:40px 24px">
  <div class="auth-card" style="max-width:520px">
    <div class="auth-logo"><i data-lucide="user-plus"></i></div>
    <h1 class="auth-title">Buat Akun Baru</h1>
    <p class="auth-sub">Lengkapi data berikut untuk mendaftar</p>

    <form method="post">
      <div class="field"><label>NIK</label><input name="nik" placeholder="Contoh: 2026001" value="<?= htmlspecialchars($_POST['nik'] ?? '') ?>" required></div>
      <div class="field"><label>Nama Lengkap</label><input name="nama" placeholder="Nama lengkap" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required></div>
      <div class="field"><label>Jabatan</label><input name="jabatan" placeholder="Contoh: Staff IT" value="<?= htmlspecialchars($_POST['jabatan'] ?? '') ?>" required></div>
      <div class="field"><label>No. WhatsApp</label><input name="no_hp" placeholder="08xxxxxxxxxx" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" placeholder="nama@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" placeholder="Minimal 6 karakter" required></div>
      <div class="field"><label>Konfirmasi Password</label><input type="password" name="konfirmasi" placeholder="Ulangi password" required></div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        Daftar <i data-lucide="arrow-right"></i>
      </button>
    </form>

    <p class="text-center text-muted" style="margin-top:20px;font-size:13.5px">
      Sudah punya akun?
      <a href="login.php" style="color:var(--mahogany);font-weight:700;text-decoration:none">Login di sini</a>
    </p>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
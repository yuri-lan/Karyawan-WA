<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../api/fonnte.php';
cekAdmin();

$modal = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik=trim($_POST['nik']); $nama=trim($_POST['nama']);
    $jabatan=trim($_POST['jabatan']); $no_hp=formatNomor($_POST['no_hp']);
    $email=trim($_POST['email']); $pass=$_POST['password'];

    if(!$nik||!$nama||!$jabatan||!$no_hp||!$email||!$pass){
        $modal = ['type'=>'warning', 'title'=>'Data Belum Lengkap', 'text'=>'Semua field wajib diisi.'];
    } else {
        // Cek duplikat NIK
        $stmt = $conn->prepare("SELECT nik FROM karyawan WHERE nik=?");
        $stmt->bind_param('s', $nik); $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $modal = ['type'=>'danger', 'title'=>'NIK Sudah Terdaftar',
                      'text'=>'NIK <b>'.htmlspecialchars($nik).'</b> sudah ada di database. Gunakan NIK lain.'];
        } else {
            // Cek duplikat Email
            $stmt = $conn->prepare("SELECT email FROM karyawan WHERE email=?");
            $stmt->bind_param('s', $email); $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $modal = ['type'=>'danger', 'title'=>'Email Sudah Terdaftar',
                          'text'=>'Email <b>'.htmlspecialchars($email).'</b> sudah digunakan.'];
            } else {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO karyawan (nik,nama,jabatan,no_hp,email,password) VALUES (?,?,?,?,?,?)");
                $stmt->bind_param('ssssss',$nik,$nama,$jabatan,$no_hp,$email,$hash);

                if($stmt->execute()){
                    kirimWA($no_hp, "REGISTRASI BERHASIL\n\nHalo $nama,\n\nAkun Anda telah dibuat admin.\n\nNIK     : $nik\nJabatan : $jabatan\n\nTerima kasih.");
                    kirimWA(WA_ADMIN, "KARYAWAN BARU\n\nNama: $nama\nNIK: $nik\nJabatan: $jabatan\nHP: $no_hp");
                    redirect('index.php');
                } else {
                    $modal = ['type'=>'danger', 'title'=>'Gagal Menyimpan', 'text'=>'Terjadi kesalahan pada database.'];
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
<title>Tambah Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../config/modal.php'; ?>

<div class="auth-wrap" style="padding:40px 24px">
  <div class="auth-card" style="max-width:520px">
    <div class="auth-logo"><i data-lucide="user-plus"></i></div>
    <h1 class="auth-title">Tambah Karyawan</h1>
    <p class="auth-sub">Isi data karyawan baru</p>

    <form method="post">
      <div class="field"><label>NIK</label><input name="nik" value="<?= htmlspecialchars($_POST['nik'] ?? '') ?>" required></div>
      <div class="field"><label>Nama</label><input name="nama" value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required></div>
      <div class="field"><label>Jabatan</label><input name="jabatan" value="<?= htmlspecialchars($_POST['jabatan'] ?? '') ?>" required></div>
      <div class="field"><label>No WhatsApp</label><input name="no_hp" value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" required></div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        <i data-lucide="save"></i> Simpan Data
      </button>
    </form>
    <p class="text-center" style="margin-top:16px">
      <a href="index.php" style="color:var(--text-muted);font-size:13.5px;text-decoration:none">
        <i data-lucide="arrow-left" style="width:14px;height:14px;vertical-align:middle"></i> Kembali
      </a>
    </p>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
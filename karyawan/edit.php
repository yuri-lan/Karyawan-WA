<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../api/fonnte.php';
cekAdmin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if(!$row) redirect('index.php');

$modal = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nama=trim($_POST['nama']); $jabatan=trim($_POST['jabatan']);
    $no_hp=formatNomor($_POST['no_hp']); $email=trim($_POST['email']);

    // Cek email duplikat (selain dirinya sendiri)
    $stmt = $conn->prepare("SELECT email FROM karyawan WHERE email=? AND id!=?");
    $stmt->bind_param('si', $email, $id); $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $modal = ['type'=>'danger', 'title'=>'Email Sudah Dipakai',
                  'text'=>'Email <b>'.htmlspecialchars($email).'</b> sudah digunakan karyawan lain.'];
    } else {
        $stmt=$conn->prepare("UPDATE karyawan SET nama=?,jabatan=?,no_hp=?,email=? WHERE id=?");
        $stmt->bind_param('ssssi',$nama,$jabatan,$no_hp,$email,$id);
        if($stmt->execute()){
            kirimWA($no_hp,
"PEMBARUAN DATA

Halo $nama,

Data karyawan Anda telah diperbarui oleh administrator.

Data terbaru:
Jabatan : $jabatan
No. HP  : $no_hp
Email   : $email

Jika perubahan ini tidak Anda lakukan, silakan hubungi administrator.");
            redirect('index.php');
        } else {
            $modal = ['type'=>'danger', 'title'=>'Gagal Update', 'text'=>'Terjadi kesalahan pada database.'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>

<?php require_once __DIR__ . '/../config/modal.php'; ?>

<div class="auth-wrap" style="padding:40px 24px">
  <div class="auth-card" style="max-width:520px">
    <div class="auth-logo"><i data-lucide="pencil"></i></div>
    <h1 class="auth-title">Edit Karyawan</h1>
    <p class="auth-sub">Ubah data karyawan</p>

    <form method="post">
      <div class="field"><label>Nama</label><input name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required></div>
      <div class="field"><label>Jabatan</label><input name="jabatan" value="<?= htmlspecialchars($row['jabatan']) ?>" required></div>
      <div class="field"><label>No WhatsApp</label><input name="no_hp" value="<?= htmlspecialchars($row['no_hp']) ?>" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>" required></div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        <i data-lucide="save"></i> Update Data
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
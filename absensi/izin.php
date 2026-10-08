<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/fonnte.php';
cekLogin();

$uid = $_SESSION['user_id'];
$hariIni = date('Y-m-d');
$tipe = $_GET['tipe'] ?? 'Izin';
if (!in_array($tipe, ['Izin','Sakit'])) $tipe = 'Izin';

$stmt = $conn->prepare("SELECT id FROM absensi WHERE id_karyawan=? AND tanggal=?");
$stmt->bind_param('is',$uid,$hariIni); $stmt->execute();
if($stmt->get_result()->num_rows > 0){
    $_SESSION['err'] = 'Anda sudah absen hari ini!';
    redirect('index.php');
}

$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param('i',$uid); $stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$error = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $ket = trim($_POST['keterangan'] ?? '');
    if(!$ket) {
        $error = 'Keterangan wajib diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO absensi (id_karyawan,tanggal,status,keterangan) VALUES (?,?,?,?)");
        $stmt->bind_param('isss',$uid,$hariIni,$tipe,$ket);
        $stmt->execute();

        $tgl = date('d F Y');
        $pesan = "PENGAJUAN " . strtoupper($tipe) . "

Nama    : {$user['nama']}
NIK     : {$user['nik']}
Tanggal : $tgl
Status  : " . strtoupper($tipe) . "
Alasan  : $ket

Pengajuan Anda telah dicatat.";
        kirimWA($user['no_hp'], $pesan);

        $pesanAdmin = "NOTIFIKASI " . strtoupper($tipe) . "

Karyawan : {$user['nama']}
NIK      : {$user['nik']}
Tanggal  : $tgl
Alasan   : $ket";
        kirimWA(WA_ADMIN, $pesanAdmin);

        $_SESSION['sukses'] = "Pengajuan $tipe berhasil!";
        redirect('index.php');
    }
}

// Icon & warna sesuai tipe
$icon = $tipe === 'Izin' ? 'file-text' : 'thermometer';
$subtitle = $tipe === 'Izin' ? 'Ajukan izin untuk hari ini' : 'Ajukan sakit untuk hari ini';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Form <?= $tipe ?> — Sistem Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="/karyawan-wa/absensi/index.php">
      <div class="brand-icon"><i data-lucide="clock"></i></div>
      Sistem Karyawan
    </a>
    <div style="flex:1"></div>
    <a href="/karyawan-wa/auth/logout.php" class="nav-link"><i data-lucide="log-out"></i> Logout</a>
  </div>
</div>

<div class="container-narrow animate-in">

  <div style="margin-bottom:20px">
    <a href="index.php" class="nav-link" style="padding-left:0">
      <i data-lucide="arrow-left"></i> Kembali ke Absensi
    </a>
  </div>

  <div class="auth-card" style="max-width:520px">
    <div class="auth-logo">
      <i data-lucide="<?= $icon ?>"></i>
    </div>
    <h1 class="auth-title">Form <?= $tipe ?></h1>
    <p class="auth-sub"><?= $subtitle ?></p>

    <?php if($error): ?>
      <div class="alert alert-error">
        <i data-lucide="alert-circle"></i> <?= $error ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="field">
        <label>Nama</label>
        <input type="text" value="<?= htmlspecialchars($user['nama']) ?>" disabled
               style="background:var(--cream-2);color:var(--text-muted);cursor:not-allowed">
      </div>
      <div class="field">
        <label>NIK</label>
        <input type="text" value="<?= htmlspecialchars($user['nik']) ?>" disabled
               style="background:var(--cream-2);color:var(--text-muted);cursor:not-allowed">
      </div>
      <div class="field">
        <label>Tanggal</label>
        <input type="text" value="<?= date('d F Y') ?>" disabled
               style="background:var(--cream-2);color:var(--text-muted);cursor:not-allowed">
      </div>
      <div class="field">
        <label>Alasan / Keterangan</label>
        <textarea name="keterangan" rows="4" required
          placeholder="<?= $tipe === 'Izin' ? 'Contoh: Ada urusan keluarga penting' : 'Contoh: Demam tinggi, ada surat dokter' ?>"></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px">
        <i data-lucide="send"></i> Kirim Pengajuan <?= $tipe ?>
      </button>
    </form>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
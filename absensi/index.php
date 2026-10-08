<?php
require_once __DIR__ . '/../config/database.php';
cekLogin();
if($_SESSION['role']==='admin') redirect('/karyawan-wa/admin/index.php');

$uid = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param('i',$uid); $stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$hariIni = date('Y-m-d');
$stmt = $conn->prepare("SELECT * FROM absensi WHERE id_karyawan=? AND tanggal=?");
$stmt->bind_param('is',$uid,$hariIni); $stmt->execute();
$absen = $stmt->get_result()->fetch_assoc();

function tglIndo($t){
    $hari = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
    $bln  = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return $hari[date('l',strtotime($t))].', '.date('d',strtotime($t)).' '.$bln[(int)date('n',strtotime($t))].' '.date('Y',strtotime($t));
}

$status='BELUM ABSEN';
if($absen){
    if(in_array($absen['status'],['Izin','Sakit'])) $status = strtoupper($absen['status']);
    elseif($absen['jam_masuk'] && !$absen['jam_pulang']) $status = strtoupper($absen['status']) . ' — SUDAH MASUK';
    elseif($absen['jam_masuk'] && $absen['jam_pulang']) $status = strtoupper($absen['status']) . ' — SELESAI';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Absensi — Sistem Karyawan</title>
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
    <a href="/karyawan-wa/auth/logout.php" class="nav-link">
      <i data-lucide="log-out"></i> Logout
    </a>
  </div>
</div>

<div class="container-narrow animate-in">

  <?php if(!empty($_SESSION['sukses'])): ?>
    <div class="alert alert-success"><i data-lucide="check-circle"></i> <?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?></div>
  <?php endif; ?>
  <?php if(!empty($_SESSION['err'])): ?>
    <div class="alert alert-error"><i data-lucide="alert-circle"></i> <?= $_SESSION['err']; unset($_SESSION['err']); ?></div>
  <?php endif; ?>

  <div class="absen-card">
    <div class="absen-profile">
      <div class="absen-avatar"><?= strtoupper(substr($user['nama'],0,1)) ?></div>
      <div>
        <div class="absen-name"><?= htmlspecialchars($user['nama']) ?></div>
        <div class="absen-nik"><?= htmlspecialchars($user['nik']) ?> &middot; <?= htmlspecialchars($user['jabatan']) ?></div>
      </div>
    </div>

    <div class="date-display">
      <i data-lucide="calendar"></i> <?= tglIndo($hariIni) ?>
    </div>

    <?php
      $cls = 's-belum'; $icon = 'hourglass';
      if($status==='SUDAH ABSEN MASUK') { $cls='s-masuk'; $icon='log-in'; }
      elseif($status==='SELESAI') { $cls='s-selesai'; $icon='check-circle-2'; }
    ?>
    <div class="status-pill <?= $cls ?>">
      <i data-lucide="<?= $icon ?>"></i> <?= $status ?>
    </div>

    <?php if($absen && $absen['jam_masuk']): ?>
      <div class="absen-info">
        <div class="absen-info-item">
          <div class="lbl"><i data-lucide="log-in"></i> Jam Masuk</div>
          <div class="val"><?= $absen['jam_masuk'] ?></div>
        </div>
        <?php if($absen['jam_pulang']): ?>
        <div class="absen-info-item">
          <div class="lbl"><i data-lucide="log-out"></i> Jam Pulang</div>
          <div class="val"><?= $absen['jam_pulang'] ?></div>
        </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div style="margin-top:24px">
      <?php if(!$absen): ?>
        <a href="masuk.php" class="btn btn-primary btn-full">
          <i data-lucide="log-in"></i> Absen Masuk
        </a>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px">
          <a href="izin.php?tipe=Izin" class="btn btn-ghost"><i data-lucide="file-text"></i> Ajukan Izin</a>
          <a href="izin.php?tipe=Sakit" class="btn btn-ghost"><i data-lucide="thermometer"></i> Ajukan Sakit</a>
        </div>
      <?php elseif(in_array($absen['status'],['Izin','Sakit'])): ?>
        <div class="alert alert-info">
          <i data-lucide="info"></i>
          <div>
            <b>Status: <?= $absen['status'] ?></b><br>
            <?= htmlspecialchars($absen['keterangan'] ?? '') ?>
          </div>
        </div>
      <?php elseif($absen && !$absen['jam_pulang']): ?>
        <a href="pulang.php" class="btn btn-gold btn-full">
          <i data-lucide="log-out"></i> Absen Pulang
        </a>
      <?php else: ?>
        <div class="alert alert-success">
          <i data-lucide="check-circle"></i> Absensi hari ini sudah selesai
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
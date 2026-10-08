<?php
require_once __DIR__ . '/../config/database.php';
cekAdmin();

$total = $conn->query("SELECT COUNT(*) c FROM karyawan WHERE role='karyawan'")->fetch_assoc()['c'];
$hariIni = date('Y-m-d');
$hadir  = $conn->query("SELECT COUNT(*) c FROM absensi WHERE tanggal='$hariIni'")->fetch_assoc()['c'];
$pulang = $conn->query("SELECT COUNT(*) c FROM absensi WHERE tanggal='$hariIni' AND jam_pulang IS NOT NULL")->fetch_assoc()['c'];
$belum  = $total - $hadir;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard — Sistem Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="/karyawan-wa/admin/index.php">
      <div class="brand-icon"><i data-lucide="layout-dashboard"></i></div>
      Sistem Karyawan
    </a>
    <a href="/karyawan-wa/admin/index.php" class="nav-link active"><i data-lucide="layout-dashboard"></i> Dashboard</a>
    <a href="/karyawan-wa/karyawan/index.php" class="nav-link"><i data-lucide="users"></i> Karyawan</a>
    <a href="/karyawan-wa/laporan.php" class="nav-link"><i data-lucide="file-text"></i> Laporan</a>
    <a href="/karyawan-wa/rekap.php" class="nav-link"><i data-lucide="bar-chart-3"></i> Rekap Bulanan</a>
    <div style="flex:1"></div>
    <a href="/karyawan-wa/auth/logout.php" class="nav-link"><i data-lucide="log-out"></i> Logout</a>
  </div>
</div>

<div class="container animate-in">
  <div class="page-header">
    <h1 class="page-title">Dashboard Admin</h1>
    <p class="page-sub">Ringkasan aktivitas karyawan hari ini &mdash; <?= date('d F Y') ?></p>
  </div>

  <div class="stats-grid">
    <div class="stat accent">
      <div class="stat-icon"><i data-lucide="users"></i></div>
      <div class="stat-label">Total Karyawan</div>
      <div class="stat-value"><?= $total ?></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i data-lucide="check-circle-2"></i></div>
      <div class="stat-label">Sudah Absen</div>
      <div class="stat-value"><?= $hadir ?></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i data-lucide="hourglass"></i></div>
      <div class="stat-label">Belum Absen</div>
      <div class="stat-value"><?= max(0,$belum) ?></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i data-lucide="log-out"></i></div>
      <div class="stat-label">Sudah Pulang</div>
      <div class="stat-value"><?= $pulang ?></div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
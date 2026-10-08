<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
cekAdmin();

// Filter bulan (default: bulan ini)
$bulan = $_GET['bulan'] ?? date('Y-m');

// Hitung tanggal awal & akhir bulan
$awal  = $bulan . '-01';
$akhir = date('Y-m-t', strtotime($awal));

// Ambil rekap per status
$rekap = ['Hadir'=>0, 'Terlambat'=>0, 'Izin'=>0, 'Sakit'=>0];
$stmt = $conn->prepare("
  SELECT status, COUNT(*) AS c
  FROM absensi
  WHERE tanggal BETWEEN ? AND ?
  GROUP BY status
");
$stmt->bind_param('ss', $awal, $akhir);
$stmt->execute();
$q = $stmt->get_result();
while ($r = $q->fetch_assoc()) {
    if (isset($rekap[$r['status']])) $rekap[$r['status']] = $r['c'];
}

// Total absensi bulan ini
$totalBulan = array_sum($rekap);

// Data detail per karyawan
$stmt = $conn->prepare("
  SELECT k.nama, k.nik,
    SUM(CASE WHEN a.status='Hadir' THEN 1 ELSE 0 END) AS hadir,
    SUM(CASE WHEN a.status='Terlambat' THEN 1 ELSE 0 END) AS terlambat,
    SUM(CASE WHEN a.status='Izin' THEN 1 ELSE 0 END) AS izin,
    SUM(CASE WHEN a.status='Sakit' THEN 1 ELSE 0 END) AS sakit,
    COUNT(*) AS total
  FROM karyawan k
  LEFT JOIN absensi a ON a.id_karyawan = k.id AND a.tanggal BETWEEN ? AND ?
  WHERE k.role = 'karyawan'
  GROUP BY k.id
  ORDER BY k.nama ASC
");
$stmt->bind_param('ss', $awal, $akhir);
$stmt->execute();
$detail = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Rekap Bulanan — Sistem Karyawan</title>
<link rel="stylesheet" href="/karyawan-wa/assets/style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <a class="brand" href="/karyawan-wa/admin/index.php">
      <div class="brand-icon"><i data-lucide="layout-dashboard"></i></div>
      Sistem Karyawan
    </a>
    <a href="/karyawan-wa/admin/index.php" class="nav-link"><i data-lucide="layout-dashboard"></i> Dashboard</a>
    <a href="/karyawan-wa/karyawan/index.php" class="nav-link"><i data-lucide="users"></i> Karyawan</a>
    <a href="/karyawan-wa/laporan.php" class="nav-link"><i data-lucide="file-text"></i> Laporan</a>
    <a href="/karyawan-wa/rekap.php" class="nav-link active"><i data-lucide="bar-chart-3"></i> Rekap Bulanan</a>
    <div style="flex:1"></div>
    <a href="/karyawan-wa/auth/logout.php" class="nav-link"><i data-lucide="log-out"></i> Logout</a>
  </div>
</div>

<div class="container animate-in">
  <div class="flex-between mb-6" style="flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">Rekap Bulanan</h1>
      <p class="page-sub">Statistik kehadiran karyawan per bulan</p>
    </div>
    <form method="get" class="flex flex-center gap-2">
      <div class="field" style="margin:0">
        <input type="month" name="bulan" value="<?= htmlspecialchars($bulan) ?>">
      </div>
      <button class="btn btn-primary"><i data-lucide="search"></i> Tampilkan</button>
    </form>
  </div>

  <!-- Kartu Rekap: 4 kolom 1 baris -->
  <div class="rekap-grid">
    <div class="rekap-card">
      <div class="rekap-icon"><i data-lucide="check-circle-2"></i></div>
      <div class="rekap-info">
        <div class="rekap-label">Hadir</div>
        <div class="rekap-value"><?= $rekap['Hadir'] ?></div>
      </div>
    </div>
    <div class="rekap-card">
      <div class="rekap-icon"><i data-lucide="clock-alert"></i></div>
      <div class="rekap-info">
        <div class="rekap-label">Terlambat</div>
        <div class="rekap-value"><?= $rekap['Terlambat'] ?></div>
      </div>
    </div>
    <div class="rekap-card">
      <div class="rekap-icon"><i data-lucide="file-text"></i></div>
      <div class="rekap-info">
        <div class="rekap-label">Izin</div>
        <div class="rekap-value"><?= $rekap['Izin'] ?></div>
      </div>
    </div>
    <div class="rekap-card">
      <div class="rekap-icon"><i data-lucide="thermometer"></i></div>
      <div class="rekap-info">
        <div class="rekap-label">Sakit</div>
        <div class="rekap-value"><?= $rekap['Sakit'] ?></div>
      </div>
    </div>
  </div>

  <div class="flex-between mt-6 mb-4" style="flex-wrap:wrap;gap:12px">
    <div>
      <h2 class="page-title" style="font-size:24px">Detail per Karyawan</h2>
      <p class="page-sub">Total <?= $totalBulan ?> absensi pada <?= date('F Y', strtotime($awal)) ?></p>
    </div>
    <a href="export_rekap_excel.php?bulan=<?= urlencode($bulan) ?>" class="btn btn-gold">
      <i data-lucide="file-spreadsheet"></i> Export Excel
    </a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>NIK</th>
          <th style="text-align:center">Hadir</th>
          <th style="text-align:center">Terlambat</th>
          <th style="text-align:center">Izin</th>
          <th style="text-align:center">Sakit</th>
          <th style="text-align:center">Total</th>
        </tr>
      </thead>
      <tbody>
        <?php $n=1; while($r = $detail->fetch_assoc()): ?>
        <tr>
          <td><?= $n++ ?></td>
          <td><b><?= htmlspecialchars($r['nama']) ?></b></td>
          <td><?= htmlspecialchars($r['nik']) ?></td>
          <td style="text-align:center"><?= $r['hadir'] ?></td>
          <td style="text-align:center"><?= $r['terlambat'] ?></td>
          <td style="text-align:center"><?= $r['izin'] ?></td>
          <td style="text-align:center"><?= $r['sakit'] ?></td>
          <td style="text-align:center"><span class="badge badge-gold"><?= $r['total'] ?></span></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>
</body>
</html>
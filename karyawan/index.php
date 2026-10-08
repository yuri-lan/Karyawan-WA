<?php
require_once __DIR__ . '/../config/database.php';
cekAdmin();

$data = $conn->query("SELECT * FROM karyawan ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Karyawan</title>
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
    <a href="/karyawan-wa/karyawan/index.php" class="nav-link active"><i data-lucide="users"></i> Karyawan</a>
    <a href="/karyawan-wa/laporan.php" class="nav-link"><i data-lucide="file-text"></i> Laporan</a>
    <a href="/karyawan-wa/rekap.php" class="nav-link"><i data-lucide="bar-chart-3"></i> Rekap Bulanan</a>
    <div style="flex:1"></div>
    <a href="/karyawan-wa/auth/logout.php" class="nav-link"><i data-lucide="log-out"></i> Logout</a>
  </div>
</div>

<div class="container animate-in">
  <div class="flex-between mb-6">
    <div class="page-header" style="margin:0">
      <h1 class="page-title">Data Karyawan</h1>
      <p class="page-sub">Kelola seluruh data karyawan</p>
    </div>
    <a href="tambah.php" class="btn btn-primary"><i data-lucide="plus"></i> Tambah Karyawan</a>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:60px">No</th>
          <th>NIK</th>
          <th>Nama</th>
          <th>Jabatan</th>
          <th>No. HP</th>
          <th style="width:160px">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; while($r = $data->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><b><?= htmlspecialchars($r['nik']) ?></b></td>
          <td><?= htmlspecialchars($r['nama']) ?></td>
          <td><span class="badge badge-gold"><?= htmlspecialchars($r['jabatan']) ?></span></td>
          <td><?= htmlspecialchars($r['no_hp']) ?></td>
          <td>
            <a class="btn btn-ghost btn-sm" href="edit.php?id=<?= $r['id'] ?>"><i data-lucide="pencil"></i> Edit</a>
            <a class="btn btn-primary btn-sm" href="hapus.php?id=<?= $r['id'] ?>"
               onclick="return confirm('Yakin hapus <?= htmlspecialchars($r['nama']) ?>?')"><i data-lucide="trash-2"></i></a>
          </td>
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
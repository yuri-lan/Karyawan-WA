<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
cekAdmin();

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$status  = $_GET['status'] ?? '';

$sql = "SELECT a.*, k.nama, k.nik FROM absensi a 
        JOIN karyawan k ON a.id_karyawan=k.id 
        WHERE a.tanggal=?";
if ($status) $sql .= " AND a.status=?";
$sql .= " ORDER BY a.jam_masuk ASC";

$stmt = $conn->prepare($sql);
if ($status) $stmt->bind_param('ss', $tanggal, $status);
else $stmt->bind_param('s', $tanggal);
$stmt->execute();
$data = $stmt->get_result();

// Simpan buat export
$_SESSION['laporan_query'] = ['tanggal'=>$tanggal, 'status'=>$status];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laporan Absensi</title>
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
    <a href="/karyawan-wa/laporan.php" class="nav-link active"><i data-lucide="file-text"></i> Laporan</a>
    <a href="/karyawan-wa/rekap.php" class="nav-link"><i data-lucide="bar-chart-3"></i> Rekap Bulanan</a>
    <div style="flex:1"></div>
    <a href="/karyawan-wa/auth/logout.php" class="nav-link"><i data-lucide="log-out"></i> Logout</a>
  </div>
</div>

<div class="container animate-in">
  <div class="page-header">
    <h1 class="page-title">Laporan Absensi</h1>
    <p class="page-sub">Filter dan export data absensi karyawan</p>
  </div>

  <div class="card mb-6">
    <form method="get" class="flex flex-center gap-3" style="flex-wrap:wrap">
      <div class="field" style="margin:0;flex:1;min-width:180px">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
      </div>
      <div class="field" style="margin:0;flex:1;min-width:180px">
        <label>Status</label>
        <select name="status">
          <option value="">Semua Status</option>
          <?php foreach(['Hadir','Terlambat','Izin','Sakit'] as $s): ?>
            <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field" style="margin:0">
        <label>&nbsp;</label>
        <button class="btn btn-primary"><i data-lucide="search"></i> Tampilkan</button>
      </div>
      <div class="field" style="margin:0">
        <label>&nbsp;</label>
        <a href="export_excel.php?tanggal=<?= urlencode($tanggal) ?>&status=<?= urlencode($status) ?>" class="btn btn-gold">
          <i data-lucide="file-spreadsheet"></i> Excel
        </a>
      </div>
      <div class="field" style="margin:0">
        <label>&nbsp;</label>
        <a href="export_pdf.php?tanggal=<?= urlencode($tanggal) ?>&status=<?= urlencode($status) ?>" target="_blank" class="btn btn-ghost">
          <i data-lucide="file-text"></i> PDF
        </a>
      </div>
    </form>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>No</th><th>Nama</th><th>NIK</th><th>Tanggal</th>
          <th>Masuk</th><th>Pulang</th><th>Status</th><th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php $n=1; while($r=$data->fetch_assoc()):
          $badge = 'badge-neutral';
          if($r['status']==='Hadir') $badge='badge-gold';
          elseif($r['status']==='Terlambat') $badge='badge-mahogany';
          elseif($r['status']==='Izin') $badge='badge-gold';
          elseif($r['status']==='Sakit') $badge='badge-mahogany';
        ?>
        <tr>
          <td><?= $n++ ?></td>
          <td><b><?= htmlspecialchars($r['nama']) ?></b></td>
          <td><?= htmlspecialchars($r['nik']) ?></td>
          <td><?= date('d/m/y',strtotime($r['tanggal'])) ?></td>
          <td><?= $r['jam_masuk'] ?: '-' ?></td>
          <td><?= $r['jam_pulang'] ?: '-' ?></td>
          <td><span class="badge <?= $badge ?>"><?= $r['status'] ?></span></td>
          <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
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
<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/config.php';
cekAdmin();

$bulan = $_GET['bulan'] ?? date('Y-m');
$awal  = $bulan . '-01';
$akhir = date('Y-m-t', strtotime($awal));

$stmt = $conn->prepare("
  SELECT k.nama, k.nik,
    SUM(CASE WHEN a.status='Hadir' THEN 1 ELSE 0 END) AS hadir,
    SUM(CASE WHEN a.status='Terlambat' THEN 1 ELSE 0 END) AS terlambat,
    SUM(CASE WHEN a.status='Izin' THEN 1 ELSE 0 END) AS izin,
    SUM(CASE WHEN a.status='Sakit' THEN 1 ELSE 0 END) AS sakit,
    COUNT(a.id) AS total
  FROM karyawan k
  LEFT JOIN absensi a ON a.id_karyawan = k.id AND a.tanggal BETWEEN ? AND ?
  WHERE k.role = 'karyawan'
  GROUP BY k.id ORDER BY k.nama ASC
");
$stmt->bind_param('ss', $awal, $akhir);
$stmt->execute();
$data = $stmt->get_result();

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="rekap_bulanan_'.$bulan.'.xls"');
?>
<table border="1">
<tr>
  <th colspan="8" style="background:#6B2C2C;color:#fff;font-size:14px;padding:8px">
    REKAP BULANAN — <?= date('F Y', strtotime($awal)) ?>
  </th>
</tr>
<tr style="background:#F4ECDD;color:#6B2C2C">
  <th>No</th><th>Nama</th><th>NIK</th>
  <th>Hadir</th><th>Terlambat</th><th>Izin</th><th>Sakit</th><th>Total</th>
</tr>
<?php $n=1; while($r=$data->fetch_assoc()): ?>
<tr>
  <td><?= $n++ ?></td>
  <td><?= htmlspecialchars($r['nama']) ?></td>
  <td><?= htmlspecialchars($r['nik']) ?></td>
  <td><?= $r['hadir'] ?></td>
  <td><?= $r['terlambat'] ?></td>
  <td><?= $r['izin'] ?></td>
  <td><?= $r['sakit'] ?></td>
  <td><?= $r['total'] ?></td>
</tr>
<?php endwhile; ?>
</table>
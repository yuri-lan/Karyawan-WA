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

// Header Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="laporan_absensi_'.$tanggal.'.xls"');
?>
<table border="1">
<tr>
  <th colspan="8" style="font-size:16px;background:#2563eb;color:#fff">
    LAPORAN ABSENSI KARYAWAN — <?= date('d F Y',strtotime($tanggal)) ?>
    <?= $status ? " (Status: $status)" : '' ?>
  </th>
</tr>
<tr style="background:#dbeafe">
  <th>No</th><th>Nama</th><th>NIK</th><th>Tanggal</th>
  <th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th><th>Keterangan</th>
</tr>
<?php $n=1; while($r=$data->fetch_assoc()): ?>
<tr>
  <td><?= $n++ ?></td>
  <td><?= htmlspecialchars($r['nama']) ?></td>
  <td><?= htmlspecialchars($r['nik']) ?></td>
  <td><?= $r['tanggal'] ?></td>
  <td><?= $r['jam_masuk'] ?: '-' ?></td>
  <td><?= $r['jam_pulang'] ?: '-' ?></td>
  <td><?= htmlspecialchars($r['status']) ?></td>
  <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
</tr>
<?php endwhile; ?>
</table>
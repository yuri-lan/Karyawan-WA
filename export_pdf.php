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
?>
<!DOCTYPE html><html><head><title>Laporan PDF</title>
<style>
body{font-family:Arial;padding:30px}
h2{text-align:center}
table{width:100%;border-collapse:collapse;margin-top:15px}
th,td{padding:8px;border:1px solid #333;font-size:13px}
th{background:#2563eb;color:#fff}
@media print { .no-print{display:none} }
</style></head><body onload="window.print()">
<div class="no-print" style="text-align:center;margin-bottom:15px">
  <button onclick="window.print()">🖨️ Print / Save as PDF</button>
</div>
<h2>LAPORAN ABSENSI KARYAWAN</h2>
<p style="text-align:center">Tanggal: <?= date('d F Y',strtotime($tanggal)) ?> 
<?= $status ? "| Status: $status" : '' ?></p>
<table>
<tr><th>No</th><th>Nama</th><th>NIK</th><th>Masuk</th><th>Pulang</th><th>Status</th><th>Keterangan</th></tr>
<?php $n=1; while($r=$data->fetch_assoc()): ?>
<tr>
  <td><?= $n++ ?></td>
  <td><?= htmlspecialchars($r['nama']) ?></td>
  <td><?= htmlspecialchars($r['nik']) ?></td>
  <td><?= $r['jam_masuk'] ?: '-' ?></td>
  <td><?= $r['jam_pulang'] ?: '-' ?></td>
  <td><?= htmlspecialchars($r['status']) ?></td>
  <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
</tr>
<?php endwhile; ?>
</table>
</body></html>
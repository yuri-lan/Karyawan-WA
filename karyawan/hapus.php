<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../api/fonnte.php';
cekAdmin();

$id = (int)($_GET['id'] ?? 0);

// 1. Ambil data dulu
$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param('i',$id); $stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if($row){
    // 2. Kirim WA
    $pesan = "NOTIFIKASI DATA KARYAWAN

Halo {$row['nama']},

Data Anda pada sistem karyawan telah dihapus oleh administrator.

Jika Anda merasa tidak melakukan permintaan tersebut, silakan hubungi administrator.";
    kirimWA($row['no_hp'], $pesan);

    // 3. Baru hapus
    $stmt = $conn->prepare("DELETE FROM karyawan WHERE id=?");
    $stmt->bind_param('i',$id); $stmt->execute();
}
redirect('index.php');
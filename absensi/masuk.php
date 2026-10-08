<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/fonnte.php';
cekLogin();

$uid = $_SESSION['user_id'];
$hariIni = date('Y-m-d');
$jam     = date('H:i:s');

// Cek sudah absen
$stmt = $conn->prepare("SELECT id FROM absensi WHERE id_karyawan=? AND tanggal=?");
$stmt->bind_param('is',$uid,$hariIni); $stmt->execute();
if($stmt->get_result()->num_rows > 0){
    $_SESSION['err'] = 'Anda sudah melakukan absensi hari ini!';
    redirect('index.php');
}

// ===== Level 2: Tentukan status otomatis =====
if ($jam <= JAM_TEPAT_WAKTU) {
    $status = 'Hadir';
} else {
    $status = 'Terlambat';
}

// Ambil data karyawan
$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param('i',$uid); $stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Simpan
$stmt = $conn->prepare("INSERT INTO absensi (id_karyawan,tanggal,jam_masuk,status) VALUES (?,?,?,?)");
$stmt->bind_param('isss',$uid,$hariIni,$jam,$status);
$stmt->execute();

$tgl = date('d F Y');

// ===== WA ke karyawan =====
$pesan = "ABSENSI MASUK

Nama    : {$user['nama']}
NIK     : {$user['nik']}
Tanggal : $tgl
Jam     : $jam
Status  : " . strtoupper($status) . "

Absensi masuk Anda telah berhasil dicatat.";
kirimWA($user['no_hp'], $pesan);

// ===== Level 5: WA ke admin =====
$pesanAdmin = "NOTIFIKASI ABSENSI

Karyawan : {$user['nama']}
NIK      : {$user['nik']}
Tanggal  : $tgl
Jam      : $jam
Status   : " . strtoupper($status);

if ($status === 'Terlambat') {
    $pesanAdmin .= "\n\n⚠️ Karyawan ini TERLAMBAT!";
}
kirimWA(WA_ADMIN, $pesanAdmin);

$_SESSION['sukses'] = "Absen masuk berhasil! Status: $status";
redirect('index.php');
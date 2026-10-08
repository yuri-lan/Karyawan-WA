<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/fonnte.php';
cekLogin();

$uid = $_SESSION['user_id'];
$hariIni = date('Y-m-d');
$jam     = date('H:i:s');

$stmt = $conn->prepare("SELECT * FROM absensi WHERE id_karyawan=? AND tanggal=?");
$stmt->bind_param('is',$uid,$hariIni); $stmt->execute();
$absen = $stmt->get_result()->fetch_assoc();

if(!$absen || !$absen['jam_masuk']){
    $_SESSION['err'] = 'Anda belum absen masuk hari ini!';
    redirect('index.php');
}
if($absen['jam_pulang']){
    $_SESSION['err'] = 'Anda sudah absen pulang!';
    redirect('index.php');
}

$stmt = $conn->prepare("SELECT * FROM karyawan WHERE id=?");
$stmt->bind_param('i',$uid); $stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("UPDATE absensi SET jam_pulang=? WHERE id=?");
$stmt->bind_param('si',$jam,$absen['id']); $stmt->execute();

$tgl = date('d F Y');

// WA ke karyawan
$pesan = "ABSENSI PULANG

Nama       : {$user['nama']}
NIK        : {$user['nik']}
Tanggal    : $tgl
Jam Masuk  : {$absen['jam_masuk']}
Jam Pulang : $jam
Status     : PULANG

Terima kasih.
Absensi pulang Anda telah berhasil dicatat.";
kirimWA($user['no_hp'], $pesan);

// Level 5: WA ke admin
$pesanAdmin = "NOTIFIKASI ABSEN PULANG

Karyawan   : {$user['nama']}
NIK        : {$user['nik']}
Tanggal    : $tgl
Jam Masuk  : {$absen['jam_masuk']}
Jam Pulang : $jam";
kirimWA(WA_ADMIN, $pesanAdmin);

$_SESSION['sukses'] = 'Absen pulang berhasil!';
redirect('index.php');
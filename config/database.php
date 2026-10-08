<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_karyawan';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Koneksi gagal: ' . $conn->connect_error);
}

// helper
function redirect($url) { header("Location: $url"); exit; }
function cekLogin() {
    if (!isset($_SESSION['user_id'])) redirect('/karyawan-wa/auth/login.php');
}
function cekAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        redirect('/karyawan-wa/index.php');
    }
}
function old($key, $default='') { return htmlspecialchars($_POST[$key] ?? $default); }
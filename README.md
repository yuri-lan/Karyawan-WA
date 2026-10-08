# Sistem Karyawan — Register, Absensi & WhatsApp Gateway Fonnte

Aplikasi web berbasis PHP + MySQL untuk mengelola data karyawan dan absensi, dengan integrasi **WhatsApp Gateway Fonnte** untuk notifikasi otomatis.

## Fitur

- Register & Login karyawan (password ter-hash)
- CRUD data karyawan
- Absensi masuk & pulang (status otomatis: Hadir / Terlambat)
- Pengajuan Izin & Sakit
- Dashboard admin dengan statistik
- Laporan absensi + filter + Export Excel/PDF
- Rekap bulanan per karyawan
- Notifikasi WhatsApp otomatis via Fonnte

## Teknologi

- **Backend**: PHP 8+, MySQL
- **Frontend**: HTML, CSS (custom design system), JavaScript
- **API**: Fonnte WhatsApp Gateway
- **Ikon**: Lucide Icons

## Instalasi

1. Clone repo ini ke `htdocs/`:
   ```bash
   git clone https://github.com/USERNAME/karyawan-wa.git
   ```

2. Copy file konfigurasi:
   ```bash
   cp config/config.example.php config/config.php
   ```

3. Isi `config/config.php` dengan token Fonnte dan nomor WA admin kamu.

4. Buat database `db_karyawan` di phpMyAdmin, lalu import struktur tabel (lihat `database.sql`).

5. Akses di browser: `http://localhost/karyawan-wa/`

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@email.com | admin123 |
| Karyawan | karyawan@email.com | karyawan123 |

> Ganti password default setelah login pertama!

## Struktur Folder

Lihat dokumentasi di laporan.

## Lisensi

Proyek ini dibuat untuk keperluan pembelajaran SMK RPL.

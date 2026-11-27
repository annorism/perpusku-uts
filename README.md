# PerpusKu — Sistem Manajemen Data Interaktif (Perpustakaan Mini)

## Cara menjalankan
**XAMPP:** salin folder `perpustakaan-mini` ke `htdocs`, nyalakan Apache,
buka http://localhost/perpustakaan-mini/

**Tanpa XAMPP (PHP terpasang):** di dalam folder ini jalankan
`php -S localhost:8000` lalu buka http://localhost:8000

Butuh PHP 7.4 atau lebih baru. File .php tidak bisa dibuka dengan klik dua kali.

## Struktur
- index.php    : beranda & statistik
- buku.php     : tambah buku + sampul (POST), cari (GET), hapus buku
- anggota.php  : daftar anggota (POST)
- pinjam.php   : pinjam & kembalikan buku
- includes/    : data, header, footer
- assets/      : style.css, script.js

## Pemenuhan ketentuan
| Ketentuan | Letak |
|---|---|
| HTML5 semantik (header, nav, main, section, article, footer) | includes/header.php, footer.php, semua halaman |
| Flexbox & Grid tanpa framework | assets/style.css |
| Mobile-first | gaya dasar untuk HP, media query min-width 600px & 860px |
| Interaktivitas JavaScript | mode gelap/terang, validasi real-time, filter buku, konfirmasi hapus |
| PHP GET & POST, tampil ulang data | pencarian (GET), form tambah/daftar/pinjam (POST), kotak "Pemberitahuan" |

## Catatan
Sampul: unggah JPG/PNG/WEBP maksimal 1 MB; tanpa foto, sampul dibuat otomatis.
Data disimpan di session: hilang jika sesi berakhir (misalnya browser ditutup).
Font diambil dari Google Fonts; tanpa internet akan memakai font cadangan.

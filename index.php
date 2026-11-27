<?php
require 'includes/data.php';
$judulHalaman = 'Beranda';
$dipinjam = count(array_filter($_SESSION['pinjam'], fn($p) => !$p['kembali']));
$statistik = [
    ['I', count($_SESSION['buku']), 'Koleksi buku'],
    ['II', count($_SESSION['anggota']), 'Anggota'],
    ['III', $dipinjam, 'Sedang dipinjam'],
];
require 'includes/header.php';
?>
<section class="jendela">
  <h1>Selamat Datang</h1>
  <div class="isi tengah">
    <p class="besar">Temukan bacaan berikutnya.</p>
    <p>Kelola buku, anggota, dan peminjaman di satu tempat.</p>
    <a class="tombol" href="pinjam.php">Pinjam Buku</a>
  </div>
</section>

<section aria-labelledby="judul-statistik">
  <h2 id="judul-statistik" class="subjudul">Ringkasan Perpustakaan</h2>
  <div class="grid">
    <?php foreach ($statistik as [$ikon, $angka, $label]): ?>
      <article class="kartu tengah">
        <p class="ikon" aria-hidden="true"><?= $ikon ?></p>
        <p class="angka"><?= $angka ?></p>
        <p><?= $label ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require 'includes/footer.php'; ?>

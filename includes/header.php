<?php
$menu = ['index.php' => 'Beranda', 'buku.php' => 'Buku', 'anggota.php' => 'Anggota', 'pinjam.php' => 'Pinjam'];
$aktif = basename($_SERVER['SCRIPT_NAME']);
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($judulHalaman) ?> · PerpusKu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/script.js" defer></script>
</head>
<body>
  <header class="kepala">
    <p class="logo">PerpusKu</p>
    <p class="slogan">Perpustakaan Mini</p>
    <button type="button" id="tombol-tema" class="tombol kecil" aria-pressed="false">Mode gelap</button>
  </header>

  <nav class="menu" aria-label="Menu utama">
    <?php foreach ($menu as $file => $label): ?>
      <a href="<?= $file ?>" <?= $aktif === $file ? 'aria-current="page"' : '' ?>><?= $label ?></a>
    <?php endforeach; ?>
  </nav>

  <main>
    <?php if ($flash): ?>
      <section class="jendela notif" role="status">
        <h2>Pemberitahuan</h2>
        <div class="isi">
          <p><strong><?= e($flash['pesan']) ?></strong></p>
          <?php if ($flash['detail']): ?>
            <dl>
              <?php foreach ($flash['detail'] as $label => $nilai): ?>
                <dt><?= e($label) ?></dt><dd><?= e($nilai) ?></dd>
              <?php endforeach; ?>
            </dl>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>

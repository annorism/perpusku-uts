<?php
require 'includes/data.php';
$judulHalaman = 'Anggota';
$error = [];
$input = ['nama' => '', 'email' => '', 'telepon' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($input as $kunci => $_) $input[$kunci] = ambil($_POST, $kunci);

    if (strlen($input['nama']) < 3) $error['nama'] = 'Nama minimal 3 karakter.';
    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) $error['email'] = 'Format email belum benar.';
    if (!preg_match('/^08[0-9]{8,11}$/', $input['telepon'])) $error['telepon'] = 'Nomor diawali 08, total 10-13 angka.';

    if (!$error) {
        $_SESSION['anggota'][id_baru($_SESSION['anggota'])] = $input;
        selesai('anggota.php', 'Anggota baru berhasil didaftarkan.', [
            'Nama' => $input['nama'], 'Email' => $input['email'], 'Telepon' => $input['telepon'],
        ]);
    }
}

require 'includes/header.php';
?>
<div class="dua-kolom">
  <section class="jendela">
    <h1>Daftar Anggota Baru</h1>
    <form class="isi" method="post" action="anggota.php" data-validasi novalidate>
      <label for="nama">Nama lengkap</label>
      <input id="nama" name="nama" required minlength="3" maxlength="60" value="<?= e($input['nama']) ?>">
      <small class="error"><?= e($error['nama'] ?? '') ?></small>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" required maxlength="80" value="<?= e($input['email']) ?>">
      <small class="error"><?= e($error['email'] ?? '') ?></small>

      <label for="telepon">No. HP</label>
      <input id="telepon" name="telepon" type="tel" inputmode="numeric" required pattern="08[0-9]{8,11}"
             data-pesan="Nomor diawali 08, total 10-13 angka." value="<?= e($input['telepon']) ?>">
      <small class="error"><?= e($error['telepon'] ?? '') ?></small>

      <button class="tombol">Daftarkan</button>
    </form>
  </section>

  <section aria-labelledby="judul-anggota">
    <h2 id="judul-anggota" class="subjudul">Daftar Anggota</h2>
    <div class="grid">
      <?php foreach ($_SESSION['anggota'] as $id => $a): ?>
        <article class="kartu">
          <span class="stiker">ID #<?= $id ?></span>
          <h3><?= e($a['nama']) ?></h3>
          <p><?= e($a['email']) ?></p>
          <p><?= e($a['telepon']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</div>
<?php require 'includes/footer.php'; ?>

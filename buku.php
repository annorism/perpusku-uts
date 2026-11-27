<?php
require 'includes/data.php';
$judulHalaman = 'Buku';
$error = [];
$input = ['judul' => '', 'penulis' => '', 'tahun' => '', 'kategori' => ''];

// ===== Proses POST: tambah & hapus =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // $_POST kosong artinya kiriman melebihi batas ukuran server
    if (!$_POST) selesai('buku.php', 'File terlalu besar. Pilih foto maksimal 1 MB ya.');

    if (ambil($_POST, 'aksi') === 'hapus') {
        $id = (int) ambil($_POST, 'id');
        if (sedang_dipinjam($id)) selesai('buku.php', 'Buku sedang dipinjam sehingga belum dapat dihapus.');
        unset($_SESSION['buku'][$id]);
        selesai('buku.php', 'Buku berhasil dihapus.');
    }

    foreach ($input as $kunci => $_) $input[$kunci] = ambil($_POST, $kunci);

    [$sampul, $errorSampul] = proses_sampul($_FILES['sampul'] ?? null);
    if ($errorSampul) $error['sampul'] = $errorSampul;

    if (strlen($input['judul']) < 3)   $error['judul'] = 'Judul minimal 3 karakter.';
    if (strlen($input['penulis']) < 3) $error['penulis'] = 'Nama penulis minimal 3 karakter.';
    $tahun = filter_var($input['tahun'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => (int) date('Y')]]);
    if ($tahun === false)              $error['tahun'] = 'Tahun harus antara 1900 dan ' . date('Y') . '.';
    if (!in_array($input['kategori'], KATEGORI, true)) $error['kategori'] = 'Pilih salah satu kategori.';

    if (!$error) {
        $_SESSION['buku'][id_baru($_SESSION['buku'])] = ['tahun' => $tahun, 'sampul' => $sampul] + $input;
        selesai('buku.php', 'Buku baru berhasil ditambahkan.', [
            'Judul' => $input['judul'], 'Penulis' => $input['penulis'],
            'Tahun' => $tahun, 'Kategori' => $input['kategori'],
            'Sampul' => $sampul ? 'foto diunggah' : 'dibuat otomatis',
        ]);
    }
}

// ===== Proses GET: pencarian =====
$cari = ambil($_GET, 'cari');
$hasil = array_filter($_SESSION['buku'], fn($b) =>
    $cari === '' || stripos($b['judul'] . ' ' . $b['penulis'] . ' ' . $b['kategori'], $cari) !== false);

require 'includes/header.php';
?>
<div class="dua-kolom">
  <section class="jendela">
    <h1>Tambah Buku</h1>
    <form class="isi" method="post" action="buku.php" enctype="multipart/form-data" data-validasi novalidate>
      <label for="judul">Judul buku</label>
      <input id="judul" name="judul" required minlength="3" maxlength="100" value="<?= e($input['judul']) ?>">
      <small class="error"><?= e($error['judul'] ?? '') ?></small>

      <label for="penulis">Penulis</label>
      <input id="penulis" name="penulis" required minlength="3" maxlength="60" value="<?= e($input['penulis']) ?>">
      <small class="error"><?= e($error['penulis'] ?? '') ?></small>

      <label for="tahun">Tahun terbit</label>
      <input id="tahun" name="tahun" type="number" required min="1900" max="<?= date('Y') ?>" value="<?= e($input['tahun']) ?>">
      <small class="error"><?= e($error['tahun'] ?? '') ?></small>

      <label for="kategori">Kategori</label>
      <select id="kategori" name="kategori" required>
        <option value="">Pilih kategori</option>
        <?php foreach (KATEGORI as $k): ?>
          <option <?= $input['kategori'] === $k ? 'selected' : '' ?>><?= e($k) ?></option>
        <?php endforeach; ?>
      </select>
      <small class="error"><?= e($error['kategori'] ?? '') ?></small>

      <label for="sampul">Sampul buku (opsional)</label>
      <input id="sampul" name="sampul" type="file" accept="image/jpeg,image/png,image/webp">
      <small class="error"><?= e($error['sampul'] ?? '') ?></small>
      <p class="petunjuk">JPG/PNG/WEBP, maks. 1 MB. Kosongkan untuk sampul otomatis.</p>
      <img id="pratinjau" class="pratinjau" alt="Pratinjau sampul" hidden>

      <button class="tombol">Simpan Buku</button>
    </form>
  </section>

  <section aria-labelledby="judul-daftar">
    <h2 id="judul-daftar" class="subjudul">Koleksi Buku</h2>
    <form class="pencarian" method="get" action="buku.php" role="search">
      <input type="search" name="cari" id="cari" placeholder="Cari judul atau penulis" aria-label="Cari buku" value="<?= e($cari) ?>">
      <button class="tombol">Cari</button>
    </form>
    <?php if ($cari !== ''): ?>
      <p class="info">Hasil untuk "<?= e($cari) ?>": <?= count($hasil) ?> buku. <a href="buku.php">reset</a></p>
    <?php endif; ?>

    <div class="grid">
      <?php foreach ($hasil as $id => $b): $dipinjam = sedang_dipinjam($id); ?>
        <article class="kartu" data-item>
          <?php if (!empty($b['sampul'])): ?>
            <img class="sampul" src="<?= e($b['sampul']) ?>" alt="Sampul buku <?= e($b['judul']) ?>">
          <?php else: ?>
            <div class="sampul otomatis warna-<?= $id % 6 ?>" aria-hidden="true">
              <span class="sampul-judul"><?= e($b['judul']) ?></span>
              <span class="sampul-penulis"><?= e($b['penulis']) ?></span>
            </div>
          <?php endif; ?>
          <span class="stiker <?= $dipinjam ? 'merah' : '' ?>"><?= $dipinjam ? 'Dipinjam' : 'Tersedia' ?></span>
          <h3><?= e($b['judul']) ?></h3>
          <p><?= e($b['penulis']) ?> · <?= e($b['tahun']) ?></p>
          <p class="tag"><?= e($b['kategori']) ?></p>
          <form method="post" action="buku.php" data-konfirmasi="Yakin mau hapus buku ini?">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button class="tombol kecil">Hapus</button>
          </form>
        </article>
      <?php endforeach; ?>
    </div>
    <p id="kosong" class="info" <?= $hasil ? 'hidden' : '' ?>>Buku tidak ditemukan. Coba kata kunci lain.</p>
  </section>
</div>
<?php require 'includes/footer.php'; ?>

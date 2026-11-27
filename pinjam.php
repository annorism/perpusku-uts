<?php
require 'includes/data.php';
$judulHalaman = 'Peminjaman';
$error = [];
$input = ['id_anggota' => '', 'id_buku' => '', 'lama' => '7'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (ambil($_POST, 'aksi') === 'kembali') {
        $id = (int) ambil($_POST, 'id');
        if (isset($_SESSION['pinjam'][$id])) $_SESSION['pinjam'][$id]['kembali'] = true;
        selesai('pinjam.php', 'Buku telah dikembalikan. Terima kasih.');
    }

    foreach ($input as $kunci => $_) $input[$kunci] = ambil($_POST, $kunci);
    $idAnggota = (int) $input['id_anggota'];
    $idBuku = (int) $input['id_buku'];
    $lama = (int) $input['lama'];

    if (!isset($_SESSION['anggota'][$idAnggota])) $error['id_anggota'] = 'Silakan pilih anggota.';
    if (!isset($_SESSION['buku'][$idBuku]) || sedang_dipinjam($idBuku)) $error['id_buku'] = 'Pilih buku yang tersedia.';
    if ($lama < 1 || $lama > 14) $error['lama'] = 'Lama pinjam 1-14 hari.';

    if (!$error) {
        $tempo = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->modify("+$lama days")->format('d-m-Y');
        $nama = $_SESSION['anggota'][$idAnggota]['nama'];
        $judul = $_SESSION['buku'][$idBuku]['judul'];
        // Nama & judul ikut disimpan supaya riwayat tetap ada walau buku dihapus
        $_SESSION['pinjam'][id_baru($_SESSION['pinjam'])] = [
            'id_buku' => $idBuku, 'nama' => $nama, 'judul' => $judul, 'tempo' => $tempo, 'kembali' => false,
        ];
        selesai('pinjam.php', 'Peminjaman berhasil. Selamat membaca.', [
            'Peminjam' => $nama, 'Buku' => $judul, 'Lama' => "$lama hari", 'Jatuh tempo' => $tempo,
        ]);
    }
}

require 'includes/header.php';
?>
<div class="dua-kolom">
  <section class="jendela">
    <h1>Form Peminjaman</h1>
    <form class="isi" method="post" action="pinjam.php" data-validasi novalidate>
      <label for="id_anggota">Anggota</label>
      <select id="id_anggota" name="id_anggota" required>
        <option value="">Pilih anggota</option>
        <?php foreach ($_SESSION['anggota'] as $id => $a): ?>
          <option value="<?= $id ?>" <?= $input['id_anggota'] === (string) $id ? 'selected' : '' ?>><?= e($a['nama']) ?></option>
        <?php endforeach; ?>
      </select>
      <small class="error"><?= e($error['id_anggota'] ?? '') ?></small>

      <label for="id_buku">Buku</label>
      <select id="id_buku" name="id_buku" required>
        <option value="">Pilih buku</option>
        <?php foreach ($_SESSION['buku'] as $id => $b): if (sedang_dipinjam($id)) continue; ?>
          <option value="<?= $id ?>" <?= $input['id_buku'] === (string) $id ? 'selected' : '' ?>><?= e($b['judul']) ?></option>
        <?php endforeach; ?>
      </select>
      <small class="error"><?= e($error['id_buku'] ?? '') ?></small>

      <label for="lama">Lama pinjam (hari)</label>
      <input id="lama" name="lama" type="number" required min="1" max="14" value="<?= e($input['lama']) ?>">
      <small class="error"><?= e($error['lama'] ?? '') ?></small>

      <button class="tombol">Pinjam Buku</button>
    </form>
  </section>

  <section aria-labelledby="judul-riwayat">
    <h2 id="judul-riwayat" class="subjudul">Riwayat Peminjaman</h2>
    <?php if (!$_SESSION['pinjam']): ?>
      <p class="info">Belum ada riwayat peminjaman.</p>
    <?php endif; ?>
    <div class="grid">
      <?php foreach (array_reverse($_SESSION['pinjam'], true) as $id => $p): ?>
        <article class="kartu">
          <span class="stiker <?= $p['kembali'] ? '' : 'merah' ?>"><?= $p['kembali'] ? 'Sudah kembali' : 'Dipinjam' ?></span>
          <h3><?= e($p['judul']) ?></h3>
          <p>Peminjam: <?= e($p['nama']) ?></p>
          <p>Jatuh tempo: <?= e($p['tempo']) ?></p>
          <?php if (!$p['kembali']): ?>
            <form method="post" action="pinjam.php">
              <input type="hidden" name="aksi" value="kembali">
              <input type="hidden" name="id" value="<?= $id ?>">
              <button class="tombol kecil">Kembalikan</button>
            </form>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
</div>
<?php require 'includes/footer.php'; ?>

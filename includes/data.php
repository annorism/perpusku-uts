<?php
// Data disimpan di SESSION (tanpa database), jadi aman untuk latihan.
session_start();

if (!isset($_SESSION['buku'])) {
    // Data contoh saat pertama kali dibuka
    $_SESSION['buku'] = [
        1 => ['judul' => 'Laskar Pelangi',  'penulis' => 'Andrea Hirata',          'tahun' => 2005, 'kategori' => 'Novel'],
        2 => ['judul' => 'Bumi Manusia',    'penulis' => 'Pramoedya Ananta Toer',  'tahun' => 1980, 'kategori' => 'Sejarah'],
        3 => ['judul' => 'Filosofi Teras',  'penulis' => 'Henry Manampiring',      'tahun' => 2018, 'kategori' => 'Pengembangan Diri'],
    ];
    $_SESSION['anggota'] = [
        1 => ['nama' => 'Nadia Putri', 'email' => 'nadia@example.com', 'telepon' => '081200000001'],
        2 => ['nama' => 'Raka Pratama', 'email' => 'raka@example.com', 'telepon' => '081200000002'],
    ];
    $_SESSION['pinjam'] = [];
}

const KATEGORI = ['Novel', 'Sejarah', 'Teknologi', 'Pengembangan Diri', 'Komik'];

// Mencegah XSS saat menampilkan data
function e($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

// Mengambil input teks dengan aman
function ambil(array $sumber, string $kunci): string {
    return isset($sumber[$kunci]) && is_string($sumber[$kunci]) ? trim($sumber[$kunci]) : '';
}

function id_baru(array $data): int {
    return $data ? max(array_keys($data)) + 1 : 1;
}

const MAKS_SAMPUL = 1048576; // 1 MB

// Memeriksa foto sampul. Hasil: [data gambar, pesan error].
// Foto disimpan sebagai teks base64 di session, jadi tidak perlu folder upload.
function proses_sampul(?array $file): array {
    if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return ['', ''];
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) return ['', 'Ukuran foto maksimal 1 MB.'];
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) return ['', 'Foto gagal diunggah, coba lagi.'];
    if ($file['size'] > MAKS_SAMPUL) return ['', 'Ukuran foto maksimal 1 MB.'];

    // Cek isi file, bukan hanya nama atau ekstensinya
    $info = @getimagesize($file['tmp_name']);
    if (!$info || !in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)) {
        return ['', 'Sampul harus foto JPG, PNG, atau WEBP.'];
    }
    return ['data:' . $info['mime'] . ';base64,' . base64_encode(file_get_contents($file['tmp_name'])), ''];
}

// Cek apakah buku sedang dipinjam
function sedang_dipinjam(int $idBuku): bool {
    foreach ($_SESSION['pinjam'] as $p) {
        if ($p['id_buku'] === $idBuku && !$p['kembali']) return true;
    }
    return false;
}

// Pesan satu kali tampil, lalu redirect (pola Post/Redirect/Get)
function selesai(string $halaman, string $pesan, array $detail = []): void {
    $_SESSION['flash'] = ['pesan' => $pesan, 'detail' => $detail];
    header("Location: $halaman");
    exit;
}

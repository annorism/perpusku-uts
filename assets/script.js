// ===== 1. Toggle tema siang / malam =====
const tombolTema = document.getElementById('tombol-tema');

function pakaiTema(malam) {
  document.body.classList.toggle('malam', malam);
  tombolTema.textContent = malam ? 'Mode terang' : 'Mode gelap';
  tombolTema.setAttribute('aria-pressed', malam);
}

let temaTersimpan = null;
try { temaTersimpan = localStorage.getItem('tema'); } catch (e) {}
pakaiTema(temaTersimpan === 'malam');

tombolTema.addEventListener('click', () => {
  const malam = !document.body.classList.contains('malam');
  pakaiTema(malam);
  try { localStorage.setItem('tema', malam ? 'malam' : 'siang'); } catch (e) {}
});

// ===== 2. Validasi form real-time =====
function pesanError(input) {
  const v = input.validity;
  const nilai = input.value.trim();
  if (input.type === 'file') {
    const file = input.files[0];
    if (!file) return '';
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) return 'Sampul harus foto JPG, PNG, atau WEBP.';
    return file.size > 1048576 ? 'Ukuran foto maksimal 1 MB.' : '';
  }
  if (v.badInput) return 'Isi dengan angka.';
  if (input.required && nilai === '') return 'Bagian ini wajib diisi.';
  if (input.minLength > 0 && nilai.length < input.minLength) return `Minimal ${input.minLength} karakter.`;
  if (v.typeMismatch || (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(nilai))) return 'Format email belum benar.';
  if (v.patternMismatch) return input.dataset.pesan || 'Format belum sesuai.';
  if (v.rangeUnderflow || v.rangeOverflow) return `Isi antara ${input.min} dan ${input.max}.`;
  if (v.stepMismatch) return 'Harus angka bulat.';
  return '';
}

function cek(input) {
  const pesan = pesanError(input);
  input.nextElementSibling.textContent = pesan;
  input.classList.toggle('salah', pesan !== '');
  input.classList.toggle('benar', pesan === '');
  input.setAttribute('aria-invalid', pesan !== '');
  return pesan === '';
}

document.querySelectorAll('form[data-validasi]').forEach(form => {
  const isian = form.querySelectorAll('input:not([type="hidden"]), select');
  isian.forEach(input => input.addEventListener('input', () => cek(input)));

  form.addEventListener('submit', event => {
    const salah = [...isian].filter(input => !cek(input));
    if (salah.length) {
      event.preventDefault();
      salah[0].focus();
    }
  });
});

// ===== 3. Filter buku langsung saat mengetik (manipulasi DOM) =====
const kolomCari = document.getElementById('cari');
// Hanya aktif jika halaman menampilkan semua buku (bukan hasil pencarian GET)
if (kolomCari && kolomCari.value === '') {
  kolomCari.addEventListener('input', () => {
    const kata = kolomCari.value.toLowerCase().trim();
    let jumlah = 0;
    document.querySelectorAll('[data-item]').forEach(kartu => {
      const teks = [...kartu.querySelectorAll('h3, p')].map(el => el.textContent).join(' ').toLowerCase();
      kartu.hidden = !teks.includes(kata);
      if (!kartu.hidden) jumlah++;
    });
    document.getElementById('kosong').hidden = jumlah > 0;
  });
}

// ===== 4. Konfirmasi sebelum hapus =====
document.querySelectorAll('form[data-konfirmasi]').forEach(form => {
  form.addEventListener('submit', event => {
    if (!confirm(form.dataset.konfirmasi)) event.preventDefault();
  });
});

// ===== 5. Pratinjau sampul sebelum disimpan =====
const inputSampul = document.getElementById('sampul');
if (inputSampul) {
  const pratinjau = document.getElementById('pratinjau');
  inputSampul.addEventListener('change', () => {
    const sah = cek(inputSampul) && inputSampul.files.length > 0;
    if (pratinjau.src) URL.revokeObjectURL(pratinjau.src);
    if (sah) pratinjau.src = URL.createObjectURL(inputSampul.files[0]);
    pratinjau.hidden = !sah;
  });
}

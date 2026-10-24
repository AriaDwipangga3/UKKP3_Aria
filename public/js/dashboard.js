/**
 * dashboard.js: script kecil untuk halaman setelah login.
 *
 * Fungsi: menyembunyikan otomatis kotak pesan sukses (elemen ber-atribut data-flash)
 * setelah 5 detik, supaya tampilan tidak penuh pesan lama.
 * Pesan error (merah) sengaja dibiarkan tetap tampil sampai halaman dimuat ulang.
 */
document.addEventListener('DOMContentLoaded', function () {
  // Ambil semua kotak pesan yang boleh hilang otomatis.
  document.querySelectorAll('[data-flash]').forEach(function (box) {
    // Setelah 5 detik: pudarkan dulu (opacity 0), lalu hapus dari halaman.
    setTimeout(function () {
      box.style.opacity = '0';
      setTimeout(function () { box.remove(); }, 400);
    }, 5000);
  });
});

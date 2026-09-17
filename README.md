# Halaman SPMB — Pon.Pes & MTs Tahfidh Roudlotul Qur'an

Halaman tambahan untuk penerimaan murid baru. Terpisah dari situs profil yang
sudah ada, jadi tidak ada file situs lama yang tersentuh.

## Isi folder

```
spmb/
├── config.php              ← semua teks, tanggal, dan angka ada di sini
├── index.php               Beranda SPMB (promosi + ringkasan semua halaman)
├── gelombang.php           Jadwal tiga gelombang + perbandingan biayanya
├── alur.php                Enam langkah pendaftaran, syarat berkas, cara bayar
├── biaya.php               Rincian biaya mukim & non-mukim, beasiswa
├── includes/
│   ├── header.php          Header hijau, menu, sub-navigasi SPMB
│   └── footer.php          Ajakan daftar, kontak panitia, footer, skrip
└── assets/
    ├── css/spmb.css        Tema (warna & font dikumpulkan di bagian :root)
    └── img/                Taruh logo.png di sini
```

## Cara memasang

1. Unggah seluruh folder `spmb` ke root hosting, sejajar dengan file situs profil.
   Hasilnya halaman bisa dibuka di `https://mtstahfidhroudlotulquran.sch.id/spmb/`.
2. Salin logo dari situs profil ke `spmb/assets/img/logo.png`.
   Kalau file belum ada, header otomatis menampilkan lingkaran bertuliskan LOGO.
3. Buka `config.php`, isi data yang benar. Bagian yang wajib diganti:
   - `wa`, `wa2` — nomor WhatsApp panitia, format `62812…` tanpa tanda plus dan tanpa nol di depan
   - `email`, `alamat`, `maps`, `jam_layanan`
   - `link_formulir` — URL Google Form pendaftaran. Bila dibiarkan `'#'`,
     semua tombol "Daftar sekarang" otomatis mengarah ke WhatsApp panitia
   - `kuota`, `rombel`, `target_juz`, `rasio`
   - tanggal di `$GELOMBANG` dan angka di `$BIAYA`
4. Tambahkan menu SPMB di situs profil, misalnya:
   `<a href="/spmb/">SPMB</a>`

Syarat hosting: PHP 7.4 ke atas. Tidak perlu basis data, tidak perlu composer.

## Yang berjalan otomatis

- **Status gelombang** (Dibuka / Segera / Ditutup) dihitung dari tanggal hari ini.
  Tiap tahun ajaran baru cukup ubah tanggalnya, badge dan urutannya menyesuaikan.
- **Hitung mundur** di beranda mengarah ke tanggal tutup gelombang yang sedang berjalan.
- **Total biaya** dijumlahkan dari komponennya, jadi tidak pernah ada selisih
  antara rincian dan total.
- **Potongan gelombang** yang sedang berlaku otomatis muncul sebagai baris
  pengurang di tabel biaya.
- **Tahun di footer** memakai tahun berjalan.

## Kalau tema situs profil berubah

Semua warna dan font ada di bagian `:root` pada `assets/css/spmb.css`.
Ganti nilai `--hijau`, `--mint`, dan `--font` di sana, seluruh halaman ikut berubah.
Warna saat ini diambil dari header situs profil: `#15803d`.

## Catatan

Semua angka biaya, tanggal, nomor rekening, dan alamat masih contoh.
Cari kata `GANTI` di dalam file untuk menemukan yang belum diisi, terutama
nomor rekening di `alur.php`.

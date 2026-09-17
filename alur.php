<?php
require __DIR__ . '/config.php';

$PAGE = [
    'slug'      => 'alur',
    'judul'     => 'Alur & Tata Cara Pendaftaran',
    'deskripsi' => 'Enam langkah pendaftaran santri baru ' . $SITE['lembaga'] . ', mulai dari mengisi formulir sampai daftar ulang, lengkap dengan syarat berkas.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint-tua">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Tata cara</span>
      <h2>Alur pendaftaran</h2>
      <p>Enam langkah, kira-kira dua pekan sejak formulir masuk sampai daftar ulang selesai. Panitia mendampingi lewat WhatsApp di setiap langkah.</p>
    </div>
  </div>
</section>

<section class="blok">
  <div class="wadah">
    <ol class="alur">
      <?php foreach ($ALUR as $i => $l): ?>
        <li>
          <span class="no" aria-hidden="true"><?= $i + 1 ?></span>
          <h3><?= e($l['judul']) ?></h3>
          <p><?= e($l['isi']) ?></p>
          <span class="meta"><?= e($l['meta']) ?></span>
        </li>
      <?php endforeach; ?>
    </ol>

    <p style="text-align:center;margin-top:2.8rem">
      <a class="tbl tbl-utama" href="<?= e(link_daftar()) ?>"<?= link_daftar() === '#' ? '' : ' target="_blank" rel="noopener"' ?>>Mulai dari langkah pertama</a>
    </p>
  </div>
</section>

<!-- ============ SYARAT BERKAS ============ -->
<section class="blok blok-mint" id="syarat">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Berkas</span>
      <h2>Yang perlu disiapkan</h2>
      <p>Masukkan semua fotokopi ke dalam satu map berwarna hijau. Berkas yang belum terbit saat mendaftar, seperti ijazah, boleh menyusul sampai daftar ulang.</p>
    </div>

    <div class="kartu" style="max-width:56rem;margin-inline:auto">
      <ul class="syarat">
        <?php foreach ($SYARAT as [$nama, $ket]): ?>
          <li>
            <svg class="centang" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9 17.5 20 6.5"/></svg>
            <span>
              <b><?= e($nama) ?></b>
              <?php if ($ket): ?><small><?= e($ket) ?></small><?php endif; ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<!-- ============ TES SELEKSI ============ -->
<section class="blok">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Tes seleksi</span>
      <h2>Apa yang diujikan</h2>
      <p>Tes dipakai untuk menempatkan santri di kelas yang sesuai, bukan untuk menggugurkan. Tidak perlu bimbingan belajar khusus.</p>
    </div>

    <div class="grid-2" style="max-width:56rem;margin-inline:auto">
      <div class="kartu">
        <h3 style="font-size:1.2rem">Membaca Al-Qur'an</h3>
        <p style="margin-top:.6rem">Calon santri membaca beberapa ayat yang ditunjuk penguji. Yang dinilai adalah kelancaran dan makhraj. Yang belum lancar masuk kelas tahsin dasar di semester pertama.</p>
      </div>
      <div class="kartu">
        <h3 style="font-size:1.2rem">Tes tulis</h3>
        <p style="margin-top:.6rem">Matematika dan Bahasa Indonesia setingkat kelas 6 SD, sekitar 40 soal pilihan ganda. Waktu pengerjaan 60 menit.</p>
      </div>
      <div class="kartu">
        <h3 style="font-size:1.2rem">Wawancara</h3>
        <p style="margin-top:.6rem">Calon santri ditanya kesiapan mondok dan motivasinya. Wali ditanya harapan dan kondisi keluarga. Berlangsung sekitar 20 menit dan wali wajib hadir.</p>
      </div>
      <div class="kartu">
        <h3 style="font-size:1.2rem">Jadwal dan tempat</h3>
        <p style="margin-top:.6rem">Setiap Sabtu pukul 08.00 WIB di kantor panitia. Pilih tanggal tes saat mengisi formulir, atau konfirmasi ulang ke panitia paling lambat tiga hari sebelumnya.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ CARA BAYAR ============ -->
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Pembayaran</span>
      <h2>Cara membayar</h2>
      <p>Setiap pembayaran dikonfirmasi ke WhatsApp panitia agar tercatat, lalu kuitansi resmi diterbitkan bendahara.</p>
    </div>

    <div class="grid-2" style="max-width:52rem;margin-inline:auto">
      <div class="kartu">
        <h3 style="font-size:1.2rem">Transfer bank</h3>
        <!-- GANTI: nomor rekening resmi lembaga -->
        <p style="margin-top:.6rem">
          Bank Syariah Indonesia<br>
          <strong style="color:var(--judul);font-size:1.1rem">1234567890</strong><br>
          a.n. <?= e($SITE['yayasan']) ?>
        </p>
        <p style="margin-top:.8rem;font-size:.9rem;color:var(--redup)">Kirim bukti transfer ke WhatsApp panitia, sertakan nama calon santri.</p>
      </div>
      <div class="kartu">
        <h3 style="font-size:1.2rem">Tunai di kantor</h3>
        <p style="margin-top:.6rem"><?= e($SITE['alamat']) ?></p>
        <p style="margin-top:.8rem;font-size:.9rem;color:var(--redup)">Jam layanan <?= e($SITE['jam_layanan']) ?>. Kuitansi diberikan saat itu juga.</p>
      </div>
    </div>

    <p class="catatan-biaya">
      Panitia tidak pernah meminta pembayaran ke rekening pribadi. Bila ragu, konfirmasi dulu ke
      <a href="<?= e(wa('Assalamualaikum, saya ingin memastikan nomor rekening resmi untuk pembayaran SPMB.')) ?>" target="_blank" rel="noopener" style="color:var(--hijau);font-weight:700">WhatsApp panitia</a>.
    </p>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

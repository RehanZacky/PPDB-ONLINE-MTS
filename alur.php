<?php
require __DIR__ . '/config.php';

$PAGE = [
    'slug'      => 'alur',
    'judul'     => 'Alur & Tata Cara Pendaftaran',
    'deskripsi' => 'Alur pendaftaran santri baru ' . $SITE['lembaga'] . ', mulai dari mengisi formulir sampai daftar ulang, lengkap dengan syarat berkas.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint-tua alur-hero">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Tata cara</span>
      <h2>Alur pendaftaran</h2>
      <p>Proses pendaftaran berlangsung kira-kira dua pekan sejak formulir masuk sampai daftar ulang selesai. Panitia mendampingi lewat WhatsApp di setiap langkah.</p>
    </div>
  </div>
</section>

<section class="blok">
  <div class="wadah">
    <figure class="gambar-alur">
      <img src="assets/img/Alur.png" alt="Infografik alur pendaftaran calon santri dan siswa baru">
    </figure>

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

<!-- ============ CARA BAYAR ============ -->
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Pembayaran</span>
      <h2>Cara membayar</h2>
      <p>Setiap pembayaran dikonfirmasi ke WhatsApp panitia agar tercatat, lalu kuitansi resmi diterbitkan bendahara.</p>
    </div>

    <div class="gambar-pembayaran" aria-label="Pilihan pembayaran online, mobile banking, dan pembayaran offline">
      <div class="kolom-gambar-pembayaran">
        <img src="assets/img/Bayaronline.png" alt="Panduan pembayaran online">
        <img src="assets/img/Metodebayaroffline.png" alt="Metode pembayaran offline">
      </div>
      <div class="kolom-gambar-pembayaran">
        <img src="assets/img/Mobilebanking.png" alt="Panduan pembayaran melalui mobile banking">
        <img src="assets/img/Tatacaraoffline.png" alt="Tata cara pembayaran offline">
      </div>
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
        <p><a href="<?= e($SITE['maps']) ?>" target="_blank" rel="noopener" style="color:var(--hijau);font-weight:700">Buka di Google Maps</a></p>
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

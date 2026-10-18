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

<?php include __DIR__ . '/includes/footer.php'; ?>

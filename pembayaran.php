<?php
require __DIR__ . '/config.php';

$PAGE = [
    'slug'      => 'pembayaran',
    'judul'     => 'Cara Pembayaran',
    'deskripsi' => 'Panduan pembayaran biaya SPMB secara transfer bank maupun tunai di kantor panitia ' . $SITE['lembaga'] . '.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint-tua pembayaran-hero">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Pembayaran</span>
      <h2>Cara membayar biaya SPMB</h2>
      <p>Setiap pembayaran dikonfirmasi ke WhatsApp panitia agar tercatat, lalu kuitansi resmi diterbitkan bendahara.</p>
    </div>
  </div>
</section>

<section class="blok blok-mint">
  <div class="wadah">
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
        <p style="margin-top:.6rem">
          <?php foreach ($SITE['rekening_options'] as $account): ?>
            <?= e((string) $account['bank']) ?><br>
            <strong style="color:var(--judul);font-size:1.1rem"><?= e((string) $account['nomor']) ?></strong><br>
            a.n. <?= e((string) $account['nama']) ?><br><br>
          <?php endforeach; ?>
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

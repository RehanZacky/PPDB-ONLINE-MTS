<?php
require __DIR__ . '/config.php';

$persyaratan = [
  'Fotokopi KTP ayah dan ibu',
  'Fotokopi KK',
  'Fotokopi akta kelahiran',
  'Foto santri 3x4 (2 lembar)',
  'Print out NISN',
  'Surat keterangan lulus/ijazah',
  'Bisa membaca Al-Quran dengan baik dan benar',
  'Mengisi persetujuan siswa non-mukim',
  'Menerima murid pindahan (mutasi)',
];

$rincianBiaya = [
  ['Formulir', 0, 'Gratis apabila sudah mem-follow akun media sosial'],
  ['Kegiatan orientasi', 100000, ''],
  ['Uang gedung', 500000, ''],
  ['SPP', 200000, ''],
  ['Seragam', 750000, 'Putra: 3 setel kain seragam, bet, peci, dan sabuk. Putri: 3 setel kain seragam, bet, dan kerudung.'],
  ['Kegiatan ODL, P5, dan lain-lain', 200000, ''],
  ['Uang semester', 250000, ''],
];
$totalBiayaMts = array_sum(array_column($rincianBiaya, 1));

$PAGE = [
    'slug'      => 'biaya',
    'judul'     => 'Rincian Biaya',
    'deskripsi' => 'Rincian biaya masuk dan biaya bulanan santri mukim dan non-mukim ' . $SITE['lembaga'] . ' tahun ajaran ' . $SITE['tahun_ajaran'] . '.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint-tua biaya-hero">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Transparan</span>
      <h2>Tidak ada biaya di luar daftar ini</h2>
      <p>Angka berikut berlaku untuk tahun ajaran <?= e($SITE['tahun_ajaran']) ?> dan belum dikurangi potongan gelombang. Uang saku santri diatur terpisah oleh wali.</p>
    </div>
  </div>
</section>

<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Persyaratan SPMB</span>
      <h2>Persyaratan SPMB MTs</h2>
    </div>

    <div class="kartu" style="max-width:48rem;margin-inline:auto">
      <ul class="syarat" style="grid-template-columns:1fr">
        <?php foreach ($persyaratan as $nomor => $syarat): ?>
          <li>
            <svg class="centang" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9 17.5 20 6.5"/></svg>
            <span><b style="font-weight:600;color:var(--teks)"><?= $nomor + 1 ?>. <?= e($syarat) ?></b></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="blok">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Rincian Biaya MTs</span>
      <h2>Rincian Biaya MTs</h2>
    </div>

    <div class="bungkus-tabel" style="max-width:52rem;margin-inline:auto">
      <div class="geser">
        <table>
          <caption>Rincian biaya masuk MTs</caption>
          <thead>
            <tr><th scope="col">Komponen</th><th scope="col">Nominal</th></tr>
          </thead>
          <tbody>
            <?php foreach ($rincianBiaya as [$nama, $nilai, $catatan]): ?>
              <tr>
                <td><?= e($nama) ?><?php if ($catatan): ?><small><?= e($catatan) ?></small><?php endif; ?></td>
                <td><?= $nilai === 0 ? 'Gratis' : e(rp($nilai)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr><td>Total</td><td><?= e(rp($totalBiayaMts)) ?></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

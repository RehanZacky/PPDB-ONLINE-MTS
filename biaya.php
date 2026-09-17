<?php
require __DIR__ . '/config.php';

$aktif = gelombang_kini($GELOMBANG);
$potongan = ($aktif && status_gel($aktif) === 'buka') ? $aktif['potongan'] : 0;

$PAGE = [
    'slug'      => 'biaya',
    'judul'     => 'Rincian Biaya',
    'deskripsi' => 'Rincian biaya masuk dan biaya bulanan santri mukim dan non-mukim ' . $SITE['lembaga'] . ' tahun ajaran ' . $SITE['tahun_ajaran'] . '.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint-tua">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Transparan</span>
      <h2>Tidak ada biaya di luar daftar ini</h2>
      <p>Angka berikut berlaku untuk tahun ajaran <?= e($SITE['tahun_ajaran']) ?> dan belum dikurangi potongan gelombang. Uang saku santri diatur terpisah oleh wali.</p>
    </div>
  </div>
</section>

<section class="blok">
  <div class="wadah">

    <div class="tab-tombol" role="tablist" aria-label="Pilih jalur santri">
      <button id="tab-mukim" role="tab" aria-selected="true" aria-controls="panel-mukim">Santri mukim</button>
      <button id="tab-nonmukim" role="tab" aria-selected="false" aria-controls="panel-nonmukim" tabindex="-1">Santri non-mukim</button>
    </div>

    <?php foreach (['mukim', 'nonmukim'] as $kunci): ?>
      <?php
      $data     = $BIAYA[$kunci];
      $totalAwal = total_biaya($data['awal']);
      $totalBulan = total_biaya($data['bulanan']);
      ?>
      <div class="panel grid-2" id="panel-<?= $kunci ?>" role="tabpanel" aria-labelledby="tab-<?= $kunci ?>"<?= $kunci === 'nonmukim' ? ' hidden' : '' ?>>

        <div class="bungkus-tabel">
          <div class="geser">
            <table>
              <caption>
                Dibayar satu kali saat masuk
                <small><?= e($data['label']) ?> — <?= e($data['ket']) ?></small>
              </caption>
              <thead>
                <tr><th scope="col">Komponen</th><th scope="col">Nominal</th></tr>
              </thead>
              <tbody>
                <?php foreach ($data['awal'] as [$nama, $nilai, $ket]): ?>
                  <tr>
                    <td><?= e($nama) ?><?php if ($ket): ?><small><?= e($ket) ?></small><?php endif; ?></td>
                    <td><?= e(rp($nilai)) ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if ($potongan > 0): ?>
                  <tr>
                    <td style="color:var(--hijau);font-weight:700">
                      Potongan <?= e($aktif['nama']) ?>
                      <small>otomatis dihitung saat daftar ulang</small>
                    </td>
                    <td style="color:var(--hijau);font-weight:700">&minus; <?= e(rp($potongan)) ?></td>
                  </tr>
                <?php endif; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td>Total biaya awal</td>
                  <td><?= e(rp($totalAwal - $potongan)) ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div class="bungkus-tabel">
          <div class="geser">
            <table>
              <caption>
                Dibayar setiap bulan
                <small>Jatuh tempo tanggal 10</small>
              </caption>
              <thead>
                <tr><th scope="col">Komponen</th><th scope="col">Nominal</th></tr>
              </thead>
              <tbody>
                <?php foreach ($data['bulanan'] as [$nama, $nilai, $ket]): ?>
                  <tr>
                    <td><?= e($nama) ?><?php if ($ket): ?><small><?= e($ket) ?></small><?php endif; ?></td>
                    <td><?= e(rp($nilai)) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr>
                  <td>Total per bulan</td>
                  <td><?= e(rp($totalBulan)) ?></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

      </div>
    <?php endforeach; ?>

    <?php if ($potongan > 0): ?>
      <p class="catatan-biaya">
        Total di atas sudah memakai potongan <?= e($aktif['nama']) ?> sebesar <?= e(rp($potongan)) ?>,
        yang berlaku sampai <?= e(tgl($aktif['selesai'])) ?>.
      </p>
    <?php else: ?>
      <p class="catatan-biaya">Tidak ada potongan gelombang yang sedang berjalan saat ini.</p>
    <?php endif; ?>

  </div>
</section>

<!-- ============ BEASISWA ============ -->
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Keringanan</span>
      <h2>Tidak ada anak yang kami tolak karena biaya</h2>
      <p>Ajukan keringanan sebelum daftar ulang lewat panitia. Semua pengajuan disurvei dan diputuskan bersama pengasuh.</p>
    </div>

    <div class="kartu" style="max-width:48rem;margin-inline:auto">
      <ul class="syarat" style="grid-template-columns:1fr">
        <?php foreach ($BEASISWA as $b): ?>
          <li>
            <svg class="centang" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9 17.5 20 6.5"/></svg>
            <span><b style="font-weight:600;color:var(--teks)"><?= e($b) ?></b></span>
          </li>
        <?php endforeach; ?>
      </ul>

      <p style="margin-top:1.5rem;text-align:center">
        <a class="tbl tbl-utama" href="<?= e(wa('Assalamualaikum, saya ingin menanyakan keringanan biaya SPMB.')) ?>" target="_blank" rel="noopener">Tanya keringanan biaya</a>
      </p>
    </div>
  </div>
</section>

<!-- ============ CATATAN ============ -->
<section class="blok">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Perlu diketahui</span>
      <h2>Catatan tentang pembayaran</h2>
    </div>

    <div class="grid-2" style="max-width:52rem;margin-inline:auto">
      <div class="kartu">
        <h3 style="font-size:1.2rem">Yang belum termasuk</h3>
        <p style="margin-top:.6rem">Uang saku santri, biaya wisuda tahfidh, dan biaya kegiatan luar seperti studi wisata. Semuanya diumumkan terpisah minimal satu bulan sebelumnya.</p>
      </div>
      <div class="kartu">
        <h3 style="font-size:1.2rem">Kalau mengundurkan diri</h3>
        <p style="margin-top:.6rem">Biaya pendaftaran tidak dapat dikembalikan. Komponen lain yang sudah dibayar dikembalikan penuh bila pengunduran diri diajukan sebelum tahun ajaran dimulai.</p>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

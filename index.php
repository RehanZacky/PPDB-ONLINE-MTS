<?php
require __DIR__ . '/config.php';

$aktif = gelombang_kini($GELOMBANG);
$status = $aktif ? status_gel($aktif) : null;

$PAGE = [
    'slug'      => 'index',
    'judul'     => 'SPMB Tahun Ajaran ' . $SITE['tahun_ajaran'],
    'deskripsi' => 'Penerimaan santri dan murid baru ' . $SITE['lembaga'] . ' tahun ajaran ' . $SITE['tahun_ajaran'] . '. Informasi gelombang, alur pendaftaran, syarat berkas, dan rincian biaya.',
];

include __DIR__ . '/includes/header.php';
?>

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="wadah hero-isi">

    <?php if ($aktif && $status === 'buka'): ?>
      <p class="status-pill"><span class="titik" aria-hidden="true"></span> <?= e($aktif['nama']) ?> sedang dibuka</p>
    <?php elseif ($aktif): ?>
      <p class="status-pill"><?= e($aktif['nama']) ?> dibuka <?= e(tgl($aktif['mulai'])) ?></p>
    <?php else: ?>
      <p class="status-pill">Pendaftaran tahun ini sudah ditutup</p>
    <?php endif; ?>

    <h1>
      Penerimaan Santri &amp; Murid Baru
      <small>Tahun Ajaran <?= e($SITE['tahun_ajaran']) ?></small>
    </h1>

    <p class="hero-teks">
      Madrasah Tsanawiyah berbasis tahfidh Al-Qur'an dengan asrama pesantren.
      Anak belajar kurikulum nasional,dan serta menghafal dan mengaji di sore
      serta malam hari,.
    </p>

    <div class="hero-tbl">
      <a class="tbl tbl-putih" href="<?= e(link_daftar()) ?>"<?= link_daftar() === '#' ? '' : ' target="_blank" rel="noopener"' ?>>Daftar sekarang</a>
      <a class="tbl tbl-terang" href="<?= e(wa()) ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.45 1.34 4.95L2 22l5.22-1.37a9.9 9.9 0 0 0 4.82 1.23h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2Zm5.8 14.06c-.24.68-1.42 1.31-1.95 1.36-.5.05-.98.24-3.3-.69-2.78-1.1-4.54-3.95-4.68-4.13-.13-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.25-.28.55-.35.73-.35h.52c.17 0 .4-.06.62.48.24.57.8 1.98.87 2.12.07.14.12.31.02.49-.09.18-.14.29-.28.45-.14.16-.29.35-.41.47-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.04.93 1.92 1.21 2.2 1.35.28.14.44.12.6-.07.17-.19.7-.81.88-1.09.18-.28.37-.23.62-.14.25.09 1.6.75 1.87.89.28.14.46.21.53.32.07.12.07.66-.17 1.34Z"/></svg>
        Tanya lewat WhatsApp
      </a>
    </div>
  </div>
</section>

<!-- ============ ANGKA RINGKAS ============ -->
<section class="blok">
  <div class="wadah">
    <div class="angka">
      <div>
        <span class="bulat" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 8.5 12 4l9 4.5-9 4.5-9-4.5Z"/><path d="M7 11v5c0 1.2 2.2 2.5 5 2.5s5-1.3 5-2.5v-5"/></svg></span>
        <b><?= (int) $SITE['kuota'] ?></b>
        <span>Kuota santri baru</span>
      </div>
      <div>
        <span class="bulat" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg></span>
        <b><?= (int) $SITE['rombel'] ?></b>
        <span>Rombongan belajar</span>
      </div>
      <div>
        <span class="bulat" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H19v15H6.5A2.5 2.5 0 0 0 4 20.5Z"/><path d="M8 7.5h7M8 11h5"/></svg></span>
        <b><?= (int) $SITE['target_juz'] ?> juz</b>
        <span>Target hafalan tiga tahun</span>
      </div>
      <div>
        <span class="bulat" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.2 2.7-5.2 6-5.2s6 2 6 5.2"/><path d="M16.5 11.5a3 3 0 1 0-1.6-5.5M18 20c0-2.2-.9-3.9-2.4-4.8"/></svg></span>
        <b><?= e($SITE['rasio']) ?></b>
        <span>Rasio ustaz &amp; santri</span>
      </div>
    </div>
  </div>
</section>

<!-- ============ DUA JALUR ============ -->
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Pilih jalur</span>
      <h2>Dua jalur santri, satu madrasah</h2>
      <p>Keduanya mengikuti kelas MTs yang sama dan mendapat ijazah negeri yang sama. Yang membedakan adalah tempat tinggal dan jam pembinaan.</p>
    </div>

    <div class="grid-2">
      <?php foreach ($PROGRAM as $i => $pr): ?>
        <?php $kunci = $i === 0 ? 'mukim' : 'nonmukim'; ?>
        <article class="kartu program<?= $pr['unggul'] ? ' kartu-sorot' : '' ?>">
          <?php if ($pr['unggul']): ?><span class="pita">Paling banyak dipilih</span><?php endif; ?>
          <h3><?= e($pr['nama']) ?></h3>
          <p class="ket"><?= e($pr['ket']) ?></p>
          <p><?= e($pr['isi']) ?></p>
          <ul>
            <?php foreach ($pr['poin'] as $poin): ?>
              <li>
                <svg class="centang" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 12.5 9 17.5 20 6.5"/></svg>
                <?= e($poin) ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <p class="kaki">
            Biaya bulanan <b><?= e(rp(total_biaya($BIAYA[$kunci]['bulanan']))) ?></b><br>
            Biaya awal masuk <?= e(rp(total_biaya($BIAYA[$kunci]['awal']))) ?>
          </p>
        </article>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:2rem">
      <a class="tbl tbl-garis" href="biaya.php">Lihat rincian biaya lengkap</a>
    </p>
  </div>
</section>

<!-- ============ RINGKASAN GELOMBANG ============ -->
<section class="blok">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Tiga gelombang</span>
      <h2>Makin awal mendaftar, makin ringan biayanya</h2>
      <p>Potongan berlaku untuk infak pengembangan dan langsung dihitung saat daftar ulang.</p>
    </div>

    <div class="gelombang">
      <?php foreach ($GELOMBANG as $i => $g): ?>
        <?php
        $st = status_gel($g);
        $kelas = ['buka' => 'gel-buka', 'akan' => 'gel-akan', 'tutup' => 'gel-tutup'][$st];
        $teks  = ['buka' => 'Dibuka', 'akan' => 'Segera', 'tutup' => 'Ditutup'][$st];
        ?>
        <article class="kartu gel <?= $kelas ?><?= $st === 'buka' ? ' kartu-sorot' : '' ?>">
          <span class="badge badge-<?= $st ?>"><?= e($teks) ?></span>
          <span class="no" aria-hidden="true"><?= $i + 1 ?></span>
          <h3><?= e($g['nama']) ?></h3>
          <p class="tanggal"><?= e(rentang($g['mulai'], $g['selesai'])) ?></p>
          <div class="potong">
            <?php if ($g['potongan'] > 0): ?>
              <b><?= e(rp($g['potongan'])) ?></b>
              <span>potongan infak pengembangan</span>
            <?php else: ?>
              <b style="font-size:1.15rem;color:var(--redup)">Tanpa potongan</b>
              <span>hanya bila kuota masih tersisa</span>
            <?php endif; ?>
          </div>
          <p class="catatan"><?= e($g['catatan']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:2rem">
      <a class="tbl tbl-garis" href="gelombang.php">Lihat jadwal lengkap</a>
    </p>
  </div>
</section>

<!-- ============ RINGKASAN ALUR ============ -->
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Enam langkah</span>
      <h2>Dari isi formulir sampai daftar ulang</h2>
      <p>Seluruh proses memakan waktu sekitar dua pekan. Berikut tiga langkah pertamanya.</p>
    </div>

    <ol class="alur">
      <?php foreach (array_slice($ALUR, 0, 3) as $i => $l): ?>
        <li>
          <span class="no" aria-hidden="true"><?= $i + 1 ?></span>
          <h3><?= e($l['judul']) ?></h3>
          <p><?= e($l['isi']) ?></p>
          <span class="meta"><?= e($l['meta']) ?></span>
        </li>
      <?php endforeach; ?>
    </ol>

    <p style="text-align:center;margin-top:2.4rem">
      <a class="tbl tbl-garis" href="alur.php">Lihat enam langkah dan syarat berkas</a>
    </p>
  </div>
</section>

<!-- ============ TANYA JAWAB ============ -->
<section class="blok">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Sering ditanyakan</span>
      <h2>Tanya jawab</h2>
    </div>

    <div class="faq">
      <?php foreach ($FAQ as $i => [$tanya, $jawab]): ?>
        <details<?= $i === 0 ? ' open' : '' ?>>
          <summary><?= e($tanya) ?></summary>
          <p><?= e($jawab) ?></p>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

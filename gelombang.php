<?php
require __DIR__ . '/config.php';

$aktif = gelombang_kini($GELOMBANG);
$status = $aktif ? status_gel($aktif) : null;

$PAGE = [
    'slug'      => 'gelombang',
    'judul'     => 'Gelombang Pendaftaran',
    'deskripsi' => 'Jadwal tiga gelombang pendaftaran santri baru ' . $SITE['lembaga'] . ' tahun ajaran ' . $SITE['tahun_ajaran'] . ' beserta potongan biayanya.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint-tua">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Jadwal</span>
      <h2>Gelombang pendaftaran <?= e($SITE['tahun_ajaran']) ?></h2>
      <p>Pendaftaran dibuka tiga kali. Potongan biaya mengecil di setiap gelombang berikutnya, dan gelombang bisa ditutup lebih cepat bila kuota sudah penuh.</p>
    </div>

    <?php $timer_schedule = array_map(function ($g) {
      return [
        'nama' => $g['nama'],
        'mulai' => $g['mulai'] . 'T00:00:00+07:00',
        'selesai' => $g['selesai'] . 'T23:59:59+07:00',
      ];
    }, $GELOMBANG); ?>

    <div class="kartu kartu-sorot" style="max-width:40rem;margin-inline:auto;text-align:center">
      <p class="label" data-gelombang-label style="background:var(--hijau);color:#fff">Gelombang 1 sedang dibuka</p>
      <h3 data-gelombang-deadline style="font-size:1.35rem">Ditutup <?= e(tgl($GELOMBANG[0]['selesai'])) ?></h3>
      <div class="mundur" data-gelombang-timer role="timer" style="margin-top:1.2rem">
        <div style="background:var(--mint);border-color:var(--mint)"><b data-hari style="color:var(--hijau)">--</b><span style="color:var(--teks);opacity:1">hari</span></div>
        <div style="background:var(--mint);border-color:var(--mint)"><b data-jam style="color:var(--hijau)">--</b><span style="color:var(--teks);opacity:1">jam</span></div>
        <div style="background:var(--mint);border-color:var(--mint)"><b data-menit style="color:var(--hijau)">--</b><span style="color:var(--teks);opacity:1">menit</span></div>
        <div style="background:var(--mint);border-color:var(--mint)"><b data-detik style="color:var(--hijau)">--</b><span style="color:var(--teks);opacity:1">detik</span></div>
      </div>
      <p class="mundur-habis" data-habis hidden style="color:var(--redup)">Gelombang ini sudah ditutup.</p>
      <p style="margin-top:1.4rem">
        <a class="tbl tbl-utama" href="<?= e(link_daftar()) ?>"<?= link_daftar() === '#' ? '' : ' target="_blank" rel="noopener"' ?>>Daftar sekarang</a>
      </p>
    </div>
  </div>
</section>

<section class="blok">
  <div class="wadah">
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
  </div>
</section>

<!-- perbandingan biaya awal tiap gelombang -->
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Pengaruh ke biaya</span>
      <h2>Berapa yang dibayar di tiap gelombang</h2>
      <p>Angka di bawah adalah total biaya awal masuk setelah dikurangi potongan gelombang. Biaya bulanan tidak terpengaruh.</p>
    </div>

    <div class="bungkus-tabel" style="max-width:52rem;margin-inline:auto">
      <div class="geser">
        <table>
          <thead>
            <tr>
              <th scope="col">Gelombang</th>
              <th scope="col">Santri mukim</th>
              <th scope="col">Santri non-mukim</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($GELOMBANG as $g): ?>
              <?php
              $mukim = total_biaya($BIAYA['mukim']['awal']) - $g['potongan'];
              $non   = total_biaya($BIAYA['nonmukim']['awal']) - $g['potongan'];
              ?>
              <tr>
                <td>
                  <?= e($g['nama']) ?>
                  <small><?= e(rentang($g['mulai'], $g['selesai'])) ?></small>
                </td>
                <td><?= e(rp($mukim)) ?></td>
                <td><?= e(rp($non)) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <p class="catatan-biaya">
      Selisih antara Gelombang 1 dan Gelombang 3 mencapai
      <strong style="color:var(--hijau)"><?= e(rp($GELOMBANG[0]['potongan'])) ?></strong>.
      Rincian tiap komponen ada di <a href="biaya.php" style="color:var(--hijau);font-weight:700">halaman biaya</a>.
    </p>
  </div>
</section>

<!-- kuota -->
<section class="blok">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Kuota</span>
      <h2>Kenapa sebaiknya tidak menunggu</h2>
    </div>

    <div class="grid-2" style="max-width:52rem;margin-inline:auto">
      <div class="kartu">
        <h3 style="font-size:1.2rem">Kuota terbatas <?= (int) $SITE['kuota'] ?> santri</h3>
        <p style="margin-top:.6rem">Dibagi menjadi <?= (int) $SITE['rombel'] ?> rombongan belajar agar setiap santri tetap terpantau. Kami tidak menambah kuota di luar angka ini.</p>
      </div>
      <div class="kartu">
        <h3 style="font-size:1.2rem">Kamar asrama dipilih lebih dulu</h3>
        <p style="margin-top:.6rem">Pendaftar gelombang awal mendapat prioritas penempatan kamar dan pilihan kelas tahfidh. Tahun lalu kuota santri mukim habis di pertengahan Gelombang 2.</p>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  'use strict';

  var timer = document.querySelector('[data-gelombang-timer]');
  if (!timer) return;

  var schedule = <?php echo json_encode($timer_schedule, JSON_UNESCAPED_SLASHES); ?>;
  var labelEl = document.querySelector('[data-gelombang-label]');
  var deadlineEl = document.querySelector('[data-gelombang-deadline]');
  var doneEl = document.querySelector('[data-habis]');

  function pad(value) {
    return String(value).padStart(2, '0');
  }

  function setValue(selector, value) {
    var node = timer.querySelector(selector);
    if (!node) return;
    node.textContent = value;
  }

  function formatDate(value) {
    return new Date(value).toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    });
  }

  function getCurrentIndex(now) {
    var chosenIndex = 0;
    var foundUpcoming = false;

    for (var i = 0; i < schedule.length; i++) {
      var start = new Date(schedule[i].mulai).getTime();
      var end = new Date(schedule[i].selesai).getTime();

      if (now >= start && now < end) {
        return i;
      }

      if (!foundUpcoming && now < start) {
        chosenIndex = i;
        foundUpcoming = true;
      }
    }

    return chosenIndex;
  }

  function updateCountdown() {
    var now = Date.now();
    var index = getCurrentIndex(now);
    var target = schedule[index] || schedule[0];
    var deadline = new Date(target.selesai).getTime();
    var remaining = Math.max(0, deadline - now);

    if (!target) {
      setValue('[data-hari]', '00');
      setValue('[data-jam]', '00');
      setValue('[data-menit]', '00');
      setValue('[data-detik]', '00');
      return;
    }

    if (remaining <= 0) {
      setValue('[data-hari]', '00');
      setValue('[data-jam]', '00');
      setValue('[data-menit]', '00');
      setValue('[data-detik]', '00');

      if (labelEl) {
        labelEl.textContent = 'Semua gelombang sudah ditutup';
      }

      if (deadlineEl) {
        deadlineEl.textContent = 'Pendaftaran telah berakhir';
      }

      if (doneEl) {
        doneEl.hidden = false;
        doneEl.textContent = 'Semua gelombang pendaftaran sudah ditutup.';
      }

      return;
    }

    var days = Math.floor(remaining / (1000 * 60 * 60 * 24));
    var hours = Math.floor((remaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((remaining % (1000 * 60)) / 1000);

    setValue('[data-hari]', pad(days));
    setValue('[data-jam]', pad(hours));
    setValue('[data-menit]', pad(minutes));
    setValue('[data-detik]', pad(seconds));

    timer.setAttribute('data-batas', target.selesai);

    if (labelEl) {
      var start = new Date(target.mulai).getTime();
      labelEl.textContent = now >= start ? (target.nama + ' sedang dibuka') : (target.nama + ' segera dibuka');
    }

    if (deadlineEl) {
      deadlineEl.textContent = 'Ditutup ' + formatDate(target.selesai);
    }

    if (doneEl) {
      doneEl.hidden = true;
    }
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

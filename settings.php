<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

function spmb_admin_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function spmb_setting_text(array $source, string $key, string $fallback = ''): string
{
    return trim((string) ($source[$key] ?? $fallback));
}

function spmb_setting_rows(array $rows, int $columns): array
{
    $result = [];
    foreach ($rows as $row) {
        if (!is_array($row)) continue;
        $values = [];
        for ($i = 0; $i < $columns; $i++) {
            $values[] = trim((string) ($row[$i] ?? ''));
        }
        if ($values[0] !== '') {
            if ($columns === 3) $values[1] = max(0, (int) preg_replace('/[^\d]/', '', $values[1]));
            $result[] = $values;
        }
    }
    return $result;
}

$settingsRoot = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$settingsFile = $settingsRoot . DIRECTORY_SEPARATOR . 'settings.json';
$message = '';
$messageError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $postedSite = is_array($_POST['site'] ?? null) ? $_POST['site'] : [];
    $newSite = [];
    foreach (['email', 'alamat', 'maps', 'jam_layanan'] as $field) {
        $newSite[$field] = spmb_setting_text($postedSite, $field, (string) ($SITE[$field] ?? ''));
    }
    $newWhatsapps = [];
    foreach (is_array($_POST['whatsapp_options'] ?? null) ? $_POST['whatsapp_options'] : [] as $whatsapp) {
        if (!is_array($whatsapp)) continue;
        $nomor = preg_replace('/\D+/', '', spmb_setting_text($whatsapp, 'nomor'));
        $tampil = spmb_setting_text($whatsapp, 'tampil');
        $nama = spmb_setting_text($whatsapp, 'nama');
        if ($nomor !== '') {
            if ($tampil === '' || wa_nomor($tampil) !== wa_nomor($nomor)) $tampil = $nomor;
            $newWhatsapps[] = ['nomor' => $nomor, 'tampil' => $tampil, 'nama' => $nama];
        }
    }
    $newAccounts = [];
    foreach (is_array($_POST['rekening_options'] ?? null) ? $_POST['rekening_options'] : [] as $account) {
        if (!is_array($account)) continue;
        $bank = spmb_setting_text($account, 'bank');
        $nomor = spmb_setting_text($account, 'nomor');
        $nama = spmb_setting_text($account, 'nama');
        if ($bank !== '' && $nomor !== '' && $nama !== '') $newAccounts[] = ['bank' => $bank, 'nomor' => $nomor, 'nama' => $nama];
    }
    if ($newWhatsapps) {
        $newSite['whatsapp_options'] = $newWhatsapps;
        $newSite['wa'] = $newWhatsapps[0]['nomor'];
        $newSite['wa_tampil'] = $newWhatsapps[0]['tampil'];
        $newSite['wa_nama'] = $newWhatsapps[0]['nama'];
        $newSite['wa2'] = $newWhatsapps[1]['nomor'] ?? $newWhatsapps[0]['nomor'];
        $newSite['wa2_tampil'] = $newWhatsapps[1]['tampil'] ?? $newWhatsapps[0]['tampil'];
        $newSite['wa2_nama'] = $newWhatsapps[1]['nama'] ?? $newWhatsapps[0]['nama'];
    }
    if ($newAccounts) {
        $newSite['rekening_options'] = $newAccounts;
        $newSite['rekening_bank'] = $newAccounts[0]['bank'];
        $newSite['rekening_nomor'] = $newAccounts[0]['nomor'];
        $newSite['rekening_nama'] = $newAccounts[0]['nama'];
    }

    $newWaves = [];
    foreach (is_array($_POST['waves'] ?? null) ? $_POST['waves'] : [] as $wave) {
        if (!is_array($wave)) continue;
        $name = spmb_setting_text($wave, 'nama');
        $start = spmb_setting_text($wave, 'mulai');
        $end = spmb_setting_text($wave, 'selesai');
        if ($name === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) continue;
        $newWaves[] = [
            'nama' => $name,
            'mulai' => $start,
            'selesai' => $end,
            'potongan' => max(0, (int) preg_replace('/[^\d]/', '', (string) ($wave['potongan'] ?? '0'))),
            'catatan' => spmb_setting_text($wave, 'catatan'),
        ];
    }

    $newCosts = [];
    $postedCosts = is_array($_POST['biaya'] ?? null) ? $_POST['biaya'] : [];
    foreach (['mukim', 'nonmukim'] as $kind) {
        $current = $BIAYA[$kind];
        $posted = is_array($postedCosts[$kind] ?? null) ? $postedCosts[$kind] : [];
        $newCosts[$kind] = [
            'label' => spmb_setting_text($posted, 'label', $current['label']),
            'ket' => spmb_setting_text($posted, 'ket', $current['ket']),
            'awal' => spmb_setting_rows(is_array($posted['awal'] ?? null) ? $posted['awal'] : [], 3),
            'bulanan' => spmb_setting_rows(is_array($posted['bulanan'] ?? null) ? $posted['bulanan'] : [], 3),
        ];
    }

    $newSyarat = [];
    foreach (is_array($_POST['syarat'] ?? null) ? $_POST['syarat'] : [] as $row) {
        if (!is_array($row)) continue;
        $name = spmb_setting_text($row, 'nama');
        if ($name !== '') $newSyarat[] = [$name, spmb_setting_text($row, 'ket')];
    }
    $newMtsRequirements = [];
    foreach (is_array($_POST['mts_requirements'] ?? null) ? $_POST['mts_requirements'] : [] as $value) {
        $value = trim((string) $value);
        if ($value !== '') $newMtsRequirements[] = $value;
    }
    $newMtsCosts = spmb_setting_rows(is_array($_POST['mts_costs'] ?? null) ? $_POST['mts_costs'] : [], 3);

    if (!$newWaves || !$newSyarat || !$newMtsRequirements || !$newMtsCosts || !$newWhatsapps || !$newAccounts) {
        $message = 'Data tidak lengkap. Isi minimal satu baris pada setiap daftar.';
        $messageError = true;
    } elseif (!is_dir($settingsRoot) && !mkdir($settingsRoot, 0755, true)) {
        $message = 'Folder penyimpanan data tidak dapat dibuat.';
        $messageError = true;
    } else {
        $settings = [
            'SITE' => array_replace($SITE, $newSite),
            'GELOMBANG' => $newWaves,
            'BIAYA' => $newCosts,
            'SYARAT' => $newSyarat,
            'PERSYARATAN_MTS' => $newMtsRequirements,
            'RINCIAN_BIAYA_MTS' => $newMtsCosts,
        ];
        $saved = file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        if ($saved === false) {
            $message = 'Data tidak dapat disimpan. Pastikan folder data bisa ditulis oleh Apache.';
            $messageError = true;
        } else {
            $message = 'Pengaturan berhasil disimpan.';
            $SITE = $settings['SITE'];
            $GELOMBANG = $settings['GELOMBANG'];
            $BIAYA = $settings['BIAYA'];
            $SYARAT = $settings['SYARAT'];
            $PERSYARATAN_MTS = $settings['PERSYARATAN_MTS'];
            $RINCIAN_BIAYA_MTS = $settings['RINCIAN_BIAYA_MTS'];
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pengaturan SPMB - <?= spmb_admin_e($SITE['singkat']) ?></title>
  <style>
    :root{--green:#15803d;--dark:#14532d;--mint:#f0fdf4;--ink:#14251d;--muted:#64748b;--line:#e2e8f0;--red:#b91c1c}*{box-sizing:border-box}body{margin:0;background:#f8fafc;color:var(--ink);font:14px/1.6 system-ui,-apple-system,"Segoe UI",sans-serif}a{text-decoration:none;color:inherit}.top{background:linear-gradient(110deg,#166534,#15803d);color:#fff;box-shadow:0 3px 14px #064e3b22}.top-in,.main{width:min(1180px,calc(100% - 32px));margin:auto}.top-in{min-height:70px;display:flex;align-items:center;gap:20px}.brand{display:flex;align-items:center;gap:10px;margin-right:auto}.mark{width:39px;height:39px;border-radius:11px;background:#fff;color:var(--green);display:grid;place-items:center;font-weight:900}.brand b{display:block;font-size:1.08rem}.brand small{color:#bbf7d0}.nav{display:flex;gap:5px;flex-wrap:wrap}.nav a{padding:9px 11px;border-radius:8px;color:#dcfce7;font-weight:700}.nav a:hover,.nav .active{background:#14532d;color:#fff}.user{color:#dcfce7}.main{padding:30px 0 55px}.heading{display:flex;align-items:end;justify-content:space-between;gap:15px;margin-bottom:21px}.heading h1{font-size:1.7rem;margin:0}.heading p{margin:5px 0 0;color:var(--muted)}.button{display:inline-block;padding:10px 14px;border:0;border-radius:9px;background:var(--green);color:#fff;font:inherit;font-weight:800;cursor:pointer}.button.light{background:#dcfce7;color:var(--dark)}.button.add-row{padding:7px 11px;margin:5px 0 8px;font-size:.85rem}.button.delete-row{padding:5px 8px;border-radius:7px;background:#fee2e2;color:var(--red);font-size:.78rem;white-space:nowrap}.button.whatsapp-link{padding:6px 9px;background:#dcfce7;color:var(--dark);font-size:.8rem;white-space:nowrap}.panel{background:#fff;border:1px solid var(--line);border-radius:15px;box-shadow:0 5px 16px #0f172a08;padding:20px}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:17px}.panel h2{font-size:1.1rem;margin:0}.meta,.help{color:var(--muted);font-size:.82rem}.settings-tabs{display:flex;gap:8px;margin-bottom:18px;border-bottom:1px solid var(--line);overflow-x:auto}.settings-tab{border:0;border-bottom:3px solid transparent;background:transparent;padding:11px 14px;color:var(--muted);font:inherit;font-weight:800;white-space:nowrap;cursor:pointer}.settings-tab:hover,.settings-tab.active{color:var(--dark);border-bottom-color:var(--green);background:var(--mint)}.setting-page[hidden]{display:none}.settings-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.option-card{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;padding:14px;border:1px solid #bbf7d0;border-radius:10px;background:var(--mint);grid-column:1/-1}.option-card .delete-row{grid-column:1/-1;justify-self:start}.field{display:flex;flex-direction:column;gap:5px}.field label,.subheading{font-weight:800}.field input,.field textarea{width:100%;padding:9px 10px;border:1px solid #cbd5e1;border-radius:8px;font:inherit}.field textarea{min-height:58px;resize:vertical}.setting-block{margin-top:22px;padding-top:18px;border-top:1px solid var(--line)}.setting-block h3{font-size:1rem;margin:0 0 10px}.setting-table{width:100%;border-collapse:collapse}.setting-table th,.setting-table td{text-align:left;padding:6px;vertical-align:top}.setting-table th{font-size:.75rem;color:var(--muted)}.setting-table input{min-width:100px}.notice{padding:11px 14px;background:#dcfce7;color:var(--dark);border-radius:9px;margin-bottom:18px}.notice.error{background:#fef2f2;color:var(--red)}.save-settings{margin-top:20px}@media(max-width:720px){.top-in{width:calc(100% - 22px)}.nav{display:none}.user{font-size:.8rem}.main{width:calc(100% - 22px);padding-top:22px}.heading{align-items:flex-start;flex-direction:column}.panel{padding:14px}.settings-grid{grid-template-columns:1fr}.option-card{grid-template-columns:1fr}.setting-table{display:block;overflow:auto;white-space:nowrap}}
  </style>
</head>
<body>
  <header class="top"><div class="top-in">
    <a class="brand" href="spmb_admin.php"><span class="mark">SP</span><span><b>SPMB Admin</b><small><?= spmb_admin_e($SITE['singkat']) ?></small></span></a>
    <nav class="nav" aria-label="Navigasi admin">
      <a href="spmb_admin.php">Data SPMB</a>
      <a class="active" href="settings.php">Pengaturan</a>
      <a href="index.php" target="_blank" rel="noopener">Beranda</a>
      <a href="gelombang.php" target="_blank" rel="noopener">Gelombang</a>
      <a href="alur.php" target="_blank" rel="noopener">Alur &amp; Syarat</a>
      <a href="biaya.php" target="_blank" rel="noopener">Biaya</a>
      <a href="pembayaran.php" target="_blank" rel="noopener">Pembayaran</a>
    </nav>
    <span class="user">Admin</span>
  </div></header>
  <main class="main">
    <div class="heading"><div><h1>Pengaturan halaman website</h1><p>Menu website sudah dipisah berdasarkan halaman agar informasi lebih ringkas.</p></div><a class="button light" href="spmb_admin.php">Kembali ke data SPMB</a></div>
    <?php if ($message !== ''): ?><div class="notice<?= $messageError ? ' error' : '' ?>"><?= spmb_admin_e($message) ?></div><?php endif; ?>
    <section class="panel">
      <div class="panel-head"><h2>Pengaturan halaman website</h2><span class="meta">Perubahan langsung tampil di halaman publik</span></div>
      <nav class="settings-tabs" aria-label="Bagian pengaturan">
        <button class="settings-tab active" type="button" data-page-tab="page-1" aria-controls="page-1" aria-selected="true">Page 1 · Gelombang, harga &amp; rekening</button>
        <button class="settings-tab" type="button" data-page-tab="page-2" aria-controls="page-2" aria-selected="false">Page 2 · Kontak &amp; pelayanan</button>
        <button class="settings-tab" type="button" data-page-tab="page-3" aria-controls="page-3" aria-selected="false">Page 3 · Persiapan, syarat &amp; biaya MTs</button>
      </nav>
      <form method="post">
        <input type="hidden" name="save_settings" value="1">
        <div class="setting-page" id="page-1">
        <div class="setting-block" style="margin-top:0;padding-top:0;border-top:0">
          <h3>Page 1 · Gelombang, harga, dan rekening</h3>
          <div style="overflow:auto"><table class="setting-table"><thead><tr><th>Nama gelombang</th><th>Mulai</th><th>Berakhir</th><th>Diskon</th><th>Catatan</th></tr></thead><tbody id="waves-list">
          <?php foreach ($GELOMBANG as $i => $wave): ?><tr><td><input required name="waves[<?= $i ?>][nama]" value="<?= spmb_admin_e($wave['nama']) ?>"></td><td><input required type="date" name="waves[<?= $i ?>][mulai]" value="<?= spmb_admin_e($wave['mulai']) ?>"></td><td><input required type="date" name="waves[<?= $i ?>][selesai]" value="<?= spmb_admin_e($wave['selesai']) ?>"></td><td><input type="number" min="0" name="waves[<?= $i ?>][potongan]" value="<?= (int) $wave['potongan'] ?>"></td><td><input name="waves[<?= $i ?>][catatan]" value="<?= spmb_admin_e($wave['catatan']) ?>"></td></tr><?php endforeach; ?>
          </tbody></table></div><button class="button add-row" type="button" data-add-row="waves-list" data-template="wave-template">+ Tambah gelombang</button>
          <p class="help">Diskon diisi dalam rupiah. Harga awal dan bulanan di bawah dapat diedit semua komponennya.</p>
          <?php foreach (['mukim' => 'Santri mukim', 'nonmukim' => 'Santri non-mukim'] as $kind => $title): ?>
            <h3><?= $title ?></h3><div class="settings-grid"><div class="field"><label>Label</label><input name="biaya[<?= $kind ?>][label]" value="<?= spmb_admin_e($BIAYA[$kind]['label']) ?>"></div><div class="field"><label>Keterangan</label><input name="biaya[<?= $kind ?>][ket]" value="<?= spmb_admin_e($BIAYA[$kind]['ket']) ?>"></div></div>
            <?php foreach (['awal' => 'Biaya awal', 'bulanan' => 'Biaya bulanan'] as $section => $sectionTitle): ?><p class="subheading"><?= $sectionTitle ?></p><div style="overflow:auto"><table class="setting-table"><thead><tr><th>Komponen</th><th>Nominal</th><th>Catatan</th></tr></thead><tbody id="costs-<?= $kind ?>-<?= $section ?>"><?php foreach ($BIAYA[$kind][$section] as $i => $cost): ?><tr><td><input required name="biaya[<?= $kind ?>][<?= $section ?>][<?= $i ?>][0]" value="<?= spmb_admin_e($cost[0]) ?>"></td><td><input required type="number" min="0" name="biaya[<?= $kind ?>][<?= $section ?>][<?= $i ?>][1]" value="<?= (int) $cost[1] ?>"></td><td><input name="biaya[<?= $kind ?>][<?= $section ?>][<?= $i ?>][2]" value="<?= spmb_admin_e($cost[2]) ?>"></td></tr><?php endforeach; ?></tbody></table></div><button class="button add-row" type="button" data-add-row="costs-<?= $kind ?>-<?= $section ?>" data-template="cost-<?= $kind ?>-<?= $section ?>-template">+ Tambah <?= strtolower($sectionTitle) ?></button><?php endforeach; ?>
          <?php endforeach; ?>
          <p class="subheading">Pilihan rekening</p><div class="settings-grid" id="rekening-list"><?php foreach ($SITE['rekening_options'] as $i => $account): ?><div class="option-card"><div class="field"><label>Nama bank</label><input required name="rekening_options[<?= $i ?>][bank]" value="<?= spmb_admin_e((string) ($account['bank'] ?? '')) ?>"></div><div class="field"><label>Nomor rekening</label><input required name="rekening_options[<?= $i ?>][nomor]" value="<?= spmb_admin_e((string) ($account['nomor'] ?? '')) ?>"></div><div class="field"><label>Nama pemilik rekening</label><input required name="rekening_options[<?= $i ?>][nama]" value="<?= spmb_admin_e((string) ($account['nama'] ?? '')) ?>"></div></div><?php endforeach; ?></div><button class="button add-row" type="button" data-add-row="rekening-list" data-template="rekening-template">+ Tambah rekening</button>
        </div>
        </div>
        <div class="setting-page" id="page-2" hidden>
        <div class="setting-block" style="margin-top:0;padding-top:0;border-top:0"><h3>Page 2 · Kontak dan pelayanan</h3><p class="subheading">Pilihan WhatsApp panitia</p><p class="help">Opsi pertama digunakan untuk tombol WhatsApp utama. Semua opsi tetap tampil sebagai kontak yang bisa dihubungi.</p><div class="settings-grid" id="whatsapp-list"><?php foreach ($SITE['whatsapp_options'] as $i => $whatsapp): ?><div class="option-card"><div class="field"><label>Nomor WhatsApp</label><input required name="whatsapp_options[<?= $i ?>][nomor]" value="<?= spmb_admin_e((string) ($whatsapp['nomor'] ?? '')) ?>" data-whatsapp-number><span class="help">Format internasional, contoh 62812...</span><a class="button whatsapp-link" href="<?= e(wa_link((string) ($whatsapp['nomor'] ?? ''))) ?>" target="_blank" rel="noopener" data-whatsapp-link>Hubungi nomor ini</a></div><div class="field"><label>Nomor yang ditampilkan</label><input required name="whatsapp_options[<?= $i ?>][tampil]" value="<?= spmb_admin_e((string) ($whatsapp['tampil'] ?? '')) ?>"></div><div class="field"><label>Nama petugas</label><input name="whatsapp_options[<?= $i ?>][nama]" value="<?= spmb_admin_e((string) ($whatsapp['nama'] ?? '')) ?>"></div></div><?php endforeach; ?></div><button class="button add-row" type="button" data-add-row="whatsapp-list" data-template="whatsapp-template">+ Tambah WhatsApp</button><div class="settings-grid"><div class="field"><label>Lokasi kantor</label><textarea required name="site[alamat]"><?= spmb_admin_e($SITE['alamat']) ?></textarea></div><div class="field"><label>Link Google Maps</label><input name="site[maps]" value="<?= spmb_admin_e($SITE['maps']) ?>"></div><div class="field"><label>Hari dan jam pelayanan</label><input required name="site[jam_layanan]" value="<?= spmb_admin_e($SITE['jam_layanan']) ?>"></div><div class="field"><label>Email panitia</label><input required type="email" name="site[email]" value="<?= spmb_admin_e($SITE['email']) ?>"></div></div></div>
        </div>
        <div class="setting-page" id="page-3" hidden>
        <div class="setting-block" style="margin-top:0;padding-top:0;border-top:0"><h3>Page 3 · Persiapan, syarat, dan rincian biaya MTs</h3><p class="subheading">Yang perlu disiapkan</p><div style="overflow:auto"><table class="setting-table"><thead><tr><th>Item</th><th>Keterangan</th></tr></thead><tbody id="syarat-list"><?php foreach ($SYARAT as $i => $row): ?><tr><td><input required name="syarat[<?= $i ?>][nama]" value="<?= spmb_admin_e($row[0]) ?>"></td><td><input name="syarat[<?= $i ?>][ket]" value="<?= spmb_admin_e($row[1]) ?>"></td></tr><?php endforeach; ?></tbody></table></div><button class="button add-row" type="button" data-add-row="syarat-list" data-template="syarat-template">+ Tambah syarat berkas</button><p class="subheading">Syarat untuk mendaftar</p><div class="settings-grid" id="mts-requirements-list"><?php foreach ($PERSYARATAN_MTS as $i => $requirement): ?><div class="field"><label>Syarat <?= $i + 1 ?></label><input required name="mts_requirements[<?= $i ?>]" value="<?= spmb_admin_e($requirement) ?>"></div><?php endforeach; ?></div><button class="button add-row" type="button" data-add-row="mts-requirements-list" data-template="mts-requirement-template">+ Tambah syarat pendaftaran</button><p class="subheading">Rincian biaya MTs</p><div style="overflow:auto"><table class="setting-table"><thead><tr><th>Komponen</th><th>Nominal</th><th>Catatan</th></tr></thead><tbody id="mts-costs-list"><?php foreach ($RINCIAN_BIAYA_MTS as $i => $cost): ?><tr><td><input required name="mts_costs[<?= $i ?>][0]" value="<?= spmb_admin_e($cost[0]) ?>"></td><td><input required type="number" min="0" name="mts_costs[<?= $i ?>][1]" value="<?= (int) $cost[1] ?>"></td><td><input name="mts_costs[<?= $i ?>][2]" value="<?= spmb_admin_e($cost[2]) ?>"></td></tr><?php endforeach; ?></tbody></table></div><button class="button add-row" type="button" data-add-row="mts-costs-list" data-template="mts-cost-template">+ Tambah rincian biaya</button></div>
        </div>
        </div>
        <button class="button save-settings" type="submit">Simpan semua pengaturan</button>
      </form>
    </section>
  </main>
  <script>
    (function () {
      'use strict';

      var pageTabs = Array.prototype.slice.call(document.querySelectorAll('[data-page-tab]'));
      var pagePanels = Array.prototype.slice.call(document.querySelectorAll('.setting-page'));
      pageTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          var pageId = tab.getAttribute('data-page-tab');
          pageTabs.forEach(function (item) {
            var active = item === tab;
            item.classList.toggle('active', active);
            item.setAttribute('aria-selected', active ? 'true' : 'false');
          });
          pagePanels.forEach(function (panel) {
            panel.hidden = panel.id !== pageId;
          });
        });
      });

      function rowTemplate(template, index) {
        if (template === 'wave-template') {
          return '<tr><td><input required name="waves[' + index + '][nama]" value="Gelombang baru"></td><td><input required type="date" name="waves[' + index + '][mulai]"></td><td><input required type="date" name="waves[' + index + '][selesai]"></td><td><input type="number" min="0" name="waves[' + index + '][potongan]" value="0"></td><td><input name="waves[' + index + '][catatan]"></td></tr>';
        }

        if (template.indexOf('cost-') === 0 || template === 'mts-cost-template') {
          var prefix = template === 'mts-cost-template' ? 'mts_costs' : 'biaya[' + template.split('-')[1] + '][' + template.split('-')[2] + ']';
          return '<tr><td><input required name="' + prefix + '[' + index + '][0]"></td><td><input required type="number" min="0" name="' + prefix + '[' + index + '][1]" value="0"></td><td><input name="' + prefix + '[' + index + '][2]"></td></tr>';
        }

        if (template === 'syarat-template') {
          return '<tr><td><input required name="syarat[' + index + '][nama]"></td><td><input name="syarat[' + index + '][ket]"></td></tr>';
        }

        if (template === 'rekening-template') {
          return '<div class="option-card"><div class="field"><label>Nama bank</label><input required name="rekening_options[' + index + '][bank]"></div><div class="field"><label>Nomor rekening</label><input required name="rekening_options[' + index + '][nomor]"></div><div class="field"><label>Nama pemilik rekening</label><input required name="rekening_options[' + index + '][nama]"></div></div>';
        }

        if (template === 'whatsapp-template') {
          return '<div class="option-card"><div class="field"><label>Nomor WhatsApp</label><input required name="whatsapp_options[' + index + '][nomor]" data-whatsapp-number><span class="help">Format internasional, contoh 62812...</span><a class="button whatsapp-link" href="#" target="_blank" rel="noopener" data-whatsapp-link>Hubungi nomor ini</a></div><div class="field"><label>Nomor yang ditampilkan</label><input required name="whatsapp_options[' + index + '][tampil]"></div><div class="field"><label>Nama petugas</label><input name="whatsapp_options[' + index + '][nama]"></div></div>';
        }

        return '<div class="field"><label>Syarat ' + (index + 1) + '</label><input required name="mts_requirements[' + index + ']"></div>';
      }

      function addDeleteButton(list, item) {
        if (item.querySelector('.delete-row')) return;
        var deleteButton = document.createElement('button');
        deleteButton.className = 'button delete-row';
        deleteButton.type = 'button';
        deleteButton.textContent = 'Hapus';
        deleteButton.addEventListener('click', function () {
          if (list.children.length <= 1) {
            window.alert('Minimal satu data harus dipertahankan.');
            return;
          }
          item.remove();
        });
        if (item.tagName === 'TR') {
          var cell = document.createElement('td');
          cell.appendChild(deleteButton);
          item.appendChild(cell);
        } else {
          item.appendChild(deleteButton);
        }
      }

      function updateWhatsappLink(input) {
        var link = input.parentElement.querySelector('[data-whatsapp-link]');
        if (!link) return;
        var number = input.value.replace(/\D/g, '');
        if (number.indexOf('0') === 0) number = '62' + number.slice(1);
        link.href = number === '' ? '#' : 'https://wa.me/' + number;
      }

      document.querySelectorAll('[data-whatsapp-number]').forEach(function (input) {
        input.addEventListener('input', function () { updateWhatsappLink(input); });
        updateWhatsappLink(input);
      });

      document.querySelectorAll('[data-add-row]').forEach(function (button) {
        var list = document.getElementById(button.dataset.addRow);
        if (list) Array.prototype.forEach.call(list.children, function (item) { addDeleteButton(list, item); });
        button.addEventListener('click', function () {
          if (!list) return;
          var index = list.children.length;
          list.insertAdjacentHTML('beforeend', rowTemplate(button.dataset.template, index));
          addDeleteButton(list, list.lastElementChild);
          var newWhatsappNumber = list.lastElementChild.querySelector('[data-whatsapp-number]');
          if (newWhatsappNumber) {
            newWhatsappNumber.addEventListener('input', function () { updateWhatsappLink(newWhatsappNumber); });
            updateWhatsappLink(newWhatsappNumber);
          }
        });
      });
    }());
  </script>
</body>
</html>

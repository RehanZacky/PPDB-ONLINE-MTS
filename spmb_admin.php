<?php
declare(strict_types=1);

require __DIR__ . '/config.php';

$storageRoot = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'pendaftaran';

function spmb_admin_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function spmb_registration_id(string $value): bool
{
    return preg_match('/^MTS-[0-9]{8}-[A-F0-9]{6}$/', $value) === 1;
}

function spmb_file_size(int $bytes): string
{
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return number_format($bytes / 1024, 1, ',', '.') . ' KB';
    return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
}

function spmb_registrations(string $root): array
{
    if (!is_dir($root)) return [];

    $registrations = [];
    foreach (scandir($root) ?: [] as $folder) {
        if ($folder === '.' || $folder === '..' || !spmb_registration_id($folder)) continue;
        $folderPath = $root . DIRECTORY_SEPARATOR . $folder;
        if (!is_dir($folderPath)) continue;

        $files = [];
        foreach (scandir($folderPath) ?: [] as $file) {
            if ($file === '.' || $file === '..') continue;
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
            if (is_file($filePath)) {
                $files[] = [
                    'name' => $file,
                    'size' => (int) filesize($filePath),
                ];
            }

        }

        $registrations[] = [
            'id' => $folder,
            'files' => $files,
            'time' => (int) filemtime($folderPath),
        ];
    }

    usort($registrations, static function (array $a, array $b): int {
        return $b['time'] <=> $a['time'];
    });
    return $registrations;
}

function spmb_remove_directory(string $directory): bool
{
    if (!is_dir($directory)) return false;
    foreach (scandir($directory) ?: [] as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $directory . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            if (!spmb_remove_directory($path)) return false;
        } elseif (!unlink($path)) {
            return false;
        }
    }
    return rmdir($directory);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = strtoupper(trim((string) ($_POST['registration'] ?? '')));
    if (!spmb_registration_id($id)) {
        $message = 'Nomor pendaftaran tidak valid.';
        $messageError = true;
    } elseif (!spmb_remove_directory($storageRoot . DIRECTORY_SEPARATOR . $id)) {
        $message = 'Data pendaftaran tidak dapat dihapus.';
        $messageError = true;
    } else {
        $message = 'Data ' . $id . ' berhasil dihapus.';
    }
}

if (isset($_GET['download'])) {
    $id = strtoupper((string) ($_GET['registration'] ?? ''));
    $file = basename((string) $_GET['download']);
    $basePath = realpath($storageRoot . DIRECTORY_SEPARATOR . $id);
    $filePath = realpath($storageRoot . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $file);
    if (!spmb_registration_id($id) || $basePath === false || $filePath === false || strpos($filePath, $basePath . DIRECTORY_SEPARATOR) !== 0 || !is_file($filePath)) {
        http_response_code(404);
        exit('Dokumen tidak ditemukan.');
    }
    $mime = function_exists('mime_content_type') ? (mime_content_type($filePath) ?: 'application/octet-stream') : 'application/octet-stream';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . (string) filesize($filePath));
    header('Content-Disposition: inline; filename="' . str_replace('"', '', $file) . '"');
    readfile($filePath);
    exit;
}

$registrations = spmb_registrations($storageRoot);
$query = trim((string) ($_GET['q'] ?? ''));
$selectedWave = (string) ($_GET['wave'] ?? '');
if ($query !== '') {
    $registrations = array_values(array_filter($registrations, static function (array $item): bool {
        return stripos($item['id'], $query) !== false;
    }));
}
$totalDocuments = array_sum(array_map(static function (array $item): int {
    return count($item['files']);
}, $registrations));
$activeWave = gelombang_kini($GELOMBANG);
$username = 'Admin';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SPMB Admin - <?= spmb_admin_e($SITE['singkat']) ?></title>
  <style>
    :root{--green:#15803d;--dark:#14532d;--mint:#f0fdf4;--ink:#14251d;--muted:#64748b;--line:#e2e8f0;--red:#b91c1c}*{box-sizing:border-box}body{margin:0;background:#f8fafc;color:var(--ink);font:14px/1.6 system-ui,-apple-system,"Segoe UI",sans-serif}a{text-decoration:none;color:inherit}.top{background:linear-gradient(110deg,#166534,#15803d);color:#fff;box-shadow:0 3px 14px #064e3b22}.top-in,.main{width:min(1180px,calc(100% - 32px));margin:auto}.top-in{min-height:70px;display:flex;align-items:center;gap:20px}.brand{display:flex;align-items:center;gap:10px;margin-right:auto}.mark{width:39px;height:39px;border-radius:11px;background:#fff;color:var(--green);display:grid;place-items:center;font-weight:900}.brand b{display:block;font-size:1.08rem}.brand small{color:#bbf7d0}.nav{display:flex;gap:5px}.nav a{padding:9px 11px;border-radius:8px;color:#dcfce7;font-weight:700}.nav a:hover,.nav .active{background:#14532d;color:#fff}.user{color:#dcfce7}.main{padding:30px 0 55px}.heading{display:flex;align-items:end;justify-content:space-between;gap:15px;margin-bottom:21px}.heading h1{font-size:1.7rem;margin:0}.heading p{margin:5px 0 0;color:var(--muted)}.button{display:inline-block;padding:10px 14px;border-radius:9px;background:var(--green);color:#fff;font-weight:800}.button.light{background:#dcfce7;color:var(--dark)}.summary{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:21px}.summary-card,.panel{background:#fff;border:1px solid var(--line);border-radius:15px;box-shadow:0 5px 16px #0f172a08}.summary-card{padding:18px}.summary-card b{display:block;color:var(--green);font-size:1.65rem}.summary-card span{color:var(--muted)}.panel{padding:20px}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:17px}.panel h2{font-size:1.1rem;margin:0}.filter{display:flex;gap:9px;margin-bottom:17px}.filter input,.filter select{margin:0;padding:10px 12px;border:1px solid #cbd5e1;border-radius:9px;font:inherit;background:#fff}.filter input{flex:1;min-width:180px}.filter button{width:auto;padding:10px 17px}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse;min-width:690px}.table th,.table td{text-align:left;padding:12px 9px;border-bottom:1px solid #f1f5f9;vertical-align:top}.table th{font-size:.73rem;color:var(--muted);text-transform:uppercase;letter-spacing:.03em}.id{font-weight:850;color:var(--green);white-space:nowrap}.meta{color:var(--muted);font-size:.8rem}.files{display:flex;flex-wrap:wrap;gap:5px;max-width:380px}.file{padding:4px 7px;border-radius:6px;background:#f1f5f9;font-size:.75rem}.file:hover{background:#dcfce7;color:var(--dark)}.delete{border:0;padding:7px 9px;border-radius:7px;background:#fee2e2;color:var(--red);font:inherit;font-weight:700;cursor:pointer}.notice{padding:11px 14px;background:#dcfce7;color:var(--dark);border-radius:9px;margin-bottom:18px}.notice.error{background:#fef2f2;color:var(--red)}.wave{margin-top:20px;padding:15px;border:1px solid #bbf7d0;background:var(--mint);border-radius:10px}.wave strong{color:var(--dark)}.empty{text-align:center;padding:35px;color:var(--muted)}    .footer{text-align:center;color:var(--muted);font-size:.8rem;margin-top:22px}.settings-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.field{display:flex;flex-direction:column;gap:5px}.field label,.subheading{font-weight:800}.field input,.field textarea{width:100%;padding:9px 10px;border:1px solid #cbd5e1;border-radius:8px;font:inherit}.field textarea{min-height:58px;resize:vertical}.setting-block{margin-top:22px;padding-top:18px;border-top:1px solid var(--line)}.setting-block h3{font-size:1rem;margin:0 0 10px}.setting-table{width:100%;border-collapse:collapse}.setting-table th,.setting-table td{text-align:left;padding:6px;vertical-align:top}.setting-table th{font-size:.75rem;color:var(--muted)}.setting-table input{min-width:100px}.save-settings{margin-top:20px}.help{color:var(--muted);font-size:.82rem}.remove-row{border:0;background:#fee2e2;color:var(--red);border-radius:6px;padding:7px;cursor:pointer}@media(max-width:720px){.top-in{width:calc(100% - 22px)}.nav{display:none}.user{font-size:.8rem}.main{width:calc(100% - 22px);padding-top:22px}.heading{align-items:flex-start;flex-direction:column}.summary{grid-template-columns:1fr}.panel{padding:14px}.filter{flex-wrap:wrap}.filter input{min-width:100%}.settings-grid{grid-template-columns:1fr}.setting-table{display:block;overflow:auto;white-space:nowrap}}
  </style>
</head>
<body>
  <header class="top">
    <div class="top-in">
      <a class="brand" href="spmb_admin.php"><span class="mark">SP</span><span><b>SPMB Admin</b><small><?= spmb_admin_e($SITE['singkat']) ?></small></span></a>
      <nav class="nav" aria-label="Navigasi admin"><a class="active" href="spmb_admin.php">Data SPMB</a><a href="settings.php">Pengaturan</a><a href="index.php" target="_blank" rel="noopener">Beranda</a><a href="gelombang.php" target="_blank" rel="noopener">Gelombang</a><a href="alur.php" target="_blank" rel="noopener">Alur &amp; Syarat</a><a href="biaya.php" target="_blank" rel="noopener">Biaya</a><a href="pembayaran.php" target="_blank" rel="noopener">Pembayaran</a></nav>
      <span class="user"><?= spmb_admin_e($username) ?></span>
    </div>
  </header>
  <main class="main">
    <div class="heading"><div><h1>Pengelolaan SPMB</h1><p>Kelola data pendaftaran calon santri dan dokumen pendukung.</p></div><a class="button light" href="pendaftaran.php" target="_blank" rel="noopener">Buka formulir pendaftaran</a></div>
    <section class="summary">
      <div class="summary-card"><b><?= count($registrations) ?></b><span>Pendaftar ditemukan</span></div>
      <div class="summary-card"><b><?= $totalDocuments ?></b><span>Dokumen tersimpan</span></div>
      <div class="summary-card"><b><?= $activeWave ? spmb_admin_e($activeWave['nama']) : 'Ditutup' ?></b><span>Gelombang saat ini</span></div>
    </section>
    <section class="panel">
      <div class="panel-head"><h2>Data pendaftaran</h2><span class="meta">Terbaru ditampilkan lebih dulu</span></div>
      <form class="filter" method="get"><input type="search" name="q" value="<?= spmb_admin_e($query) ?>" placeholder="Cari nomor pendaftaran..."><button class="button" type="submit">Cari</button><?php if ($query !== ''): ?><a class="button light" href="spmb_admin.php">Reset</a><?php endif; ?></form>
      <?php if (!$registrations): ?><div class="empty">Belum ada data pendaftaran yang cocok.</div><?php else: ?>
      <div class="table-wrap"><table class="table"><thead><tr><th>Nomor pendaftaran</th><th>Dokumen</th><th>Waktu masuk</th><th>Aksi</th></tr></thead><tbody>
      <?php foreach ($registrations as $item): ?><tr><td><div class="id"><?= spmb_admin_e($item['id']) ?></div><div class="meta"><?= count($item['files']) ?> file</div></td><td><div class="files"><?php foreach ($item['files'] as $file): ?><a class="file" href="?registration=<?= rawurlencode($item['id']) ?>&download=<?= rawurlencode($file['name']) ?>" target="_blank" rel="noopener"><?= spmb_admin_e($file['name']) ?> · <?= spmb_file_size($file['size']) ?></a><?php endforeach; ?></div></td><td class="meta"><?= spmb_admin_e(date('d M Y, H:i', $item['time'])) ?></td><td><form method="post" onsubmit="return confirm('Hapus semua data <?= spmb_admin_e($item['id']) ?>?')"><input type="hidden" name="delete" value="1"><input type="hidden" name="registration" value="<?= spmb_admin_e($item['id']) ?>"><button class="delete" type="submit">Hapus</button></form></td></tr><?php endforeach; ?>
      </tbody></table></div>
      <?php endif; ?>
    </section>
    <?php if ($activeWave): ?><div class="wave"><strong><?= spmb_admin_e($activeWave['nama']) ?> sedang dibuka</strong> · <?= spmb_admin_e(rentang($activeWave['mulai'], $activeWave['selesai'])) ?> · Potongan <?= spmb_admin_e(rp((int) $activeWave['potongan'])) ?></div><?php endif; ?>
    <div class="footer">Halaman SPMB admin · Tahun ajaran <?= spmb_admin_e($SITE['tahun_ajaran']) ?></div>
  </main>
</body>
</html>

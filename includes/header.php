<?php
/* Bagian atas semua halaman SPMB.
   Setiap halaman mengisi $PAGE (slug, judul, deskripsi) sebelum meng-include file ini. */

if (!isset($SITE)) {
    require_once __DIR__ . '/../config.php';
}

$PAGE = array_merge([
    'slug'      => 'index',
    'judul'     => 'SPMB',
    'deskripsi' => '',
], $PAGE ?? []);

$HALAMAN = [
    'index'     => ['Beranda SPMB', 'index.php'],
    'gelombang' => ['Gelombang', 'gelombang.php'],
    'alur'      => ['Alur & Syarat', 'alur.php'],
    'biaya'     => ['Biaya', 'biaya.php'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($PAGE['judul']) ?> — <?= e($SITE['singkat']) ?></title>
<meta name="description" content="<?= e($PAGE['deskripsi']) ?>">
<meta property="og:title" content="<?= e($PAGE['judul']) ?> — <?= e($SITE['singkat']) ?>">
<meta property="og:description" content="<?= e($PAGE['deskripsi']) ?>">
<meta property="og:type" content="website">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/spmb.css">
</head>
<body>

<a class="lewati" href="#isi">Lewati ke isi halaman</a>

<header class="atas">
  <div class="wadah atas-isi">
    <a class="merek" href="<?= e($SITE['url_profil']) ?>">
      <?php if (!empty($SITE['logo']) && file_exists(__DIR__ . '/../' . $SITE['logo'])): ?>
        <img src="<?= e($SITE['logo']) ?>" alt="Logo <?= e($SITE['lembaga']) ?>" width="62" height="62">
      <?php else: ?>
        <span class="lambang" aria-hidden="true">LOGO</span>
      <?php endif; ?>
      <span>
        <b><?= e($SITE['yayasan']) ?><br><?= e($SITE['lembaga']) ?></b>
        <span>Penerimaan Murid Baru <?= e($SITE['tahun_ajaran']) ?></span>
      </span>
    </a>

    <nav class="menu" id="menu" aria-label="Navigasi utama">
      <a href="<?= e($SITE['url_profil']) ?>">Beranda</a>
      <a href="<?= e($SITE['url_profil']) ?>/#profil">Profil</a>
      <a href="<?= e($SITE['url_profil']) ?>/#berita">Berita</a>
      <a href="<?= e($SITE['url_profil']) ?>/#prestasi">Prestasi</a>
      <a href="<?= e($SITE['url_profil']) ?>/#galeri">Galeri</a>
      <a class="aktif" href="index.php" aria-current="page">SPMB</a>
    </nav>

    <button class="tombol-menu" id="tombol-menu" aria-expanded="false" aria-controls="menu" aria-label="Buka menu">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
        <path d="M4 7h16M4 12h16M4 17h16"/>
      </svg>
    </button>
  </div>
</header>

<nav class="subnav" aria-label="Navigasi SPMB">
  <div class="wadah subnav-isi">
    <?php foreach ($HALAMAN as $slug => [$label, $file]): ?>
      <a href="<?= e($file) ?>"<?= $slug === $PAGE['slug'] ? ' class="aktif" aria-current="page"' : '' ?>><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>
</nav>

<main id="isi">

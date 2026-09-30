<?php
require __DIR__ . '/config.php';

function pdf_text(string $text): string
{
  $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: $text;
  return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

function pdf_png_logo(string $path): ?array
{
  $png = @file_get_contents($path);
  if ($png === false || substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") {
    return null;
  }
  $offset = 8;
  $palette = '';
  $compressed = '';
  $width = $height = $colorType = $bitDepth = 0;
  while ($offset + 12 <= strlen($png)) {
    $length = unpack('N', substr($png, $offset, 4))[1];
    $type = substr($png, $offset + 4, 4);
    $chunk = substr($png, $offset + 8, $length);
    $offset += 12 + $length;
    if ($type === 'IHDR') {
      $header = unpack('Nwidth/Nheight/CbitDepth/CcolorType', substr($chunk, 0, 10));
      $width = $header['width'];
      $height = $header['height'];
      $bitDepth = $header['bitDepth'];
      $colorType = $header['colorType'];
    } elseif ($type === 'PLTE') {
      $palette = $chunk;
    } elseif ($type === 'IDAT') {
      $compressed .= $chunk;
    } elseif ($type === 'IEND') {
      break;
    }
  }
  if ($width < 1 || $height < 1 || $bitDepth !== 8 || !in_array($colorType, [2, 3, 6], true) || ($colorType === 3 && $palette === '')) {
    return null;
  }
  $bytesPerPixel = $colorType === 3 ? 1 : ($colorType === 2 ? 3 : 4);
  $raw = @zlib_decode($compressed);
  if ($raw === false || strlen($raw) < ($width * $bytesPerPixel + 1) * $height) {
    return null;
  }
  $rgbRows = '';
  $previous = array_fill(0, $width * $bytesPerPixel, 0);
  $position = 0;
  for ($row = 0; $row < $height; $row++) {
    $filter = ord($raw[$position++]);
    $current = array_fill(0, $width * $bytesPerPixel, 0);
    $rgbRow = "\x00";
    for ($pixel = 0; $pixel < $width * $bytesPerPixel; $pixel++) {
      $value = ord($raw[$position++]);
      $left = $pixel >= $bytesPerPixel ? $current[$pixel - $bytesPerPixel] : 0;
      $above = $previous[$pixel];
      $upperLeft = $pixel >= $bytesPerPixel ? $previous[$pixel - $bytesPerPixel] : 0;
      if ($filter === 1) $value = ($value + $left) & 255;
      elseif ($filter === 2) $value = ($value + $above) & 255;
      elseif ($filter === 3) $value = ($value + intdiv($left + $above, 2)) & 255;
      elseif ($filter === 4) {
        $estimate = $left + $above - $upperLeft;
        $pa = abs($estimate - $left);
        $pb = abs($estimate - $above);
        $pc = abs($estimate - $upperLeft);
        $predictor = $pa <= $pb && $pa <= $pc ? $left : ($pb <= $pc ? $above : $upperLeft);
        $value = ($value + $predictor) & 255;
      }
      $current[$pixel] = $value;
    }
    for ($x = 0; $x < $width; $x++) {
      if ($colorType === 3) {
        $rgbRow .= substr($palette, $current[$x] * 3, 3);
      } else {
        $pixelOffset = $x * $bytesPerPixel;
        $rgbRow .= chr($current[$pixelOffset]) . chr($current[$pixelOffset + 1]) . chr($current[$pixelOffset + 2]);
      }
    }
    $rgbRows .= $rgbRow;
    $previous = $current;
  }
  return ['width' => $width, 'height' => $height, 'data' => gzcompress($rgbRows)];
}

function pdf_uploaded_image(string $path): ?array
{
  $imageInfo = @getimagesize($path);
  $imageData = @file_get_contents($path);
  if ($imageInfo === false || $imageData === false || $imageInfo[0] < 1 || $imageInfo[1] < 1) {
    return null;
  }

  if (($imageInfo['mime'] ?? '') === 'image/jpeg') {
    return [
      'width' => $imageInfo[0],
      'height' => $imageInfo[1],
      'data' => $imageData,
      'filter' => '/DCTDecode',
      'colors' => 3,
    ];
  }

  if (($imageInfo['mime'] ?? '') === 'image/png') {
    $png = pdf_png_logo($path);
    if ($png !== null) {
      return $png + ['filter' => '/FlateDecode', 'colors' => 3, 'predictor' => true];
    }
  }

  return null;
}

function create_registration_pdf(string $path, string $registrationNumber, array $data, array $files): void
{
  $value = static function (string $label) use ($data): string {
    return trim((string) ($data[$label] ?? ''));
  };
  $text = static function (string $font, float $size, float $x, float $y, string $content): string {
    return "BT /{$font} {$size} Tf {$x} {$y} Td (" . pdf_text(substr($content, 0, 82)) . ") Tj ET\n";
  };
  $line = static function (float $x1, float $y1, float $x2, float $y2, float $width = 1): string {
    return "{$width} w {$x1} {$y1} m {$x2} {$y2} l S\n";
  };
  $field = static function (string $label, string $fieldValue, float $y, string $prefix = '') use ($text): string {
    return $text('F2', 10, 55, $y, $prefix . $label) . $text('F1', 10, 220, $y, ': ' . ($fieldValue !== '' ? $fieldValue : '-'));
  };

  $logo = pdf_png_logo(__DIR__ . '/assets/img/logo.png');
  $logoMark = $logo !== null ? "q 90 0 0 90 45 700 cm /Im1 Do Q\n" : '';
  $header = $logoMark . $text('F2', 16, 145, 790, 'YAYASAN ROUDLOTUL QURAN AZ ZUHRI')
    . $text('F2', 17, 185, 766, 'MTS ROUDLOTUL QURAN')
    . $text('F1', 11, 155, 742, 'Desa Ngampelsari Rt. 03 Ngampelsari, Candi, Sidoarjo')
    . $text('F1', 10, 130, 722, 'Email: mtsroudlotulquran@gmail.com   Telepon: 081230294589')
    . $text('F1', 10, 120, 702, 'SK KEMENKUMHAM Nomor AHU-0027813.AH.01.04. Tahun 2022')
    . $line(45, 685, 550, 685, 1.2) . $line(45, 680, 550, 680, 3);

  $pageOne = $header . $text('F2', 18, 165, 640, 'D A T A  D I R I  S I S W A');
  $pageOne .= $field('1. Nama Siswa', $value('Nama siswa'), 575);
  $pageOne .= $field('2. Nomor Induk', $value('Nomor induk'), 554);
  $pageOne .= $field('3. NIS Nasional', $value('NISN'), 533);
  $pageOne .= $field('4. Jenis Kelamin', $value('Jenis kelamin'), 512);
  $pageOne .= $field('5. Tempat dan Tgl Lahir', $value('Tempat dan tanggal lahir'), 491);
  $pageOne .= $field('6. Agama', $value('Agama'), 470);
  $pageOne .= $field('7. Anak Ke', $value('Anak ke'), 449);
  $pageOne .= $field('8. Status di Keluarga', $value('Status di keluarga'), 428);
  $pageOne .= $field('9. Alamat Siswa', $value('Alamat siswa'), 407);
  $pageOne .= $field('10. Telepon Siswa', $value('Telepon siswa'), 386);
  $pageOne .= $text('F2', 10, 55, 350, '11. Sekolah Asal');
  $pageOne .= $field('Nama Sekolah', $value('Sekolah asal'), 329, 'a. ');
  $pageOne .= $field('Alamat Sekolah', $value('Alamat sekolah asal'), 308, 'b. ');
  $pageOne .= $text('F2', 10, 55, 273, '12. Nama Orang Tua');
  $pageOne .= $field('Ayah', $value('Nama ayah'), 252, 'a. ');
  $pageOne .= $field('Ibu', $value('Nama ibu'), 231, 'b. ');
  $pageOne .= $field('13. Telepon Orang Tua', $value('Telepon orang tua'), 195);
  $pageOne .= $field('14. Alamat Orang Tua', $value('Alamat orang tua'), 174);
  $pageOne .= $text('F2', 10, 55, 137, '15. Pekerjaan Orang Tua');
  $pageOne .= $field('Ayah', $value('Pekerjaan ayah'), 116, 'a. ');
  $pageOne .= $field('Ibu', $value('Pekerjaan ibu'), 95, 'b. ');
  $pageOne .= $field('16. Nama Wali', $value('Nama wali'), 71);
  $pageOne .= $field('17. Alamat Wali', $value('Alamat wali'), 50);
  $pageOne .= $field('18. Pekerjaan Wali', $value('Pekerjaan wali'), 29);
  $pageOne .= $field('19. Telepon Wali', $value('Telepon wali'), 8);

  $registrationPage = $header . $text('F2', 16, 190, 640, 'DATA PENDAFTARAN')
    . $field('Nomor pendaftaran', $registrationNumber, 590)
    . $field('Tanggal kirim', date('d-m-Y H:i'), 569)
    . $field('Kategori santri', $value('Kategori santri'), 548);
  $uploadedImages = [];
  $imageIndex = 2;
  foreach ($files as $label => $fileName) {
    $image = pdf_uploaded_image(dirname($path) . '/' . $fileName);
    if ($image === null) {
      continue;
    }
    $image['resource'] = 'Im' . $imageIndex++;
    $uploadedImages[] = ['label' => $label, 'image' => $image];
  }
  $uploadedPages = [];
  $pageImageResources = [[], []];
  foreach ($uploadedImages as $uploadedImage) {
    $maxWidth = 470;
    $maxHeight = 610;
    if (in_array($uploadedImage['label'], ['Foto siswa', 'Bukti pembayaran'], true)) {
      $maxWidth = 270;
      $maxHeight = 270;
    }
    $scale = min($maxWidth / $uploadedImage['image']['width'], $maxHeight / $uploadedImage['image']['height']);
    $width = $uploadedImage['image']['width'] * $scale;
    $height = $uploadedImage['image']['height'] * $scale;
    $x = (595 - $width) / 2;
    $y = 390 - ($height / 2);
    $uploadedPages[] = $header . $text('F2', 16, 150, 640, $uploadedImage['label'])
      . "q {$width} 0 0 {$height} {$x} {$y} cm /{$uploadedImage['image']['resource']} Do Q\n";
    $pageImageResources[] = [$uploadedImage['image']['resource'] => null];
  }

  $pages = array_merge([$pageOne, $registrationPage], $uploadedPages);
  $objects = ['<< /Type /Catalog /Pages 2 0 R >>', '', '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>', '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>'];
  $pageNumbers = [];
  $contentNumbers = [];
  $imageNumber = null;
  if ($logo !== null) {
    $imageNumber = 5;
    $objects[] = '<< /Type /XObject /Subtype /Image /Width ' . $logo['width'] . ' /Height ' . $logo['height'] . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /FlateDecode /DecodeParms << /Predictor 15 /Colors 3 /BitsPerComponent 8 /Columns ' . $logo['width'] . ' >> /Length ' . strlen($logo['data']) . " >>\nstream\n" . $logo['data'] . "\nendstream";
  }
  $uploadedImageObjects = [];
  foreach ($uploadedImages as $uploadedImage) {
    $image = $uploadedImage['image'];
    $imageNumber = $logo !== null ? 6 + count($uploadedImageObjects) : 5 + count($uploadedImageObjects);
    $decodeParams = !empty($image['predictor']) ? ' /DecodeParms << /Predictor 15 /Colors 3 /BitsPerComponent 8 /Columns ' . $image['width'] . ' >>' : '';
    $objects[] = '<< /Type /XObject /Subtype /Image /Width ' . $image['width'] . ' /Height ' . $image['height'] . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter ' . $image['filter'] . $decodeParams . ' /Length ' . strlen($image['data']) . " >>\nstream\n" . $image['data'] . "\nendstream";
    $uploadedImageObjects[$image['resource']] = $imageNumber;
  }
  $nextObject = ($logo !== null ? 6 : 5) + count($uploadedImageObjects);
  foreach ($pages as $unused) {
    $pageNumbers[] = $nextObject++;
    $contentNumbers[] = $nextObject++;
  }
  $kids = implode(' ', array_map(static fn ($number) => $number . ' 0 R', $pageNumbers));
  $objects[1] = '<< /Type /Pages /Kids [' . $kids . '] /Count ' . count($pages) . ' >>';
  foreach ($pages as $pageIndex => $content) {
    $imageResources = [];
    if ($logo !== null) {
      $imageResources['Im1'] = 5;
    }
    if (isset($pageImageResources[$pageIndex])) {
      foreach ($pageImageResources[$pageIndex] as $resource => $unused) {
        $imageResources[$resource] = $uploadedImageObjects[$resource];
      }
    }
    $imageResource = $imageResources === [] ? '' : ' /XObject << ' . implode(' ', array_map(static fn ($name, $number) => '/' . $name . ' ' . $number . ' 0 R', array_keys($imageResources), $imageResources)) . ' >>';
    $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R /F2 4 0 R >>' . $imageResource . ' >> /Contents ' . $contentNumbers[$pageIndex] . ' 0 R >>';
    $objects[] = '<< /Length ' . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
  }

  $pdf = "%PDF-1.4\n";
  $offsets = [0];
  foreach ($objects as $index => $object) {
    $objectNumber = $index + 1;
    $offsets[$objectNumber] = strlen($pdf);
    $pdf .= $objectNumber . " 0 obj\n" . $object . "\nendobj\n";
  }
  $xrefOffset = strlen($pdf);
  $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
  for ($index = 1; $index <= count($objects); $index++) {
    $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
  }
  $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";
  file_put_contents($path, $pdf, LOCK_EX);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $registrationNumber = 'MTS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
  $storageDirectory = __DIR__ . '/uploads/pendaftaran/' . $registrationNumber;
  if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0755, true)) {
    http_response_code(500);
    exit('Folder penyimpanan pendaftaran tidak dapat dibuat.');
  }
  $labels = [
    'nama_siswa' => 'Nama siswa', 'nomor_induk' => 'Nomor induk', 'nisn' => 'NISN',
    'jenis_kelamin' => 'Jenis kelamin', 'tempat_tanggal_lahir' => 'Tempat dan tanggal lahir',
    'agama' => 'Agama', 'anak_ke' => 'Anak ke', 'status_keluarga' => 'Status di keluarga',
    'kategori_santri' => 'Kategori santri', 'alamat_siswa' => 'Alamat siswa', 'telepon_siswa' => 'Telepon siswa',
    'nama_sekolah' => 'Sekolah asal', 'alamat_sekolah' => 'Alamat sekolah asal',
    'nama_ayah' => 'Nama ayah', 'nama_ibu' => 'Nama ibu', 'telepon_orang_tua' => 'Telepon orang tua', 'alamat_orang_tua' => 'Alamat orang tua',
    'pekerjaan_ayah' => 'Pekerjaan ayah', 'pekerjaan_ibu' => 'Pekerjaan ibu',
    'nama_wali' => 'Nama wali', 'alamat_wali' => 'Alamat wali', 'pekerjaan_wali' => 'Pekerjaan wali', 'telepon_wali' => 'Telepon wali',
  ];
  $data = [];
  foreach ($labels as $key => $label) {
    $data[$label] = trim((string) ($_POST[$key] ?? ''));
  }
  $data['Nomor MTS'] = trim((string) ($_POST['nomor_mts'] ?? ''));
  $files = [];
  foreach (['akta' => 'Akta kelahiran', 'kk' => 'Kartu keluarga', 'sertifikat' => 'Sertifikat/prestasi', 'rapor' => 'Rapor/nilai', 'foto' => 'Foto siswa', 'dokumen_lain' => 'Dokumen lainnya', 'bukti_pembayaran' => 'Bukti pembayaran'] as $key => $label) {
    if (empty($_FILES[$key]['name']) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
      continue;
    }
    $extension = strtolower(pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION));
    $safeName = $key . ($extension ? '.' . preg_replace('/[^a-z0-9]/', '', $extension) : '');
    if (move_uploaded_file($_FILES[$key]['tmp_name'], $storageDirectory . '/' . $safeName)) {
      $files[$label] = $safeName;
    }
  }
  $pdfName = $registrationNumber . '.pdf';
  $pdfPath = $storageDirectory . '/' . $pdfName;
  create_registration_pdf($pdfPath, $registrationNumber, $data, $files);
  header('Content-Type: application/pdf');
  header('Content-Disposition: attachment; filename="' . $pdfName . '"');
  header('Content-Length: ' . filesize($pdfPath));
  readfile($pdfPath);
  exit;
}

$PAGE = [
    'slug' => 'pendaftaran',
    'judul' => 'Formulir Pendaftaran SPMB',
    'deskripsi' => 'Formulir pendaftaran calon santri baru MTs Tahfidh Roudlotul Qur\'an.',
];

include __DIR__ . '/includes/header.php';
?>

<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Formulir Pendaftaran</span>
      <h2>Daftar calon santri baru</h2>
    </div>

    <form class="ppdb-form" action="pendaftaran.php" method="post" enctype="multipart/form-data">
      <div class="step-indicator" aria-label="Tahap pendaftaran">
        <div class="step-item active" data-step="0">
          <span class="step-dot">1</span>
          <small>Data Diri</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item" data-step="1">
          <span class="step-dot">2</span>
          <small>Data Pendukung</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item" data-step="2">
          <span class="step-dot">3</span>
          <small>Bukti Pembayaran</small>
        </div>
      </div>

      <div class="form-step active" data-step="1">
        <div class="form-panel">
          <h3>Data Diri Siswa</h3>

          <div class="form-grid">
            <div class="field">
              <label for="nama-siswa">1. Nama Siswa</label>
              <input id="nama-siswa" type="text" name="nama_siswa" placeholder="Masukkan nama lengkap" required />
            </div>

            <div class="field">
              <label for="nomor-induk">2. Nomor Induk</label>
              <input id="nomor-induk" type="text" name="nomor_induk" placeholder="Nomor induk" required />
            </div>

            <div class="field">
              <label for="nisn">3. NIS Nasional</label>
              <input id="nisn" type="text" name="nisn" placeholder="NISN" required />
            </div>

            <div class="field">
              <label for="jenis-kelamin">4. Jenis Kelamin</label>
              <select id="jenis-kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
              </select>
            </div>

            <div class="field">
              <label for="tempat-lahir">5. Tempat dan Tgl Lahir</label>
              <input id="tempat-lahir" type="text" name="tempat_tanggal_lahir" placeholder="Contoh: Bandung, 14 Januari 2012" required />
            </div>

            <div class="field">
              <label for="agama">6. Agama</label>
              <select id="agama" name="agama" required>
                <option value="">-- Pilih --</option>
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Budha">Budha</option>
                <option value="Konghucu">Konghucu</option>
              </select>
            </div>

            <div class="field">
              <label for="anak-ke">7. Anak Ke</label>
              <input id="anak-ke" type="text" name="anak_ke" placeholder="Anak ke-" required />
            </div>

            <div class="field">
              <label for="status-keluarga">8. Status di Keluarga</label>
              <select id="status-keluarga" name="status_keluarga" required>
                <option value="">-- Pilih --</option>
                <option value="Anak Kandung">Anak Kandung</option>
                <option value="Anak Angkat">Anak Angkat</option>
                <option value="Anak Tiri">Anak Tiri</option>
              </select>
            </div>

            <div class="field field-wide">
              <label>9. Kategori Santri</label>
              <div class="radio-group" role="radiogroup" aria-label="Kategori Santri">
                <label class="radio-item">
                  <span class="radio-mark">
                    <input type="radio" name="kategori_santri" value="Mukim" required />
                  </span>
                  <span class="radio-text">Santri Mukim</span>
                </label>
                <label class="radio-item">
                  <span class="radio-mark">
                    <input type="radio" name="kategori_santri" value="Non Mukim" required />
                  </span>
                  <span class="radio-text">Santri Non Mukim</span>
                </label>
              </div>
            </div>

            <div class="field field-wide">
              <label for="alamat-siswa">9. Alamat Siswa</label>
              <textarea id="alamat-siswa" name="alamat_siswa" placeholder="Masukkan alamat lengkap siswa" required></textarea>
            </div>

            <div class="field">
              <label for="telepon-siswa">10. Nomor Telepon Siswa <small>(opsional)</small></label>
              <input id="telepon-siswa" type="tel" name="telepon_siswa" placeholder="Nomor telepon siswa" data-optional="true" />
            </div>
          </div>

          <div class="sub-block">
            <h4>11. Sekolah Asal</h4>
            <div class="form-grid">
              <div class="field">
                <label for="nama-sekolah">a. Nama Sekolah</label>
                <input id="nama-sekolah" type="text" name="nama_sekolah" placeholder="Nama sekolah asal" required />
              </div>

              <div class="field">
                <label for="alamat-sekolah">b. Alamat Sekolah</label>
                <input id="alamat-sekolah" type="text" name="alamat_sekolah" placeholder="Alamat sekolah asal" required />
              </div>
            </div>
          </div>

          <div class="sub-block">
            <h4>12. Nama Orang Tua</h4>
            <div class="form-grid">
              <div class="field">
                <label for="ayah">a. Ayah</label>
                <input id="ayah" type="text" name="nama_ayah" placeholder="Nama ayah" required />
              </div>

              <div class="field">
                <label for="ibu">b. Ibu</label>
                <input id="ibu" type="text" name="nama_ibu" placeholder="Nama ibu" required />
              </div>
            </div>
          </div>

          <div class="sub-block">
            <h4>13. Nomor Telepon Orang Tua</h4>
            <div class="field">
              <input id="telepon-orang-tua" type="tel" name="telepon_orang_tua" placeholder="Nomor telepon orang tua" required />
            </div>
          </div>

          <div class="sub-block">
            <h4>14. Alamat Orang Tua</h4>
            <div class="field">
              <textarea name="alamat_orang_tua" placeholder="Alamat lengkap orang tua" required></textarea>
            </div>
          </div>

          <div class="sub-block">
            <h4>15. Pekerjaan Orang Tua</h4>
            <div class="form-grid">
              <div class="field">
                <label for="pekerjaan-ayah">a. Ayah</label>
                <input id="pekerjaan-ayah" type="text" name="pekerjaan_ayah" placeholder="Pekerjaan ayah" required />
              </div>

              <div class="field">
                <label for="pekerjaan-ibu">b. Ibu</label>
                <input id="pekerjaan-ibu" type="text" name="pekerjaan_ibu" placeholder="Pekerjaan ibu" required />
              </div>
            </div>
          </div>

          <div class="sub-block">
            <h4>16. Nama Wali</h4>
            <div class="field">
              <input type="text" name="nama_wali" placeholder="Nama wali" data-optional="true" />
            </div>
          </div>

          <div class="sub-block">
            <h4>17. Alamat Wali</h4>
            <div class="field">
              <textarea name="alamat_wali" placeholder="Alamat lengkap wali" data-optional="true"></textarea>
            </div>
          </div>

          <div class="sub-block">
            <h4>18. Pekerjaan Wali</h4>
            <div class="field">
              <input type="text" name="pekerjaan_wali" placeholder="Pekerjaan wali" data-optional="true" />
            </div>
          </div>

          <div class="sub-block">
            <h4>19. Nomor Telepon Wali <small>(opsional)</small></h4>
            <div class="field">
              <input id="telepon-wali" type="tel" name="telepon_wali" placeholder="Nomor telepon wali" data-optional="true" />
            </div>
          </div>

          <div class="step-actions">
            <button type="button" class="tbl tbl-utama next-step" data-next="1">Lanjut</button>
          </div>
        </div>
      </div>

      <div class="form-step" data-step="2">
        <div class="form-panel">
          <h3>Data Pendukung</h3>
          <div class="upload-grid">
            <label class="upload-box">
              <span>Akta Kelahiran</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="akta" accept=".pdf,image/*" required />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Kartu Keluarga</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="kk" accept=".pdf,image/*" required />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Sertifikat / Prestasi</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="sertifikat" accept=".pdf,image/*" data-optional="true" />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Rapor / Nilai</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="rapor" accept=".pdf,image/*" required />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Foto Siswa</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="foto" accept="image/*" required />
              <small>Upload foto terbaru</small>
            </label>

            <label class="upload-box">
              <span>Dokumen Lainnya</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="dokumen_lain" accept=".pdf,image/*" data-optional="true" />
              <small>Upload dokumen tambahan</small>
            </label>
          </div>

          <div class="step-actions split">
            <button type="button" class="tbl tbl-garis prev-step">Kembali</button>
            <button type="button" class="tbl tbl-utama next-step" data-next="2">Lanjut</button>
          </div>
        </div>
      </div>

      <div class="form-step" data-step="3">
        <div class="form-panel">
          <h3>Tata cara pembayaran online dan offline</h3>

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

          <div class="rekening-box">
            <h3>Rekening &amp; Bukti Pembayaran</h3>
            <p><strong>Nomor rekening pendafataran:</strong></p>
            <div class="rek-row">
              <div class="field">
                <label for="bank">Bank</label>
                <input id="bank" type="text" name="bank" value="Bank Mandiri" readonly tabindex="-1" />
              </div>

              <div class="field">
                <label for="norek">Nomor Rekening</label>
                <input id="norek" type="text" name="norek" value="1234567890" readonly tabindex="-1" />
              </div>

              <div class="field">
                <label for="atas-nama">Atas Nama</label>
                <input id="atas-nama" type="text" name="atas_nama" value="YAYASAN MTS" readonly tabindex="-1" />
              </div>

              <div class="field">
                <label for="nomor-mts">Nomor MTS</label>
                <input id="nomor-mts" type="text" name="nomor_mts" value="MTS-001" readonly tabindex="-1" />
              </div>
            </div>
          </div>

          <div class="upload-grid upload-grid-mt">
            <label class="upload-box upload-box-single">
              <span>Bukti Pembayaran</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="bukti_pembayaran" accept=".pdf,image/*" required />
              <small>Upload bukti transfer atau pembayaran</small>
            </label>
          </div>

          <div class="step-actions split">
            <button type="button" class="tbl tbl-garis prev-step">Kembali</button>
            <button type="submit" class="tbl tbl-utama submit-confirm">Kirim Pendaftaran</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

<a class="wa-apung" href="<?= e(wa()) ?>" target="_blank" rel="noopener" aria-label="Hubungi panitia lewat WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.45 1.34 4.95L2 22l5.22-1.37a9.9 9.9 0 0 0 4.82 1.23h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2Zm5.8 14.06c-.24.68-1.42 1.31-1.95 1.36-.5.05-.98.24-3.3-.69-2.78-1.1-4.54-3.95-4.68-4.13-.13-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.25-.28.55-.35.73-.35h.52c.17 0 .4-.06.62.48.24.57.8 1.98.87 2.12.07.14.12.31.02.49-.09.18-.14.29-.28.45-.14.16-.29.35-.41.47-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.04.93 1.92 1.21 2.2 1.35.28.14.44.12.6-.07.17-.19.7-.81.88-1.09.18-.28.37-.23.62-.14.25.09 1.6.75 1.87.89.28.14.46.21.53.32.07.12.07.66-.17 1.34Z"/></svg>
  <span>Tanya panitia</span>
</a>


<script>
(function () {
  "use strict";

  var form = document.querySelector('.ppdb-form');
  if (!form) return;

  var steps = Array.prototype.slice.call(form.querySelectorAll('.form-step'));
  var stepItems = Array.prototype.slice.call(document.querySelectorAll('.step-item'));
  var stepLines = Array.prototype.slice.call(document.querySelectorAll('.step-line'));
  var nextButtons = Array.prototype.slice.call(form.querySelectorAll('.next-step'));
  var prevButtons = Array.prototype.slice.call(form.querySelectorAll('.prev-step'));
  var submitButton = form.querySelector('.submit-confirm');
  var paymentInput = form.querySelector('input[name="bukti_pembayaran"]');
  var currentStep = 0;

  function isOptionalField(field) {
    return field.dataset && field.dataset.optional === 'true';
  }

  function isFieldValid(field) {
    if (field.type === 'file') {
      if (field.required && !field.dataset.optional) {
        return !!(field.files && field.files.length > 0);
      }
      return true;
    }

    if (field.disabled || field.readOnly || isOptionalField(field)) {
      return !field.required || (field.value !== undefined && field.value.trim() === '') || field.checkValidity();
    }

    if (field.value === undefined) {
      return true;
    }

    if (field.required && field.value.trim() === '') {
      return false;
    }

    return field.checkValidity();
  }

  function getFields(stepEl) {
    return Array.prototype.slice.call(stepEl.querySelectorAll('input, select, textarea')).filter(function (field) {
      return !field.disabled && !field.readOnly;
    });
  }

  function isStepComplete(stepEl) {
    var fields = getFields(stepEl);
    return fields.every(function (field) {
      if (isOptionalField(field) && field.value.trim() === '') {
        return true;
      }

      return isFieldValid(field);
    });
  }

  function updateStepView() {
    steps.forEach(function (step, index) {
      step.classList.toggle('active', index === currentStep);
    });

    stepItems.forEach(function (item, index) {
      item.classList.toggle('active', index === currentStep);
      item.classList.toggle('completed', index < currentStep);
    });

    stepLines.forEach(function (line, index) {
      line.classList.toggle('active', index < currentStep);
    });
  }

  function validateAndAdvance(targetIndex) {
    if (targetIndex > currentStep) {
      var currentStepEl = steps[currentStep];
      var fields = getFields(currentStepEl);

      if (!fields.length) {
        currentStep = targetIndex;
        updateStepView();
        return;
      }

      var valid = true;
      fields.forEach(function (field) {
        var ok = isFieldValid(field);

        if (!ok) {
          valid = false;
          if (typeof field.reportValidity === 'function') {
            field.reportValidity();
          }
        }
      });

      if (!valid) return;
    }

    if (targetIndex >= 0 && targetIndex < steps.length) {
      currentStep = targetIndex;
      updateStepView();
    }
  }

  function syncSubmitState() {
    if (!submitButton || !paymentInput) return;

    var ready = !!(paymentInput.files && paymentInput.files.length > 0);
    submitButton.disabled = !ready;
    submitButton.title = ready ? 'Kirim pendaftaran' : 'Upload bukti pembayaran terlebih dahulu';
  }

  function saveDraft() {
    if (!window.localStorage) return;

    var draft = {};
    form.querySelectorAll('input, select, textarea').forEach(function (field) {
      if (!field.name || field.type === 'file') return;

      if (field.type === 'radio' || field.type === 'checkbox') {
        if (field.checked) {
          draft[field.name] = field.value;
        }
        return;
      }

      draft[field.name] = field.value;
    });

    localStorage.setItem('ppdb_form_draft', JSON.stringify(draft));
  }

  function restoreDraft() {
    if (!window.localStorage) return;

    try {
      var draft = JSON.parse(localStorage.getItem('ppdb_form_draft') || '{}');
    } catch (error) {
      return;
    }

    form.querySelectorAll('input, select, textarea').forEach(function (field) {
      if (!field.name || field.type === 'file') return;

      if (!(field.name in draft)) return;

      if (field.type === 'radio') {
        field.checked = String(draft[field.name]) === String(field.value);
        return;
      }

      if (field.type === 'checkbox') {
        field.checked = !!draft[field.name];
        return;
      }

      field.value = draft[field.name] || '';
    });
  }

  nextButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      validateAndAdvance(Number(button.dataset.next || currentStep + 1));
    });
  });

  prevButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      validateAndAdvance(currentStep - 1);
    });
  });

  form.addEventListener('submit', function (event) {
    if (!paymentInput || !paymentInput.files || !paymentInput.files.length) {
      event.preventDefault();
      if (paymentInput && typeof paymentInput.reportValidity === 'function') {
        paymentInput.reportValidity();
      }
      return;
    }

    if (!isStepComplete(steps[steps.length - 1])) {
      event.preventDefault();
      currentStep = steps.length - 1;
      updateStepView();
      var finalFields = getFields(steps[steps.length - 1]);
      finalFields.forEach(function (field) {
        if (field.type === 'file') {
          if (!field.files || field.files.length === 0) {
            if (typeof field.reportValidity === 'function') {
              field.reportValidity();
            }
          }
        } else if (!field.checkValidity() || field.value.trim() === '') {
          if (typeof field.reportValidity === 'function') {
            field.reportValidity();
          }
        }
      });
    }

    if (window.localStorage) {
      localStorage.removeItem('ppdb_form_draft');
    }
  });

  form.querySelectorAll('input, select, textarea').forEach(function (field) {
    if (field.type === 'file') return;
    field.addEventListener('input', saveDraft);
    field.addEventListener('change', saveDraft);
  });

  window.addEventListener('beforeunload', saveDraft);
  window.addEventListener('pagehide', saveDraft);

  function setupFilePreview(input) {
    var preview = input.closest('.upload-box').querySelector('.upload-preview');
    if (!preview) return;

    input.addEventListener('change', function () {
      preview.innerHTML = '';
      if (!input.files || !input.files.length) return;

      var file = input.files[0];
      var objectUrl = URL.createObjectURL(file);

      if (file.type && file.type.indexOf('image/') === 0) {
        preview.innerHTML = '<img src="' + objectUrl + '" alt="Preview file" />';
        return;
      }

      var ext = (file.name.split('.').pop() || 'file').toUpperCase();
      preview.innerHTML = '<div class="preview-file"><span class="preview-badge">' + ext + '</span><span class="preview-name">' + file.name + '</span></div>';
      setTimeout(function () { URL.revokeObjectURL(objectUrl); }, 0);
    });
  }

  form.querySelectorAll('input[type="file"]').forEach(function (input) {
    setupFilePreview(input);
  });

  if (paymentInput) {
    paymentInput.addEventListener('change', syncSubmitState);
  }

  restoreDraft();
  syncSubmitState();
  updateStepView();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

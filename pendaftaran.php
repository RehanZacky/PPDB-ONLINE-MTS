<?php
require __DIR__ . '/config.php';

function pdf_text(string $text): string
{
  $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: $text;
  return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
}

function form_select(string $id, string $name, string $label, array $options, bool $required = true, ?string $otherName = null): void
{
  $otherId = $id . '-lainnya';
  $accessibleLabel = $label !== '' ? $label : ucwords(str_replace('_', ' ', $name));
  echo '<div class="field">';
  if ($label !== '') {
    echo '<label for="' . e($id) . '">' . e($label) . '</label>';
  }
  echo '<select id="' . e($id) . '" name="' . e($name) . '" aria-label="' . e($accessibleLabel) . '"' . ($required ? ' required' : ' data-optional="true"');
  if ($otherName !== null) {
    echo ' data-other-target="' . e($otherId) . '"';
  }
  echo '><option value="">-- Pilih --</option>';
  foreach ($options as $option) {
    echo '<option value="' . e($option) . '">' . e($option) . '</option>';
  }
  echo '</select>';
  if ($otherName !== null) {
    echo '<div class="field other-field" id="' . e($otherId) . '" hidden><label for="' . e($otherId) . '-input">Tuliskan pilihan lainnya</label>';
    echo '<input id="' . e($otherId) . '-input" type="text" name="' . e($otherName) . '" placeholder="Isi pilihan lainnya" disabled></div>';
  }
  echo '</div>';
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
  $backgroundColor = $colorType === 3 ? substr($palette, 0, 3) : null;
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
        $color = substr($palette, $current[$x] * 3, 3);
        $rgbRow .= $color === $backgroundColor ? "\xff\xff\xff" : $color;
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
  $pdfTopOffset = 25;
  $value = static function (string $label) use ($data): string {
    return trim((string) ($data[$label] ?? ''));
  };
  $text = static function (string $font, float $size, float $x, float $y, string $content): string {
    return "BT /{$font} {$size} Tf {$x} {$y} Td (" . pdf_text(substr($content, 0, 82)) . ") Tj ET\n";
  };
  $dataText = static function (string $font, float $size, float $x, float $y, string $content) use ($text, $pdfTopOffset): string {
    return $text($font, $size, $x, $y + $pdfTopOffset, $content);
  };
  $line = static function (float $x1, float $y1, float $x2, float $y2, float $width = 1): string {
    return "{$width} w {$x1} {$y1} m {$x2} {$y2} l S\n";
  };
  $field = static function (string $label, string $fieldValue, float $y, string $prefix = '') use ($dataText): string {
    return $dataText('F2', 10, 55, $y, $prefix . $label) . $dataText('F1', 10, 220, $y, ': ' . ($fieldValue !== '' ? $fieldValue : '-'));
  };

  $logo = pdf_png_logo(__DIR__ . '/assets/img/logo.png');
  $logoMark = $logo !== null ? "q 90 0 0 90 45 700 cm /Im1 Do Q\n" : '';
  $header = $logoMark . $text('F2', 16, 155, 790, 'YAYASAN ROUDLOTUL QURAN AZ ZUHRI')
    . $text('F2', 17, 195, 766, 'MTS ROUDLOTUL QURAN')
    . $text('F1', 11, 165, 742, 'Desa Ngampelsari Rt. 03 Ngampelsari, Candi, Sidoarjo')
    . $text('F1', 10, 155, 726, 'Email: mtsroudlotulquran@gmail.com   Telepon: 081230294589')
    . $text('F1', 10, 150, 710, 'SK KEMENKUMHAM Nomor AHU-0027813.AH.01.04. Tahun 2022')
    . $line(45, 685, 550, 685, 1.2) . $line(45, 680, 550, 680, 3);

  $pageOne = $header . $text('F2', 18, 220, 640, 'DATA DIRI SISWA');
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
  $pageOne .= $dataText('F2', 10, 55, 350, '11. Sekolah Asal');
  $pageOne .= $field('Nama Sekolah', $value('Sekolah asal'), 329, 'a. ');
  $pageOne .= $field('Alamat Sekolah', $value('Alamat sekolah asal'), 308, 'b. ');
  $pageOne .= $dataText('F2', 10, 55, 273, '12. Nama Orang Tua');
  $pageOne .= $field('Ayah', $value('Nama ayah'), 252, 'a. ');
  $pageOne .= $field('Ibu', $value('Nama ibu'), 231, 'b. ');
  $pageOne .= $field('13. Telepon Orang Tua', $value('Telepon orang tua'), 195);
  $pageOne .= $field('14. Alamat Orang Tua', $value('Alamat orang tua'), 174);
  $pageOne .= $dataText('F2', 10, 55, 137, '15. Pekerjaan Orang Tua');
  $pageOne .= $field('Ayah', $value('Pekerjaan ayah'), 116, 'a. ');
  $pageOne .= $field('Ibu', $value('Pekerjaan ibu'), 95, 'b. ');
  $pageOne .= $field('16. Nama Wali', $value('Nama wali'), 71);
  $pageOne .= $field('17. Alamat Wali', $value('Alamat wali'), 50);
  $pageOne .= $field('18. Pekerjaan Wali', $value('Pekerjaan wali'), 29);
  $pageOne .= $field('19. Telepon Wali', $value('Telepon wali'), 8);

  $registrationPage = $header . $text('F2', 16, 210, 640, 'DATA PENDAFTARAN')
    . $field('Nomor pendaftaran', $registrationNumber, 590)
    . $field('Tanggal kirim', date('d-m-Y H:i'), 569)
    . $field('Kategori santri', $value('Status tempat tinggal siswa'), 548);
  $dataRows = [];
  foreach ($data as $label => $dataValue) {
    if (trim((string) $dataValue) !== '') {
      $dataRows[] = $label . ': ' . $dataValue;
    }
  }
  $dataPages = [];
  foreach (array_chunk($dataRows, 33) as $pageIndex => $pageRows) {
    $dataPage = $header . $text('F2', 16, 130, 650, $pageIndex === 0 ? 'DATA DIRI SISWA & KELUARGA' : 'DATA DIRI (LANJUTAN)');
    foreach ($pageRows as $rowIndex => $row) {
      $dataPage .= $text('F1', 9, 55, 620 - ($rowIndex * 18), $row);
    }
    $dataPages[] = $dataPage;
  }
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
  $pageImageResources = array_fill(0, count($dataPages) + 2, []);
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

  $pages = array_merge([$pageOne], $dataPages, [$registrationPage], $uploadedPages);
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

if (isset($_GET['download'])) {
  $downloadNumber = strtoupper((string) $_GET['download']);
  if (!preg_match('/^MTS-[0-9]{8}-[A-F0-9]{6}$/', $downloadNumber)) {
    http_response_code(404);
    exit('Berkas tidak ditemukan.');
  }
  $downloadPath = __DIR__ . '/uploads/pendaftaran/' . $downloadNumber . '/' . $downloadNumber . '.pdf';
  if (!is_file($downloadPath)) {
    http_response_code(404);
    exit('Berkas tidak ditemukan.');
  }
  header('Content-Type: application/pdf');
  header('Content-Disposition: attachment; filename="' . $downloadNumber . '.pdf"');
  header('Content-Length: ' . filesize($downloadPath));
  readfile($downloadPath);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $registrationNumber = 'MTS-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
  $storageDirectory = __DIR__ . '/uploads/pendaftaran/' . $registrationNumber;
  if (!is_dir($storageDirectory) && !mkdir($storageDirectory, 0755, true)) {
    http_response_code(500);
    exit('Folder penyimpanan pendaftaran tidak dapat dibuat.');
  }
  $labels = [
    'nama_siswa' => 'Nama siswa', 'program_pondok' => 'Program pondok', 'pendidikan_siswa' => 'Pendidikan terakhir',
    'pendidikan_siswa_lainnya' => 'Pendidikan terakhir lainnya', 'riwayat_penyakit' => 'Riwayat penyakit',
    'nisn' => 'NISN', 'nik_siswa' => 'NIK anak',
    'jenis_kelamin' => 'Jenis kelamin', 'tempat_tanggal_lahir' => 'Tempat dan tanggal lahir',
    'agama' => 'Agama', 'agama_lainnya' => 'Agama lainnya', 'anak_ke' => 'Anak ke', 'jumlah_saudara' => 'Jumlah saudara',
    'status_keluarga_siswa' => 'Status dalam keluarga', 'status_keluarga_siswa_lainnya' => 'Status keluarga siswa lainnya',
    'kategori_santri' => 'Status tempat tinggal siswa', 'status_keluarga' => 'Status keluarga',
    'provinsi_siswa' => 'Provinsi siswa', 'kabupaten_siswa' => 'Kabupaten/kota siswa', 'kecamatan_siswa' => 'Kecamatan siswa',
    'kelurahan_siswa' => 'Kelurahan/desa siswa', 'jalan_siswa' => 'Jalan/alamat siswa', 'rt_siswa' => 'RT siswa', 'rw_siswa' => 'RW siswa',
    'telepon_siswa' => 'Telepon siswa', 'transportasi' => 'Transportasi ke sekolah', 'transportasi_lainnya' => 'Transportasi lainnya',
    'nama_sekolah' => 'Nama sekolah', 'alamat_sekolah' => 'Alamat sekolah', 'npsn_sekolah' => 'NPSN sekolah', 'nsm_sekolah' => 'NSM sekolah',
    'tahun_ijazah' => 'Tahun ijazah', 'nomor_ijazah' => 'Nomor ijazah', 'kelas_diterima' => 'Kelas diterima', 'kelas_diterima_lainnya' => 'Kelas diterima lainnya',
    'tanggal_diterima' => 'Tanggal diterima', 'pembiaya_sekolah' => 'Yang membiayai sekolah', 'pembiaya_lainnya' => 'Pembiaya lainnya',
    'pencapaian_mengaji' => 'Pencapaian mengaji', 'pencapaian_lainnya' => 'Pencapaian lainnya', 'jumlah_juz' => 'Jumlah juz Al-Quran',
    'nama_ayah' => 'Nama ayah', 'nik_ayah' => 'NIK ayah', 'lahir_ayah' => 'Tempat/tanggal lahir ayah',
    'pendidikan_ayah' => 'Pendidikan ayah', 'pendidikan_ayah_lainnya' => 'Pendidikan ayah lainnya',
    'nama_ibu' => 'Nama ibu', 'nik_ibu' => 'NIK ibu', 'lahir_ibu' => 'Tempat/tanggal lahir ibu',
    'pendidikan_ibu' => 'Pendidikan ibu', 'pendidikan_ibu_lainnya' => 'Pendidikan ibu lainnya',
    'status_tempat_tinggal_orang_tua' => 'Status tempat tinggal orang tua', 'status_tempat_tinggal_orang_tua_lainnya' => 'Status tempat tinggal orang tua lainnya',
    'telepon_orang_tua' => 'Telepon orang tua', 'provinsi_orang_tua' => 'Provinsi orang tua', 'kabupaten_orang_tua' => 'Kabupaten/kota orang tua',
    'kecamatan_orang_tua' => 'Kecamatan orang tua', 'kelurahan_orang_tua' => 'Kelurahan/desa orang tua', 'jalan_orang_tua' => 'Jalan/alamat orang tua',
    'rt_orang_tua' => 'RT orang tua', 'rw_orang_tua' => 'RW orang tua',
    'pekerjaan_ayah' => 'Pekerjaan ayah', 'pekerjaan_ayah_lainnya' => 'Pekerjaan ayah lainnya',
    'pekerjaan_ibu' => 'Pekerjaan ibu', 'pekerjaan_ibu_lainnya' => 'Pekerjaan ibu lainnya',
    'nama_wali' => 'Nama wali', 'nik_wali' => 'NIK wali', 'lahir_wali' => 'Tempat/tanggal lahir wali',
    'status_keluarga_wali' => 'Status wali dalam keluarga', 'status_keluarga_wali_lainnya' => 'Status wali lainnya',
    'pendidikan_wali' => 'Pendidikan wali', 'pendidikan_wali_lainnya' => 'Pendidikan wali lainnya',
    'status_tempat_tinggal_wali' => 'Status tempat tinggal wali', 'status_tempat_tinggal_wali_lainnya' => 'Status tempat tinggal wali lainnya',
    'alamat_wali' => 'Alamat wali', 'pekerjaan_wali' => 'Pekerjaan wali', 'pekerjaan_wali_lainnya' => 'Pekerjaan wali lainnya', 'telepon_wali' => 'Telepon wali',
  ];
  $data = [];
  foreach ($labels as $key => $label) {
    $data[$label] = trim((string) ($_POST[$key] ?? ''));
  }
  $data['Nomor MTS'] = trim((string) ($_POST['nomor_mts'] ?? ''));
  $files = [];
  foreach (['kk' => 'Fotokopi Kartu Keluarga', 'akta' => 'Fotokopi akta kelahiran', 'ijazah' => 'Fotokopi ijazah/surat keterangan lulus', 'print_nisn' => 'Cetak NISN', 'ktp_orang_tua' => 'Fotokopi KTP orang tua', 'sktm' => 'SKTM', 'pas_foto' => 'Pas foto santri 4x6', 'persyaratan_nonmukim' => 'Persyaratan khusus non-mukim', 'bukti_pembayaran' => 'Bukti pembayaran'] as $key => $label) {
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
  header('Location: pendaftaran.php?success=' . rawurlencode($registrationNumber));
  exit;
}

$successRegistration = null;
if (isset($_GET['success'])) {
  $successNumber = strtoupper((string) $_GET['success']);
  $successPath = __DIR__ . '/uploads/pendaftaran/' . $successNumber . '/' . $successNumber . '.pdf';
  if (preg_match('/^MTS-[0-9]{8}-[A-F0-9]{6}$/', $successNumber) && is_file($successPath)) {
    $successRegistration = $successNumber;
  }
}

$PAGE = [
    'slug' => 'pendaftaran',
    'judul' => $successRegistration ? 'Pendaftaran Berhasil' : 'Formulir Pendaftaran SPMB',
    'deskripsi' => $successRegistration ? 'Pendaftaran berhasil dan PDF pendaftaran sudah disiapkan.' : 'Formulir pendaftaran calon santri baru MTs Tahfidh Roudlotul Qur\'an.',
];

include __DIR__ . '/includes/header.php';

if ($successRegistration !== null):
  $downloadUrl = 'pendaftaran.php?download=' . rawurlencode($successRegistration);
?>
<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Pendaftaran berhasil</span>
      <h2>Data pendaftaran sudah berhasil terisi</h2>
      <p>PDF pendaftaran sedang diunduh ke perangkat Anda. Simpan file tersebut sebelum melanjutkan.</p>
    </div>

    <div class="form-panel">
      <p><strong>Nomor pendaftaran: <?= e($successRegistration) ?></strong></p>
      <p class="arahan-wa"><strong>Penting:</strong> setelah PDF selesai diunduh, kirim file PDF tersebut secara manual melalui WhatsApp ke panitia agar pendaftaran dapat segera diproses.</p>
      <div class="step-actions">
        <a class="tbl tbl-garis" id="download-pdf" href="<?= e($downloadUrl) ?>" download>Download PDF lagi</a>
        <a class="tbl tbl-utama" href="<?= e(wa()) ?>" target="_blank" rel="noopener">Buka WhatsApp panitia</a>
      </div>
    </div>
  </div>
</section>
<script>
  window.addEventListener('load', function () {
    var downloadLink = document.getElementById('download-pdf');
    if (downloadLink) downloadLink.click();
  });
</script>
</main>
</body>
</html>
<?php
  exit;
endif;
?>

<section class="blok blok-mint">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Formulir Pendaftaran</span>
      <h2>Daftar calon santri baru</h2>
    </div>

    <form class="ppdb-form" action="pendaftaran.php" method="post" enctype="multipart/form-data" novalidate>
      <div class="step-indicator" aria-label="Tahap pendaftaran">
        <div class="step-item active" data-step="0">
          <span class="step-dot">1</span>
          <small>Data Diri</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item" data-step="1">
          <span class="step-dot">2</span>
          <small>Orang Tua/Wali</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item" data-step="2">
          <span class="step-dot">3</span>
          <small>Data Pendukung</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item" data-step="3">
          <span class="step-dot">4</span>
          <small>Bukti Pembayaran</small>
        </div>
      </div>

      <div class="form-step active" data-step="1">
        <div class="form-panel">
          <h3>A. Identitas Siswa</h3>

          <?php form_select('program-pondok', 'program_pondok', 'Program pondok', ['Tahfidh', 'Tahfidh & Sekolah']); ?>

          <div class="form-grid form-grid-student-final">
            <div class="field">
              <label for="nama-siswa">1. Nama Siswa</label>
              <input id="nama-siswa" type="text" name="nama_siswa" placeholder="Masukkan nama lengkap" required />
            </div>

            <div class="field">
              <label for="nisn">2. NISN</label>
              <input id="nisn" type="text" name="nisn" placeholder="NISN" required />
            </div>

            <div class="field">
              <label for="nik-siswa">3. NIK anak (di KK)</label>
              <input id="nik-siswa" type="text" name="nik_siswa" inputmode="numeric" required />
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

            <?php form_select('agama', 'agama', '6. Agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'], true, 'agama_lainnya'); ?>
            <?php form_select('pendidikan-siswa', 'pendidikan_siswa', 'Pendidikan terakhir', ['TK/RA', 'SD/MI', 'SMP/MTs', 'SMA/MA', 'Paket A', 'Paket B', 'Paket C', 'Lainnya'], true, 'pendidikan_siswa_lainnya'); ?>

            <div class="field">
              <label for="anak-ke">7. Anak Ke</label>
              <input id="anak-ke" type="text" name="anak_ke" placeholder="Anak ke-" required />
            </div>

            <div class="field">
              <label for="jumlah-saudara">Dari jumlah bersaudara</label>
              <input id="jumlah-saudara" type="number" name="jumlah_saudara" min="1" required />
            </div>

            <?php form_select('status-keluarga', 'status_keluarga_siswa', '8. Status dalam keluarga', ['Anak kandung', 'Anak angkat', 'Anak tiri', 'Lainnya'], true, 'status_keluarga_siswa_lainnya'); ?>

            <?php form_select('kategori-santri', 'kategori_santri', '9. Status tempat tinggal siswa', ['Mukim', 'Non Mukim']); ?>

            <div class="form-grid field-wide">
              <h4 class="field-wide">10. Alamat siswa (lengkap)</h4>
              <div class="field"><label for="provinsi-siswa">Provinsi</label><input id="provinsi-siswa" name="provinsi_siswa" type="text" required></div>
              <div class="field"><label for="kabupaten-siswa">Kabupaten/kota</label><input id="kabupaten-siswa" name="kabupaten_siswa" type="text" required></div>
              <div class="field"><label for="kecamatan-siswa">Kecamatan</label><input id="kecamatan-siswa" name="kecamatan_siswa" type="text" required></div>
              <div class="field"><label for="kelurahan-siswa">Kelurahan/desa</label><input id="kelurahan-siswa" name="kelurahan_siswa" type="text" required></div>
              <div class="field field-wide"><label for="jalan-siswa">Jalan / alamat</label><input id="jalan-siswa" name="jalan_siswa" type="text" required></div>
              <div class="field"><label for="rt-siswa">RT</label><input id="rt-siswa" name="rt_siswa" type="text" required></div>
              <div class="field"><label for="rw-siswa">RW</label><input id="rw-siswa" name="rw_siswa" type="text" required></div>
            </div>

            <?php form_select('transportasi', 'transportasi', '11. Transportasi ke sekolah', ['Jalan kaki', 'Sepeda', 'Mobil pribadi', 'Antar jemput sekolah', 'Angkutan umum', 'Lainnya'], true, 'transportasi_lainnya'); ?>

            <div class="field">
              <label for="telepon-siswa">Nomor telepon siswa <small>(opsional)</small></label>
              <input id="telepon-siswa" type="tel" name="telepon_siswa" placeholder="Nomor telepon siswa" required />
            </div>
          </div>

          <div class="sub-block">
            <h4>12. Sekolah asal</h4>
            <div class="form-grid">
              <div class="field">
                <label for="nama-sekolah">a. Nama Sekolah</label>
                <input id="nama-sekolah" type="text" name="nama_sekolah" placeholder="Nama sekolah asal" required />
              </div>

              <div class="field">
                <label for="alamat-sekolah">b. Alamat Sekolah</label>
                <input id="alamat-sekolah" type="text" name="alamat_sekolah" placeholder="Alamat sekolah asal" required />
              </div>

              <div class="field">
                <label for="npsn-sekolah">c. NPSN Sekolah</label>
                <input id="npsn-sekolah" type="text" name="npsn_sekolah" inputmode="numeric" required />
              </div>
              <div class="field">
                <label for="nsm-sekolah">d. NSM Sekolah</label>
                <input id="nsm-sekolah" type="text" name="nsm_sekolah" inputmode="numeric" required />
              </div>
            </div>
          </div>

            <div class="sub-block">
            <h4>13. Surat tanda tamat belajar</h4>
            <div class="form-grid">
              <div class="field"><label for="tahun-ijazah">Tahun</label><input id="tahun-ijazah" name="tahun_ijazah" type="number" min="1980" max="2100" required></div>
              <div class="field"><label for="nomor-ijazah">Nomor</label><input id="nomor-ijazah" name="nomor_ijazah" type="text" required></div>
              <?php form_select('kelas-diterima', 'kelas_diterima', '14. Diterima di kelas', ['VII', 'VIII', 'IX', 'Lainnya'], true, 'kelas_diterima_lainnya'); ?>
              <div class="field"><label for="tanggal-diterima">Pada tanggal</label><input id="tanggal-diterima" name="tanggal_diterima" type="date" required></div>
            </div>
          </div>

          <div class="sub-block">
            <h4>Riwayat penyakit <small>(jika ada)</small></h4>
            <div class="field">
              <label for="riwayat-penyakit">Sebutkan riwayat penyakit</label>
              <textarea id="riwayat-penyakit" name="riwayat_penyakit" placeholder="Isi jika ada, atau kosongkan"></textarea>
            </div>
          </div>

          <div class="form-grid">
            <?php form_select('pembiaya-sekolah', 'pembiaya_sekolah', '15. Yang membiayai sekolah', ['Orang tua', 'Wali/orang tua asuh', 'Lainnya'], true, 'pembiaya_lainnya'); ?>
            <?php form_select('pencapaian-mengaji', 'pencapaian_mengaji', '16. Jilid pencapaian mengaji', ['Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Jilid 5', 'Jilid 6', 'Al-Quran', 'Khatam Al-Quran', 'Lainnya'], true, 'pencapaian_lainnya'); ?>
            <div class="field" id="jumlah-juz-field" hidden><label for="jumlah-juz">Jumlah juz Al-Quran</label><input id="jumlah-juz" name="jumlah_juz" type="number" min="1" max="30" disabled></div>
            <div class="field">
              <label for="status-keluarga-sosial">17. Status keluarga</label>
              <select id="status-keluarga-sosial" name="status_keluarga" required>
                <option value="">-- Pilih --</option>
                <option>Mampu</option><option>Kurang mampu</option><option>Anak yatim</option>
                <option>Anak piatu</option><option>Anak yatim piatu</option><option>Anak panti asuhan</option>
              </select>
            </div>
          </div>

          <div class="step-actions">
            <button type="button" class="tbl tbl-utama next-step" data-next="1">Lanjut</button>
            <button type="button" class="tbl tbl-garis skip-step" data-next="1">Lewati</button>
          </div>
        </div>
      </div>

      <div class="form-step" data-step="2">
        <div class="form-panel">
          <div class="sub-block">
            <h3>B. Identitas Orang Tua</h3>
            <h4>Identitas ayah</h4>
            <div class="form-grid">
              <div class="field">
                <label for="ayah">1. Nama ayah kandung (sesuai KK)</label>
                <input id="ayah" type="text" name="nama_ayah" placeholder="Nama ayah" required />
              </div>

              <div class="field"><label for="nik-ayah">2. NIK ayah</label><input id="nik-ayah" name="nik_ayah" type="text" inputmode="numeric" required></div>
              <div class="field"><label for="lahir-ayah">3. Tempat dan tanggal lahir ayah</label><input id="lahir-ayah" name="lahir_ayah" type="text" required></div>
              <?php form_select('pendidikan-ayah', 'pendidikan_ayah', '4. Pendidikan terakhir ayah', ['Tidak bersekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Lainnya'], true, 'pendidikan_ayah_lainnya'); ?>
              <?php form_select('pekerjaan-ayah', 'pekerjaan_ayah', '5. Pekerjaan utama ayah', ['Tidak bekerja', 'Pensiun', 'PNS', 'TNI/Polisi', 'Guru/Dosen', 'Pegawai swasta', 'Wiraswasta', 'Pengacara/Jaksa/Hakim/Notaris', 'Seniman/Pelukis/Artis/Sejenis', 'Dokter/Bidan/Perawat', 'Pilot/Pramugara', 'Pedagang', 'Petani/Peternak', 'Nelayan', 'Buruh (Tani, Pabrik/Bangunan)', 'Sopir/Masinis/Kondektur', 'Politikus', 'Lainnya'], true, 'pekerjaan_ayah_lainnya'); ?>
            </div>

            <h4 class="ibu-heading">Identitas ibu</h4>
            <div class="form-grid">
              <div class="field">
                <label for="ibu">6. Nama ibu kandung (sesuai KK)</label>
                <input id="ibu" type="text" name="nama_ibu" placeholder="Nama ibu" required />
              </div>
              <div class="field"><label for="nik-ibu">7. NIK ibu</label><input id="nik-ibu" name="nik_ibu" type="text" inputmode="numeric" required></div>
              <div class="field"><label for="lahir-ibu">8. Tempat dan tanggal lahir ibu</label><input id="lahir-ibu" name="lahir_ibu" type="text" required></div>
              <?php form_select('pendidikan-ibu', 'pendidikan_ibu', '9. Pendidikan terakhir ibu', ['Tidak bersekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Lainnya'], true, 'pendidikan_ibu_lainnya'); ?>
              <?php form_select('pekerjaan-ibu', 'pekerjaan_ibu', '10. Pekerjaan utama ibu', ['Tidak bekerja', 'Pensiun', 'PNS', 'TNI/Polisi', 'Guru/Dosen', 'Pegawai swasta', 'Wiraswasta', 'Pengacara/Jaksa/Hakim/Notaris', 'Seniman/Pelukis/Artis/Sejenis', 'Dokter/Bidan/Perawat', 'Pilot/Pramugara', 'Pedagang', 'Petani/Peternak', 'Nelayan', 'Buruh (Tani, Pabrik/Bangunan)', 'Sopir/Masinis/Kondektur', 'Politikus', 'Lainnya'], true, 'pekerjaan_ibu_lainnya'); ?>
            </div>
          </div>

          <div class="sub-block">
            <h4>11. Status tempat tinggal orang tua</h4>
            <?php form_select('tinggal-orang-tua', 'status_tempat_tinggal_orang_tua', '', ['Milik sendiri', 'Rumah orang tua', 'Rumah saudara/kerabat', 'Rumah dinas', 'Sewa/kontrak', 'Lainnya'], true, 'status_tempat_tinggal_orang_tua_lainnya'); ?>
          </div>

          <div class="sub-block">
            <h4>12. Alamat orang tua</h4>
            <div class="form-grid">
              <div class="field"><label for="provinsi-orang-tua">Provinsi</label><input id="provinsi-orang-tua" name="provinsi_orang_tua" type="text" required></div>
              <div class="field"><label for="kabupaten-orang-tua">Kabupaten/kota</label><input id="kabupaten-orang-tua" name="kabupaten_orang_tua" type="text" required></div>
              <div class="field"><label for="kecamatan-orang-tua">Kecamatan</label><input id="kecamatan-orang-tua" name="kecamatan_orang_tua" type="text" required></div>
              <div class="field"><label for="kelurahan-orang-tua">Kelurahan/desa</label><input id="kelurahan-orang-tua" name="kelurahan_orang_tua" type="text" required></div>
              <div class="field field-wide"><label for="jalan-orang-tua">Jalan / alamat lengkap</label><input id="jalan-orang-tua" name="jalan_orang_tua" type="text" required></div>
              <div class="field"><label for="rt-orang-tua">RT</label><input id="rt-orang-tua" name="rt_orang_tua" type="text" required></div>
              <div class="field"><label for="rw-orang-tua">RW</label><input id="rw-orang-tua" name="rw_orang_tua" type="text" required></div>
            </div>
          </div>

          <div class="sub-block">
            <h4>13. Nomor telepon orang tua</h4>
            <div class="field"><input id="telepon-orang-tua" type="tel" name="telepon_orang_tua" placeholder="Nomor telepon orang tua" required /></div>
          </div>

          <div class="sub-block">
            <h4>Identitas wali (bila ada)</h4>
            <div class="form-grid">
              <div class="field"><label for="nama-wali">14. Nama wali</label><input id="nama-wali" type="text" name="nama_wali" data-optional="true" /></div>
              <div class="field"><label for="nik-wali">15. NIK wali</label><input id="nik-wali" type="text" name="nik_wali" inputmode="numeric" data-optional="true" /></div>
              <div class="field"><label for="lahir-wali">16. Tempat dan tanggal lahir wali</label><input id="lahir-wali" type="text" name="lahir_wali" data-optional="true" /></div>
              <?php form_select('status-keluarga-wali', 'status_keluarga_wali', '17. Status wali dalam keluarga', ['Ayah', 'Ibu', 'Saudara', 'Kerabat', 'Lainnya'], false, 'status_keluarga_wali_lainnya'); ?>
              <?php form_select('pendidikan-wali', 'pendidikan_wali', '18. Pendidikan terakhir wali', ['Tidak bersekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3', 'Lainnya'], false, 'pendidikan_wali_lainnya'); ?>
              <?php form_select('pekerjaan-wali', 'pekerjaan_wali', '19. Pekerjaan utama wali', ['Tidak bekerja', 'Pensiun', 'PNS', 'TNI/Polisi', 'Guru/Dosen', 'Pegawai swasta', 'Wiraswasta', 'Pengacara/Jaksa/Hakim/Notaris', 'Seniman/Pelukis/Artis/Sejenis', 'Dokter/Bidan/Perawat', 'Pilot/Pramugara', 'Pedagang', 'Petani/Peternak', 'Nelayan', 'Buruh (Tani, Pabrik/Bangunan)', 'Sopir/Masinis/Kondektur', 'Politikus', 'Lainnya'], false, 'pekerjaan_wali_lainnya'); ?>
              <?php form_select('tinggal-wali', 'status_tempat_tinggal_wali', '20. Status tempat tinggal wali', ['Milik sendiri', 'Rumah orang tua', 'Rumah saudara/kerabat', 'Rumah dinas', 'Sewa/kontrak', 'Lainnya'], false, 'status_tempat_tinggal_wali_lainnya'); ?>
            </div>
          </div>

          <div class="sub-block">
            <h4>21. Alamat wali</h4>
            <div class="field">
              <textarea name="alamat_wali" placeholder="Alamat lengkap wali" data-optional="true"></textarea>
            </div>
          </div>

          <div class="sub-block">
            <h4>22. Nomor telepon wali <small>(opsional)</small></h4>
            <div class="field">
              <input id="telepon-wali" type="tel" name="telepon_wali" placeholder="Nomor telepon wali" data-optional="true" />
            </div>
          </div>

          <div class="step-actions">
            <button type="button" class="tbl tbl-garis prev-step">Kembali</button>
            <button type="button" class="tbl tbl-utama next-step" data-next="2">Lanjut</button>
            <button type="button" class="tbl tbl-garis skip-step" data-next="2">Lewati</button>
          </div>
        </div>
      </div>

      <div class="form-step" data-step="3">
        <div class="form-panel">
          <h3>Data Pendukung</h3>
          <div class="upload-grid">
            <label class="upload-box">
              <span>1. Fotokopi Kartu Keluarga (1 lembar)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="kk" accept=".pdf,image/*" required />
              <small>Unggah PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>2. Fotokopi akta kelahiran (1 lembar)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="akta" accept=".pdf,image/*" required />
              <small>Unggah PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>3. Fotokopi ijazah / surat keterangan lulus (1 lembar)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="ijazah" accept=".pdf,image/*" required />
              <small>Unggah PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>4. Cetak NISN dari website</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="print_nisn" accept=".pdf,image/*" required />
              <small>Unggah hasil cetak PDF atau foto</small>
            </label>

            <label class="upload-box" id="sktm-upload-box" hidden>
              <span>5. SKTM dari desa (bagi keluarga tidak mampu)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="sktm" accept=".pdf,image/*" disabled />
              <small>Unggah surat keterangan tidak mampu</small>
            </label>

            <label class="upload-box">
              <span>6. Fotokopi KTP orang tua (dijadikan 1 lembar dan diperbesar)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="ktp_orang_tua" accept=".pdf,image/*" required />
              <small>Unggah satu file gabungan PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>7. Pas foto santri ukuran 4x6 (1 lembar)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="pas_foto" accept="image/*" required />
              <small>Unggah foto ukuran 4x6</small>
            </label>

            <label class="upload-box" id="nonmukim-upload-box" hidden>
              <span>7. Persyaratan khusus (bagi siswa non-mukim)</span>
              <div class="upload-preview" aria-live="polite"></div>
              <input type="file" name="persyaratan_nonmukim" accept=".pdf,image/*" disabled />
              <small>Unggah dokumen persyaratan khusus</small>
            </label>
          </div>

          <div class="step-actions split">
            <button type="button" class="tbl tbl-garis prev-step">Kembali</button>
            <button type="button" class="tbl tbl-utama next-step" data-next="3">Lanjut</button>
            <button type="button" class="tbl tbl-garis skip-step" data-next="3">Lewati</button>
          </div>
        </div>
      </div>

      <div class="form-step" data-step="4">
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
    <dialog class="konfirmasi-dialog" id="konfirmasi-pendaftaran" aria-labelledby="judul-konfirmasi">
      <h2 id="judul-konfirmasi">Periksa kembali data pendaftaran</h2>
      <p>Apakah Anda yakin data yang diisi sudah sesuai dan ingin mengirim pendaftaran?</p>
      <div class="konfirmasi-actions">
        <button type="button" class="tbl tbl-garis" id="batal-kirim">Periksa kembali</button>
        <button type="button" class="tbl tbl-utama" id="konfirmasi-kirim">Ya, kirim pendaftaran</button>
      </div>
    </dialog>
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
  var skipButtons = Array.prototype.slice.call(form.querySelectorAll('.skip-step'));
  var prevButtons = Array.prototype.slice.call(form.querySelectorAll('.prev-step'));
  var submitButton = form.querySelector('.submit-confirm');
  var paymentInput = form.querySelector('input[name="bukti_pembayaran"]');
  var confirmationDialog = document.getElementById('konfirmasi-pendaftaran');
  var confirmSubmitButton = document.getElementById('konfirmasi-kirim');
  var cancelSubmitButton = document.getElementById('batal-kirim');
  var currentStep = 0;
  var skipValidation = false;
  var submissionConfirmed = false;

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

  function scrollToFormStart() {
    var header = document.querySelector('.atas');
    var headerOffset = header ? header.getBoundingClientRect().height : 0;
    var top = form.getBoundingClientRect().top + window.pageYOffset - headerOffset - 12;
    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
  }

  function updateStepView(shouldScroll) {
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

    if (shouldScroll) {
      window.requestAnimationFrame(scrollToFormStart);
    }
  }

  function validateAndAdvance(targetIndex) {
    if (targetIndex > currentStep) {
      var currentStepEl = steps[currentStep];
      var fields = getFields(currentStepEl);

      if (!fields.length) {
        currentStep = targetIndex;
        updateStepView(true);
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
      updateStepView(true);
    }
  }

  function syncSubmitState() {
    if (!submitButton || !paymentInput) return;

    var ready = skipValidation || !!(paymentInput.files && paymentInput.files.length > 0);
    submitButton.disabled = !ready;
    submitButton.title = skipValidation ? 'Kirim untuk cek hasil dan PDF' : (ready ? 'Kirim pendaftaran' : 'Upload bukti pembayaran terlebih dahulu');
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

  function syncConditionalFields() {
    form.querySelectorAll('select[data-other-target]').forEach(function (select) {
      var otherField = document.getElementById(select.dataset.otherTarget);
      if (!otherField) return;

      var otherInput = otherField.querySelector('input');
      var showOther = select.value === 'Lainnya';
      otherField.hidden = !showOther;
      if (otherInput) {
        otherInput.disabled = !showOther;
        otherInput.required = showOther;
      }
    });

    var achievement = form.querySelector('#pencapaian-mengaji');
    var juzField = form.querySelector('#jumlah-juz-field');
    var juzInput = form.querySelector('#jumlah-juz');
    var showJuz = !!achievement && achievement.value === 'Al-Quran';
    if (juzField && juzInput) {
      juzField.hidden = !showJuz;
      juzInput.disabled = !showJuz;
      juzInput.required = showJuz;
    }

    var familyStatus = form.querySelector('#status-keluarga-sosial');
    var sktmBox = form.querySelector('#sktm-upload-box');
    var sktmInput = form.querySelector('input[name="sktm"]');
    if (sktmBox && sktmInput) {
      sktmBox.hidden = false;
      sktmInput.disabled = false;
      sktmInput.required = false;
    }

    var studentType = form.querySelector('#kategori-santri');
    var nonresidentBox = form.querySelector('#nonmukim-upload-box');
    var nonresidentInput = form.querySelector('input[name="persyaratan_nonmukim"]');
    var showNonresident = !!studentType && studentType.value === 'Non Mukim';
    if (nonresidentBox && nonresidentInput) {
      nonresidentBox.hidden = !showNonresident;
      nonresidentInput.disabled = !showNonresident;
      nonresidentInput.required = showNonresident;
    }
  }

  nextButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      validateAndAdvance(Number(button.dataset.next || currentStep + 1));
    });
  });

  skipButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var targetIndex = Number(button.dataset.next);
      if (targetIndex >= 0 && targetIndex < steps.length) {
        skipValidation = true;
        currentStep = targetIndex;
        updateStepView(true);
        syncSubmitState();
      }
    });
  });




  prevButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      validateAndAdvance(currentStep - 1);
    });
  });
  form.addEventListener('submit', function (event) {
    if (submissionConfirmed) {
      submissionConfirmed = false;
      if (window.localStorage) localStorage.removeItem('ppdb_form_draft');
      return;
    }

    if (!skipValidation && (!paymentInput || !paymentInput.files || !paymentInput.files.length)) {
      event.preventDefault();
      if (paymentInput && typeof paymentInput.reportValidity === 'function') {
        paymentInput.reportValidity();
      }
      return;
    }

    if (!skipValidation && !isStepComplete(steps[steps.length - 1])) {
      event.preventDefault();
      currentStep = steps.length - 1;
      updateStepView(true);
      var finalFields = getFields(steps[steps.length - 1]);
      finalFields.forEach(function (field) {
        if (field.type === 'file') {
          if (field.files && field.files.length > 0) return;
        } else if (field.checkValidity() && field.value.trim() !== '') {
          return;
        }
        if (typeof field.reportValidity === 'function') {
          field.reportValidity();
        }
      });
      return;
    }

    event.preventDefault();
    if (confirmationDialog && !confirmationDialog.open) confirmationDialog.showModal();
  });

  if (cancelSubmitButton && confirmationDialog) {
    cancelSubmitButton.addEventListener('click', function () {
      confirmationDialog.close();
      if (submitButton) submitButton.focus();
    });
  }

  if (confirmSubmitButton && confirmationDialog) {
    confirmSubmitButton.addEventListener('click', function () {
      confirmationDialog.close();
      submissionConfirmed = true;
      form.requestSubmit();
    });
  }

  form.querySelectorAll('input, select, textarea').forEach(function (field) {
    if (field.type === 'file') return;
    field.addEventListener('input', saveDraft);
    field.addEventListener('change', saveDraft);
  });

  form.addEventListener('change', syncConditionalFields);

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
  syncConditionalFields();
  syncSubmitState();
  updateStepView();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
/* ==========================================================================
   config.php — SATU-SATUNYA FILE YANG PERLU DIEDIT RUTIN
   Semua teks, tanggal, dan angka di halaman SPMB diambil dari sini.
   ========================================================================== */

/* --------------------------------------------------------------------------
   1. IDENTITAS LEMBAGA
   -------------------------------------------------------------------------- */
$SITE = [
    'yayasan'       => 'Yayasan Roudlotul Qur\'an Az Zuhri',
    'lembaga'       => 'Pon.Pes & MTs Tahfidh Roudlotul Qur\'an',
    'singkat'       => 'MTs Tahfidh Roudlotul Qur\'an',
    'tahun_ajaran'  => '2027/2028',

    // Path logo relatif terhadap folder /spmb. Salin logo dari situs profil ke assets/img/logo.png
    'logo'          => 'assets/img/logo.png',

    // Alamat situs profil yang sudah ada (untuk menu Beranda, Profil, dll.)
    'url_profil'    => 'https://mtstahfidhroudlotulquran.sch.id',

    // Kontak panitia
    'wa'            => '6281234567890',        // format internasional, tanpa + dan tanpa 0 di depan
    'wa_tampil'     => '0812-3456-7890',
    'wa_nama'       => 'Ustaz Fauzi',
    'wa2'           => '6281234567891',
    'wa2_tampil'    => '0812-3456-7891',
    'wa2_nama'      => 'Ustazah Aisyah',
    'email'         => 'spmb@mtstahfidhroudlotulquran.sch.id',
    'alamat'        => 'Jl. Raya Pesantren No. 12, Kec. Wonokromo, Surabaya, Jawa Timur 60243',
    'maps'          => 'https://maps.google.com/?q=MTs+Tahfidh+Roudlotul+Quran',
    'jam_layanan'   => 'Senin – Sabtu, 08.00 – 15.00 WIB',

    // Link formulir pendaftaran. Isi URL Google Form, atau biarkan '#' untuk
    // mengarahkan calon pendaftar ke WhatsApp.
    'link_formulir' => '#',

    // Angka ringkas yang tampil di halaman depan SPMB
    'kuota'         => 40,
    'rombel'        => 2,
    'target_juz'    => 10,
    'rasio'         => '1 : 12',
];

/* --------------------------------------------------------------------------
   2. GELOMBANG PENDAFTARAN
   Status (belum dibuka / dibuka / ditutup) dihitung otomatis dari tanggal,
   jadi cukup ubah tanggalnya setiap tahun ajaran baru.
   -------------------------------------------------------------------------- */
$GELOMBANG = [
    [
        'nama'     => 'Gelombang 1',
        'mulai'    => '2026-09-01',
        'selesai'  => '2026-11-30',
        'potongan' => 1000000,
        'catatan'  => 'Potongan terbesar dan pilihan kamar asrama paling leluasa.',
    ],
    [
        'nama'     => 'Gelombang 2',
        'mulai'    => '2026-12-01',
        'selesai'  => '2027-02-28',
        'potongan' => 500000,
        'catatan'  => 'Kuota santri mukim biasanya mulai menipis di gelombang ini.',
    ],
    [
        'nama'     => 'Gelombang 3',
        'mulai'    => '2027-03-01',
        'selesai'  => '2027-05-31',
        'potongan' => 0,
        'catatan'  => 'Hanya dibuka bila kuota masih tersisa.',
    ],
];

/* --------------------------------------------------------------------------
   3. RINCIAN BIAYA
   -------------------------------------------------------------------------- */
$BIAYA = [
    'mukim' => [
        'label'    => 'Santri Mukim',
        'ket'      => 'Tinggal di asrama pesantren',
        'awal'     => [
            ['Biaya pendaftaran', 250000, ''],
            ['Daftar ulang dan administrasi', 750000, ''],
            ['Seragam', 1100000, '4 stel, batik, olahraga, dan kerudung'],
            ['Kitab dan buku pelajaran', 600000, ''],
            ['Perlengkapan asrama', 800000, 'kasur, bantal, lemari'],
            ['Infak pengembangan', 2500000, 'komponen yang dipotong sesuai gelombang'],
        ],
        'bulanan'  => [
            ['Syahriah (SPP)', 350000, ''],
            ['Asrama dan makan tiga kali', 600000, ''],
            ['Laundry dan kesehatan', 75000, ''],
        ],
    ],
    'nonmukim' => [
        'label'    => 'Santri Non-Mukim',
        'ket'      => 'Pulang-pergi dari rumah',
        'awal'     => [
            ['Biaya pendaftaran', 250000, ''],
            ['Daftar ulang dan administrasi', 600000, ''],
            ['Seragam', 1100000, '4 stel, batik, olahraga, dan kerudung'],
            ['Kitab dan buku pelajaran', 500000, ''],
            ['Infak pengembangan', 1750000, 'komponen yang dipotong sesuai gelombang'],
        ],
        'bulanan'  => [
            ['Syahriah (SPP)', 275000, ''],
            ['Madrasah diniyah sore', 75000, ''],
            ['Kegiatan dan ekstrakurikuler', 50000, ''],
        ],
    ],
];

$BEASISWA = [
    'Hafal 5 juz atau lebih dengan sanad yang diuji panitia: bebas seluruh infak pengembangan.',
    'Juara 1 sampai 3 lomba tingkat kabupaten ke atas: potongan 50 persen infak pengembangan.',
    'Yatim, piatu, atau pemegang KIP/PKH: keringanan ditentukan setelah survei panitia.',
    'Anak kedua dan seterusnya dari satu keluarga: potongan syahriah 25 persen.',
    'Biaya awal dapat dicicil sampai tiga kali hingga awal tahun ajaran.',
];

/* --------------------------------------------------------------------------
   4. ALUR PENDAFTARAN
   -------------------------------------------------------------------------- */
$ALUR = [
    [
        'judul' => 'Isi formulir pendaftaran',
        'isi'   => 'Bisa lewat formulir online di halaman ini atau datang langsung ke kantor panitia. Siapkan nama lengkap calon santri, NISN, dan nomor WhatsApp wali yang aktif.',
        'meta'  => 'Sekitar 10 menit',
    ],
    [
        'judul' => 'Bayar biaya pendaftaran',
        'isi'   => 'Rp250.000, ditransfer ke rekening panitia atau dibayar tunai di kantor. Kirim bukti pembayaran ke WhatsApp panitia untuk menerima nomor pendaftaran.',
        'meta'  => 'Nomor pendaftaran terbit di hari yang sama',
    ],
    [
        'judul' => 'Serahkan berkas',
        'isi'   => 'Unggah hasil pindai berkas lewat tautan dari panitia, atau antar map berkas ke kantor. Daftar lengkapnya ada di bagian bawah halaman ini.',
        'meta'  => 'Paling lambat tiga hari sebelum tes',
    ],
    [
        'judul' => 'Ikut tes seleksi',
        'isi'   => 'Tes dilaksanakan setiap Sabtu pukul 08.00. Ada tiga bagian: membaca Al-Qur\'an, tes tulis Matematika dan Bahasa Indonesia setingkat SD, serta wawancara calon santri bersama wali.',
        'meta'  => 'Sekitar 2 jam, wali wajib hadir',
    ],
    [
        'judul' => 'Terima pengumuman',
        'isi'   => 'Hasil dikirim lewat WhatsApp ke nomor wali tiga hari setelah tes, dan ditempel di papan pengumuman kantor panitia.',
        'meta'  => 'Tiga hari setelah tes',
    ],
    [
        'judul' => 'Daftar ulang',
        'isi'   => 'Lunasi biaya awal atau ambil skema cicilan, ukur seragam, lalu terima jadwal masuk asrama. Calon santri yang tidak daftar ulang sampai batas waktu dianggap mengundurkan diri.',
        'meta'  => 'Maksimal 14 hari setelah pengumuman',
    ],
];

$SYARAT = [
    ['Fotokopi ijazah atau surat keterangan lulus SD/MI', '2 lembar, boleh menyusul'],
    ['Fotokopi rapor SD/MI semester 1 sampai 5', 'dilegalisir sekolah asal'],
    ['Fotokopi akta kelahiran', '2 lembar'],
    ['Fotokopi kartu keluarga dan KTP kedua orang tua', 'masing-masing 2 lembar'],
    ['Pas foto berwarna 3×4', '4 lembar, latar biru'],
    ['Surat keterangan sehat dari puskesmas', 'khusus calon santri mukim'],
    ['Fotokopi KIP, KKS, atau kartu PKH', 'bila mengajukan keringanan biaya'],
    ['Fotokopi sertifikat prestasi', 'bila mengajukan beasiswa prestasi'],
];

/* --------------------------------------------------------------------------
   5. PROGRAM DAN TANYA JAWAB
   -------------------------------------------------------------------------- */
$PROGRAM = [
    [
        'nama'    => 'Santri Mukim',
        'ket'     => 'Tinggal di asrama pesantren',
        'unggul'  => true,
        'isi'     => 'Untuk wali yang ingin anaknya dibina penuh waktu: bangun, salat, menghafal, belajar, dan istirahat dalam satu ritme yang dijaga pengasuh.',
        'poin'    => [
            'Setoran hafalan dua kali sehari, bakda Subuh dan bakda Asar',
            'Target 10 juz selama tiga tahun, kelas takhassus untuk 30 juz',
            'Kajian kitab kuning bakda Magrib',
            'Makan tiga kali sehari, laundry, dan poliklinik',
            'Pendampingan wali asrama untuk tiap 12 santri',
        ],
    ],
    [
        'nama'    => 'Santri Non-Mukim',
        'ket'     => 'Pulang-pergi dari rumah',
        'unggul'  => false,
        'isi'     => 'Untuk keluarga yang tinggal dekat madrasah dan tetap ingin anaknya mendapat pembinaan tahfidh serta diniyah sore.',
        'poin'    => [
            'Kelas MTs pukul 07.00 sampai 14.00',
            'Madrasah diniyah dan setoran hafalan pukul 14.30 sampai 16.00',
            'Target 5 juz selama tiga tahun',
            'Boleh menginap saat pekan ujian dan kegiatan besar',
            'Ekstrakurikuler pramuka, pencak silat, dan hadrah',
        ],
    ],
];

$FAQ = [
    [
        'Apakah lulusan SD negeri boleh mendaftar?',
        'Boleh. Sekitar separuh santri kami berasal dari SD negeri. Tidak ada syarat harus lulusan MI atau pernah mondok sebelumnya.',
    ],
    [
        'Anak saya belum lancar membaca Al-Qur\'an, apakah tetap bisa diterima?',
        'Bisa. Tes baca Al-Qur\'an dipakai untuk menempatkan santri di kelas yang sesuai, bukan untuk menggugurkan. Yang belum lancar masuk kelas tahsin dasar selama semester pertama.',
    ],
    [
        'Apakah ijazah MTs di sini diakui negara?',
        'Ya. Madrasah kami berada di bawah Kementerian Agama dan terdaftar di EMIS. Ijazahnya setara SMP dan bisa dipakai mendaftar ke SMA, SMK, atau MA negeri maupun swasta.',
    ],
    [
        'Kapan santri mukim boleh dijenguk dan pulang?',
        'Jadwal besuk setiap dua pekan sekali pada hari Ahad pukul 08.00 sampai 15.00. Santri boleh pulang saat libur semester, libur Ramadan, dan Idulfitri sesuai kalender pesantren.',
    ],
    [
        'Apakah santri boleh membawa telepon genggam?',
        'Tidak untuk santri mukim. Panitia menyediakan jadwal telepon dua kali sepekan lewat nomor wali asrama, dan setiap wali asrama punya grup WhatsApp bersama orang tua.',
    ],
    [
        'Bagaimana kalau di tengah jalan ingin pindah dari non-mukim ke mukim?',
        'Bisa, selama kamar asrama masih tersedia. Wali mengajukan ke bagian pengasuhan, lalu membayar selisih perlengkapan asrama. Biaya awal yang sudah dibayar tidak dihitung ulang.',
    ],
];

/* ==========================================================================
   DI BAWAH INI FUNGSI BANTU — biasanya tidak perlu diubah
   ========================================================================== */

/** Escape output HTML. */
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/** 1250000 -> "Rp1.250.000" */
function rp(int $n): string
{
    return 'Rp' . number_format($n, 0, ',', '.');
}

/** "2026-11-30" -> "30 November 2026" */
function tgl(string $iso, bool $pakai_tahun = true): string
{
    static $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];
    $t = strtotime($iso);
    $s = (int) date('j', $t) . ' ' . $bulan[(int) date('n', $t)];

    return $pakai_tahun ? $s . ' ' . date('Y', $t) : $s;
}

/** Rentang tanggal ringkas: "1 September – 30 November 2026" */
function rentang(string $a, string $b): string
{
    $sama_tahun = date('Y', strtotime($a)) === date('Y', strtotime($b));

    return tgl($a, !$sama_tahun) . ' – ' . tgl($b);
}

/** Status gelombang dihitung dari tanggal hari ini: 'akan' | 'buka' | 'tutup' */
function status_gel(array $g): string
{
    $kini = strtotime(date('Y-m-d'));
    if ($kini < strtotime($g['mulai'])) {
        return 'akan';
    }
    if ($kini > strtotime($g['selesai'])) {
        return 'tutup';
    }

    return 'buka';
}

/** Gelombang yang sedang dibuka, atau gelombang berikutnya bila belum ada. */
function gelombang_kini(array $list): ?array
{
    foreach ($list as $g) {
        if (status_gel($g) === 'buka') {
            return $g;
        }
    }
    foreach ($list as $g) {
        if (status_gel($g) === 'akan') {
            return $g;
        }
    }

    return null;
}

/** Link WhatsApp lengkap dengan pesan awal. */
function wa(string $pesan = 'Assalamualaikum, saya ingin bertanya tentang SPMB.'): string
{
    global $SITE;

    return 'https://wa.me/' . $SITE['wa'] . '?text=' . rawurlencode($pesan);
}

/** Link tombol daftar: formulir online bila diisi, kalau tidak jatuh ke WhatsApp. */
function link_daftar(): string
{
    global $SITE;

    if (!empty($SITE['link_formulir']) && $SITE['link_formulir'] !== '#') {
        return $SITE['link_formulir'];
    }

    return wa('Assalamualaikum, saya ingin mendaftarkan anak saya sebagai santri baru tahun ajaran ' . $SITE['tahun_ajaran'] . '.');
}

/** Total sebuah daftar komponen biaya. */
function total_biaya(array $komponen): int
{
    return array_sum(array_column($komponen, 1));
}

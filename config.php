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
    'wa'            => '6281230294589',        // format internasional, tanpa + dan tanpa 0 di depan
    'wa_tampil'     => '081230294589',
    'wa_nama'       => 'Ustaz Fauzi',
    'wa2'           => '6281230294589',
    'wa2_tampil'    => '081230294589',
    'wa2_nama'      => 'Ustazah Aisyah',
    'email'         => 'spmb@mtstahfidhroudlotulquran.sch.id',
    'alamat'        => 'Ngampelsari, Kec. Candi, Kabupaten Sidoarjo, Jawa Timur 61271',
    'maps'          => 'https://www.google.com/maps/search/?api=1&query=PP.+ROUDLOTUL+QUR%27AN+-+2+NGAMPELSARI',
    'jam_layanan'   => 'Senin – Sabtu, 08.00 – 15.00 WIB',

    // Link formulir pendaftaran. Jika diisi, tombol daftar akan mengarah ke halaman
    // formulir lokal agar calon wali bisa mengisi data pendaftaran sendiri.
    'link_formulir' => 'pendaftaran.php',

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
    ['Fotokopi akta kelahiran', '2 lembar'],
    ['Pas foto berwarna 3×4 dan foto ananda santriwan/santriwati', '4 lembar pas foto berlatar biru dan 1 lembar foto ananda'],
    ['Bisa membaca Al-Qur’an', 'untuk semua calon santri'],
    ['Minimal usia 13 tahun', 'saat mendaftar'],
    ['Fotokopi KIP, KKS, atau kartu PKH', 'bila mengajukan keringanan biaya'],
    ['Fotokopi sertifikat prestasi', 'bila mengajukan beasiswa prestasi'],
];

$SYARAT_JALUR = [
    ['Fotokopi KK/KTP', '1 lembar'],
    ['Foto ananda santriwan/santriwati', '1 lembar'],
    ['Bisa membaca Al-Qur’an', ''],
    ['Minimal usia 13 tahun', ''],
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
        'syarat'  => $SYARAT_JALUR,
        'rincian' => [
            ['Pendaftaran & uang gedung', 'Rp 300.000'],
            ['Makan 2x', 'Rp 350.000'],
            ['Syariah pondok', 'Rp 150.000'],
            ['Syariah Madin', 'Rp 20.000'],
            ['Kesehatan', 'Rp 10.000'],
            ['Kegiatan ekstra', 'Rp 20.000'],
            ['Kitab Madin', 'Rp 75.000'],
            ['Tabungan wajib', 'Rp 25.000'],
            ['Almari & kasur', 'Rp 300.000'],
            ['Laundry & seterika (seragam sekolah)', 'Rp 150.000'],
        ],
        'total_rincian' => 'Rp 1.600.000',
        'kotak_bulanan' => 'Biaya bulanan lengkap laundry & seterika seragam: Rp 725.000',
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
        'syarat'  => $SYARAT_JALUR,
        'rincian' => [
            ['Pendaftaran & gedung', 'Rp 250.000'],
            ['Makan 2x', 'Rp 350.000'],
            ['Syariah pondok', 'Rp 100.000'],
            ['Syariah Madin', 'Rp 20.000'],
            ['Kesehatan', 'Rp 10.000'],
            ['Kegiatan ekstra', 'Rp 20.000'],
            ['Kitab Madin', 'Rp 75.000'],
            ['Tabungan wajib', 'Rp 25.000'],
            ['Almari & kasur', 'Rp 300.000'],
        ],
        'total_rincian' => 'Rp 1.200.000',
        'kotak_bulanan' => 'Biaya bulanan: Rp 575.000',
    ],
];

$FAQ = [
    [
        "Apakah calon santri lulusan SD Negeri tanpa latar belakang madrasah diperbolehkan mendaftar?",
        "Tentu saja boleh. Yayasan Roudlotul Qur'an Az Zuhri membuka kesempatan seluas-luasnya bagi seluruh calon santri, baik yang berasal dari SD Negeri maupun Madrasah Ibtidaiyah (MI). Kami sangat memahami bahwa setiap anak memiliki titik awal pemahaman agama yang berbeda. Oleh karena itu, kurikulum MTs kami dirancang adaptif untuk membimbing siswa pada masa awal masuk, sehingga adaptasi dari sekolah umum ke lingkungan pesantren bisa berjalan dengan baik.",
    ],
    [
        "Bagaimana jika anak saya belum lancar membaca Al-Qur'an atau belum memiliki hafalan sama sekali?",
        "Bapak/Ibu tidak perlu khawatir. MTs Tahfidh Roudlotul Qur'an memiliki program bimbingan baca tulis Al-Qur'an (BTQ) yang terstruktur dan intensif. Santri yang belum lancar membaca akan dibimbing dari nol oleh ustaz dan ustazah yang berpengalaman hingga benar-benar fasih. Setelah bacaannya tartil dan sesuai tajwid, barulah santri akan diarahkan untuk fokus pada program hafalan (tahfidh) sesuai dengan target capaian kurikulum pondok.",
    ],
    [
        "Apakah ijazah yang dikeluarkan oleh MTs di sini diakui secara resmi oleh negara?",
        "Iya, diakui sepenuhnya. MTs Roudlotul Qur'an beroperasi secara legal dan resmi di bawah naungan Kementerian Agama Republik Indonesia. Lulusan kami akan mendapatkan ijazah negara yang sah, yang dapat digunakan untuk mendaftar ke jenjang pendidikan menengah atas manapun secara nasional, baik itu SMA/SMK negeri, madrasah aliyah swasta, maupun melanjutkan ke pondok pesantren tingkat tinggi lainnya.",
    ],
    [
        "Bagaimana dengan aturan asrama, kapan wali santri diperbolehkan menjenguk dan kapan santri diizinkan pulang?",
        "Untuk menjaga fokus, kedisiplinan, dan kelancaran setoran hafalan santri selama masa pendidikan, jadwal penjengukan diatur secara berkala, umumnya dilakukan satu bulan sekali pada hari Ahad minggu tertentu sesuai kalender pondok. Sedangkan untuk kepulangan, santri mukim akan mendapatkan jatah libur resmi dan diizinkan pulang ke rumah pada saat libur akhir semester ganjil/genap serta libur panjang hari raya Idul Fitri dan Idul Adha.",
    ],
    [
        "Apa saja fasilitas asrama dan pendidikan yang akan didapatkan oleh santri selama mondok?",
        "Kami menyediakan fasilitas yang mendukung penuh kegiatan belajar, menghafal Al-Qur'an, dan pembentukan karakter santri. Santri akan menempati ruang asrama yang representatif dengan fasilitas ranjang dan lemari pribadi. Selain itu, tersedia layanan makan bergizi yang terjamin kebersihannya, ruang kelas yang nyaman, masjid sebagai pusat kegiatan ibadah dan setoran hafalan, serta berbagai fasilitas penunjang untuk kegiatan ekstrakurikuler.",
    ],
    [
        "Bagaimana alur pendaftaran secara online melalui website ini?",
        "Proses pendaftaran dirancang agar sangat mudah dilakukan secara mandiri dari rumah. Wali santri cukup membuat akun pendaftaran, mengisi formulir data diri calon santri secara lengkap, mengunggah dokumen persyaratan seperti scan Kartu Keluarga, akta kelahiran, dan pas foto, lalu melakukan pembayaran biaya pendaftaran. Setelah data diverifikasi oleh panitia spmb/komite, kartu peserta tes seleksi dapat langsung diunduh dan dicetak melalui dashboard akun masing-masing.",
    ],
    [
        "Apa saja materi yang akan diujikan dalam tes seleksi masuk MTs?",
        "Tes seleksi masuk dirancang untuk memetakan kemampuan dasar akademik calon santri dan menempatkan mereka di kelas yang tepat. Materi ujian meliputi tes akademik (Matematika, IPA, dan Bahasa Indonesia), serta tes pemetaan kemampuan membaca Al-Qur'an (kelancaran dan tajwid dasar). Bagi calon santri yang sudah memiliki hafalan sebelumnya, akan ada sesi tes sambung ayat untuk penempatan di kelas tahfidh lanjutan. Terdapat juga sesi wawancara bagi wali santri untuk menyelaraskan visi dan misi pendidikan.",
    ],
    [
        "Bagaimana rincian biaya pendidikan dan apakah MTs menyediakan program beasiswa?",
        "Kami berkomitmen untuk memberikan rincian pembiayaan yang transparan sejak awal. Informasi lengkap mengenai biaya pendaftaran, daftar ulang, perlengkapan asrama, seragam, hingga infak bulanan dapat dilihat langsung pada menu Info Biaya SPMB di website ini. Yayasan Roudlotul Qur'an Az Zuhri juga menyediakan kuota beasiswa bagi calon santri berprestasi tingkat kabupaten/kota, serta jalur khusus keringanan biaya bagi santri dari keluarga yatim atau dhuafa sesuai dengan persyaratan dari panitia.",
    ],
    [
        "Selain program tahfidh dan kegiatan madrasah, apakah ada kegiatan ekstrakurikuler?",
        "Tentu saja ada. Usia MTs adalah masa yang sangat aktif untuk mengeksplorasi minat dan bakat. Oleh karena itu, pondok memfasilitasi berbagai kegiatan ekstrakurikuler seperti kepramukaan, seni bela diri (pencak silat), seni baca Al-Qur'an (qira'ah), hadrah/banjari, kaligrafi, hingga pelatihan pidato (muhadharah) dalam bahasa Arab dan Inggris. Tujuannya agar lulusan MTs kami tidak hanya kuat hafalannya, tetapi juga memiliki keterampilan kepemimpinan dan rasa percaya diri.",
    ],
    [
        "Bagaimana cara orang tua memantau perkembangan anak dan berkomunikasi selama di asrama?",
        "Sesuai aturan kedisiplinan pesantren untuk menjaga fokus hafalan, santri MTs memang tidak diizinkan membawa alat komunikasi (HP) pribadi. Namun, pesantren menyediakan fasilitas layanan telepon umum atau video call terjadwal melalui gawai musyrif/musyrifah (pengurus asrama) pada akhir pekan. Untuk perkembangan akademik dan capaian tahfidh, ustaz/ustazah pembimbing akan rutin memberikan laporan evaluasi melalui buku penghubung dan grup WhatsApp resmi wali santri.",
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

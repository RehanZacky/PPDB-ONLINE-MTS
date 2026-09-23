<?php
require __DIR__ . '/config.php';

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

    <form class="ppdb-form" action="#" method="post" enctype="multipart/form-data">
      <div class="form-panel">
        <h3>Data Diri Siswa</h3>

        <div class="form-grid">
          <div class="field">
            <label for="nama-siswa">1. Nama Siswa</label>
            <input id="nama-siswa" type="text" name="nama_siswa" placeholder="Masukkan nama lengkap" />
          </div>

          <div class="field">
            <label for="nomor-induk">2. Nomor Induk</label>
            <input id="nomor-induk" type="text" name="nomor_induk" placeholder="Nomor induk" />
          </div>

          <div class="field">
            <label for="nisn">3. NIS Nasional</label>
            <input id="nisn" type="text" name="nisn" placeholder="NISN" />
          </div>

          <div class="field">
            <label for="jenis-kelamin">4. Jenis Kelamin</label>
            <select id="jenis-kelamin" name="jenis_kelamin">
              <option value="">-- Pilih --</option>
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
          </div>

          <div class="field">
            <label for="tempat-lahir">5. Tempat dan Tgl Lahir</label>
            <input id="tempat-lahir" type="text" name="tempat_tanggal_lahir" placeholder="Contoh: Bandung, 14 Januari 2012" />
          </div>

          <div class="field">
            <label for="agama">6. Agama</label>
            <select id="agama" name="agama">
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
            <input id="anak-ke" type="text" name="anak_ke" placeholder="Anak ke-" />
          </div>

          <div class="field">
            <label for="status-keluarga">8. Status di Keluarga</label>
            <select id="status-keluarga" name="status_keluarga">
              <option value="">-- Pilih --</option>
              <option value="Anak Kandung">Anak Kandung</option>
              <option value="Anak Angkat">Anak Angkat</option>
              <option value="Anak Tiri">Anak Tiri</option>
            </select>
          </div>

          <div class="field field-wide">
            <label for="alamat-siswa">9. Alamat Siswa</label>
            <textarea id="alamat-siswa" name="alamat_siswa" placeholder="Masukkan alamat lengkap siswa"></textarea>
          </div>
        </div>

        <div class="sub-block">
          <h4>10. Diterima di sekolah ini</h4>
          <div class="form-grid">
            <div class="field">
              <label for="kelas">a. Di Kelas</label>
              <input id="kelas" type="text" name="diterima_kelas" placeholder="Contoh: VII-A" />
            </div>

            <div class="field">
              <label for="tanggal-diterima">b. Pada Tanggal</label>
              <input id="tanggal-diterima" type="date" name="tanggal_diterima" />
            </div>
          </div>
        </div>

        <div class="sub-block">
          <h4>11. Sekolah Asal</h4>
          <div class="form-grid">
            <div class="field">
              <label for="nama-sekolah">a. Nama Sekolah</label>
              <input id="nama-sekolah" type="text" name="nama_sekolah" placeholder="Nama sekolah asal" />
            </div>

            <div class="field">
              <label for="alamat-sekolah">b. Alamat Sekolah</label>
              <input id="alamat-sekolah" type="text" name="alamat_sekolah" placeholder="Alamat sekolah asal" />
            </div>
          </div>
        </div>

        <div class="sub-block">
          <h4>12. Nama Orang Tua</h4>
          <div class="form-grid">
            <div class="field">
              <label for="ayah">a. Ayah</label>
              <input id="ayah" type="text" name="nama_ayah" placeholder="Nama ayah" />
            </div>

            <div class="field">
              <label for="ibu">b. Ibu</label>
              <input id="ibu" type="text" name="nama_ibu" placeholder="Nama ibu" />
            </div>
          </div>
        </div>

        <div class="sub-block">
          <h4>13. Alamat Orang Tua</h4>
          <div class="field">
            <textarea name="alamat_orang_tua" placeholder="Alamat lengkap orang tua"></textarea>
          </div>
        </div>

        <div class="sub-block">
          <h4>14. Pekerjaan Orang Tua</h4>
          <div class="form-grid">
            <div class="field">
              <label for="pekerjaan-ayah">a. Ayah</label>
              <input id="pekerjaan-ayah" type="text" name="pekerjaan_ayah" placeholder="Pekerjaan ayah" />
            </div>

            <div class="field">
              <label for="pekerjaan-ibu">b. Ibu</label>
              <input id="pekerjaan-ibu" type="text" name="pekerjaan_ibu" placeholder="Pekerjaan ibu" />
            </div>
          </div>
        </div>

        <div class="sub-block">
          <h4>15. Nama Wali</h4>
          <div class="field">
            <input type="text" name="nama_wali" placeholder="Nama wali" />
          </div>
        </div>

        <div class="sub-block">
          <h4>16. Alamat Wali</h4>
          <div class="field">
            <textarea name="alamat_wali" placeholder="Alamat lengkap wali"></textarea>
          </div>
        </div>

        <div class="sub-block">
          <h4>17. Pekerjaan</h4>
          <div class="field">
            <input type="text" name="pekerjaan_wali" placeholder="Pekerjaan wali" />
          </div>
        </div>
      </div>

      <div class="form-panel">
        <h3>Data Pendukung</h3>
        <div class="upload-grid">
          <label class="upload-box">
            <span>Akta Kelahiran</span>
            <input type="file" name="akta" accept=".pdf,image/*" />
            <small>Upload file PDF/JPG/PNG</small>
          </label>

          <label class="upload-box">
            <span>Kartu Keluarga</span>
            <input type="file" name="kk" accept=".pdf,image/*" />
            <small>Upload file PDF/JPG/PNG</small>
          </label>

          <label class="upload-box">
            <span>Sertifikat / Prestasi</span>
            <input type="file" name="sertifikat" accept=".pdf,image/*" />
            <small>Upload file PDF/JPG/PNG</small>
          </label>

          <label class="upload-box">
            <span>Rapor / Nilai</span>
            <input type="file" name="rapor" accept=".pdf,image/*" />
            <small>Upload file PDF/JPG/PNG</small>
          </label>

          <label class="upload-box">
            <span>Foto Siswa</span>
            <input type="file" name="foto" accept="image/*" />
            <small>Upload foto terbaru</small>
          </label>

          <label class="upload-box">
            <span>Dokumen Lainnya</span>
            <input type="file" name="dokumen_lain" accept=".pdf,image/*" />
            <small>Upload dokumen tambahan</small>
          </label>
        </div>
      </div>

      <div class="form-panel">
        <h3>Rekening &amp; Bukti Pembayaran</h3>

        <div class="rekening-box">
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
          <label class="upload-box">
            <span>Bukti Pembayaran</span>
            <input type="file" name="bukti_pembayaran" accept=".pdf,image/*" />
            <small>Upload bukti transfer atau pembayaran</small>
          </label>
        </div>
      </div>

      <div class="submit-wrap">
        <button type="button" class="tbl tbl-utama submit-confirm">Kirim Pendaftaran</button>
      </div>
    </form>
  </div>
</section>

<div class="submit-modal-overlay" id="submitModal" hidden>
  <div class="submit-modal" role="dialog" aria-modal="true" aria-labelledby="submitModalTitle">
    <h3 id="submitModalTitle">Konfirmasi pengiriman</h3>
    <p>Pastikan semua data sudah benar dan lengkap sebelum dikirim. Setelah data terkirim, data tidak akan bisa diubah.</p>

    <label class="confirm-check">
      <input type="checkbox" id="checkDataBenar" />
      <span>Saya yakin semua data yang diisi sudah benar dan lengkap.</span>
    </label>

    <label class="confirm-check">
      <input type="checkbox" id="checkTidakEdit" />
      <span>Saya memahami data tidak dapat diedit setelah dikirim.</span>
    </label>

    <div class="modal-actions">
      <button type="button" class="tbl tbl-garis" id="batalSubmit">Batal</button>
      <button type="button" class="tbl tbl-utama" id="lanjutSubmit" disabled>Kirim</button>
    </div>
  </div>
</div>

<a class="wa-apung" href="<?= e(wa()) ?>" target="_blank" rel="noopener" aria-label="Hubungi panitia lewat WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.45 1.34 4.95L2 22l5.22-1.37a9.9 9.9 0 0 0 4.82 1.23h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2Zm5.8 14.06c-.24.68-1.42 1.31-1.95 1.36-.5.05-.98.24-3.3-.69-2.78-1.1-4.54-3.95-4.68-4.13-.13-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.25-.28.55-.35.73-.35h.52c.17 0 .4-.06.62.48.24.57.8 1.98.87 2.12.07.14.12.31.02.49-.09.18-.14.29-.28.45-.14.16-.29.35-.41.47-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.04.93 1.92 1.21 2.2 1.35.28.14.44.12.6-.07.17-.19.7-.81.88-1.09.18-.28.37-.23.62-.14.25.09 1.6.75 1.87.89.28.14.46.21.53.32.07.12.07.66-.17 1.34Z"/></svg>
  <span>Tanya panitia</span>
</a>

<script>
(function () {
  const form = document.querySelector('.ppdb-form');
  const submitButton = document.querySelector('.submit-confirm');
  const modal = document.getElementById('submitModal');
  const checkDataBenar = document.getElementById('checkDataBenar');
  const checkTidakEdit = document.getElementById('checkTidakEdit');
  const lanjutSubmit = document.getElementById('lanjutSubmit');
  const batalSubmit = document.getElementById('batalSubmit');

  if (!form || !submitButton || !modal || !checkDataBenar || !checkTidakEdit || !lanjutSubmit || !batalSubmit) return;

  let allowSubmit = false;

  modal.hidden = true;
  form.noValidate = true;

  function hasFormData() {
    const fields = form.querySelectorAll('input:not([readonly]):not([type="hidden"]), select, textarea');

    for (const field of fields) {
      if (field.type === 'file') {
        if (field.files && field.files.length > 0) return true;
        continue;
      }

      if (field.value && field.value.trim() !== '') return true;
    }

    return false;
  }

  function closeModal() {
    modal.hidden = true;
    document.body.style.overflow = '';
    checkDataBenar.checked = false;
    checkTidakEdit.checked = false;
    lanjutSubmit.disabled = true;
  }

  function openModal() {
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
    checkDataBenar.checked = false;
    checkTidakEdit.checked = false;
    lanjutSubmit.disabled = true;
  }

  function updateModalState() {
    lanjutSubmit.disabled = !(checkDataBenar.checked && checkTidakEdit.checked);
  }

  submitButton.addEventListener('click', function (event) {
    event.preventDefault();

    if (!hasFormData()) {
      const firstField = form.querySelector('input:not([readonly]):not([type="hidden"]), select, textarea');
      if (firstField) firstField.focus();
      return;
    }

    openModal();
  });

  form.addEventListener('submit', function (event) {
    if (allowSubmit) {
      allowSubmit = false;
      return;
    }

    event.preventDefault();

    if (!hasFormData()) {
      const firstField = form.querySelector('input:not([readonly]):not([type="hidden"]), select, textarea');
      if (firstField) firstField.focus();
      return;
    }

    openModal();
  });

  checkDataBenar.addEventListener('change', updateModalState);
  checkTidakEdit.addEventListener('change', updateModalState);

  batalSubmit.addEventListener('click', function () {
    closeModal();
  });

  lanjutSubmit.addEventListener('click', function () {
    closeModal();
    allowSubmit = true;
    form.submit();
  });

  modal.addEventListener('click', function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (!modal.hidden && event.key === 'Escape') {
      closeModal();
    }
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

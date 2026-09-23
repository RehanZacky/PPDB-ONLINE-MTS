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
      <div class="step-indicator" aria-label="Tahap pendaftaran">
        <div class="step-item active">
          <span class="step-dot">1</span>
          <small>Data Diri</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item">
          <span class="step-dot">2</span>
          <small>Data Pendukung</small>
        </div>
        <span class="step-line"></span>
        <div class="step-item">
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
                  <input type="radio" name="kategori_santri" value="Mukim" required />
                  <span>Santri Mukim</span>
                </label>
                <label class="radio-item">
                  <input type="radio" name="kategori_santri" value="Non Mukim" required />
                  <span>Santri Non Mukim</span>
                </label>
              </div>
            </div>

            <div class="field field-wide">
              <label for="alamat-siswa">10. Alamat Siswa</label>
              <textarea id="alamat-siswa" name="alamat_siswa" placeholder="Masukkan alamat lengkap siswa" required></textarea>
            </div>
          </div>

          <div class="sub-block">
            <h4>10. Diterima di sekolah ini</h4>
            <div class="form-grid">
              <div class="field">
                <label for="kelas">a. Di Kelas</label>
                <input id="kelas" type="text" name="diterima_kelas" placeholder="Contoh: VII-A" required />
              </div>

              <div class="field">
                <label for="tanggal-diterima">b. Pada Tanggal</label>
                <input id="tanggal-diterima" type="date" name="tanggal_diterima" required />
              </div>
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
            <h4>13. Alamat Orang Tua</h4>
            <div class="field">
              <textarea name="alamat_orang_tua" placeholder="Alamat lengkap orang tua" required></textarea>
            </div>
          </div>

          <div class="sub-block">
            <h4>14. Pekerjaan Orang Tua</h4>
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
            <h4>15. Nama Wali</h4>
            <div class="field">
              <input type="text" name="nama_wali" placeholder="Nama wali" required />
            </div>
          </div>

          <div class="sub-block">
            <h4>16. Alamat Wali</h4>
            <div class="field">
              <textarea name="alamat_wali" placeholder="Alamat lengkap wali" required></textarea>
            </div>
          </div>

          <div class="sub-block">
            <h4>17. Pekerjaan</h4>
            <div class="field">
              <input type="text" name="pekerjaan_wali" placeholder="Pekerjaan wali" required />
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
              <input type="file" name="akta" accept=".pdf,image/*" required />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Kartu Keluarga</span>
              <input type="file" name="kk" accept=".pdf,image/*" required />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Sertifikat / Prestasi</span>
              <input type="file" name="sertifikat" accept=".pdf,image/*" />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Rapor / Nilai</span>
              <input type="file" name="rapor" accept=".pdf,image/*" required />
              <small>Upload file PDF/JPG/PNG</small>
            </label>

            <label class="upload-box">
              <span>Foto Siswa</span>
              <input type="file" name="foto" accept="image/*" required />
              <small>Upload foto terbaru</small>
            </label>

            <label class="upload-box">
              <span>Dokumen Lainnya</span>
              <input type="file" name="dokumen_lain" accept=".pdf,image/*" />
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
  var dots = Array.prototype.slice.call(document.querySelectorAll('.step-dot'));
  var nextButtons = Array.prototype.slice.call(form.querySelectorAll('.next-step'));
  var prevButtons = Array.prototype.slice.call(form.querySelectorAll('.prev-step'));
  var submitButton = form.querySelector('.submit-confirm');
  var paymentInput = form.querySelector('input[name="bukti_pembayaran"]');
  var currentStep = 0;

  function getFields(stepEl) {
    return Array.prototype.slice.call(stepEl.querySelectorAll('input, select, textarea')).filter(function (field) {
      return !field.disabled && !field.readOnly;
    });
  }

  function isStepComplete(stepEl) {
    var fields = getFields(stepEl);
    return fields.every(function (field) {
      if (field.type === 'file') {
        return !!(field.files && field.files.length > 0);
      }
      return field.checkValidity() && field.value.trim() !== '';
    });
  }

  function updateStepView() {
    steps.forEach(function (step, index) {
      step.classList.toggle('active', index === currentStep);
    });

    dots.forEach(function (dot, index) {
      dot.classList.toggle('active', index === currentStep);
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
        var ok = field.type === 'file'
          ? !!(field.files && field.files.length > 0)
          : field.checkValidity() && field.value.trim() !== '';

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

  if (paymentInput) {
    paymentInput.addEventListener('change', syncSubmitState);
  }

  restoreDraft();
  syncSubmitState();
  updateStepView();
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

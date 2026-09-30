</main>

<!-- ajakan daftar -->
<section class="blok blok-mint-tua" aria-labelledby="ajakan-daftar">
  <div class="wadah" style="text-align:center">
    <div class="kepala">
      <span class="label">Pendaftaran <?= e($SITE['tahun_ajaran']) ?></span>
      <h2 id="ajakan-daftar">Yuk, buruan daftar sekarang!</h2>
      <p>Amankan kesempatan belajar dan berkembang bersama MTs Tahfidh Roudlotul Qur'an.</p>
    </div>
    <a class="tbl tbl-utama" href="<?= e(link_daftar()) ?>">Daftar sekarang</a>
  </div>
</section>

<!-- kontak panitia -->
<section class="blok blok-mint" id="kontak">
  <div class="wadah">
    <div class="kepala">
      <span class="label">Kontak panitia</span>
      <h2>Tanya apa saja sebelum mendaftar</h2>
      <p>Balasan paling cepat lewat WhatsApp pada jam layanan.</p>
    </div>

    <ul class="kontak">
      <li>
        <span class="bulat" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.45 1.34 4.95L2 22l5.22-1.37a9.9 9.9 0 0 0 4.82 1.23h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2Zm5.8 14.06c-.24.68-1.42 1.31-1.95 1.36-.5.05-.98.24-3.3-.69-2.78-1.1-4.54-3.95-4.68-4.13-.13-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.25-.28.55-.35.73-.35h.52c.17 0 .4-.06.62.48.24.57.8 1.98.87 2.12.07.14.12.31.02.49-.09.18-.14.29-.28.45-.14.16-.29.35-.41.47-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.04.93 1.92 1.21 2.2 1.35.28.14.44.12.6-.07.17-.19.7-.81.88-1.09.18-.28.37-.23.62-.14.25.09 1.6.75 1.87.89.28.14.46.21.53.32.07.12.07.66-.17 1.34Z"/></svg>
        </span>
        <b>WhatsApp panitia</b>
        <a href="https://wa.me/<?= e($SITE['wa']) ?>" target="_blank" rel="noopener"><?= e($SITE['wa_tampil']) ?> — <?= e($SITE['wa_nama']) ?></a>
        <?php if (!empty($SITE['wa2']) && $SITE['wa2'] !== $SITE['wa']): ?>
          <a href="https://wa.me/<?= e($SITE['wa2']) ?>" target="_blank" rel="noopener"><?= e($SITE['wa2_tampil']) ?> — <?= e($SITE['wa2_nama']) ?></a>
        <?php endif; ?>
      </li>
      <li>
        <span class="bulat" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/></svg>
        </span>
        <b>Kantor panitia</b>
        <p style="margin:0;color:var(--redup);font-size:.93rem"><?= e($SITE['alamat']) ?></p>
        <a href="<?= e($SITE['maps']) ?>" target="_blank" rel="noopener">Buka di Google Maps</a>
      </li>
      <li>
        <span class="bulat" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/></svg>
        </span>
        <b>Jam layanan</b>
        <p style="margin:0;color:var(--redup);font-size:.93rem"><?= e($SITE['jam_layanan']) ?></p>
        <a href="mailto:<?= e($SITE['email']) ?>"><?= e($SITE['email']) ?></a>
      </li>
    </ul>
  </div>
</section>

<footer class="bawah">
  <div class="wadah">
    <div class="bawah-isi">
      <div>
        <b><?= e($SITE['lembaga']) ?></b>
        <?= e($SITE['yayasan']) ?>
      </div>
      <nav aria-label="Navigasi bawah">
        <a href="index.php">Beranda SPMB</a>
        <a href="gelombang.php">Gelombang</a>
        <a href="alur.php">Alur &amp; Syarat</a>
        <a href="biaya.php">Biaya</a>
        <a href="<?= e($SITE['url_profil']) ?>">Situs profil</a>
      </nav>
    </div>
    <div class="garis">
      &copy; <?= date('Y') ?> <?= e($SITE['yayasan']) ?>. Informasi SPMB tahun ajaran <?= e($SITE['tahun_ajaran']) ?>.
      <br>
      Dibuat tim IT @rakhahadiyazid dan @androlintangmahaegan.
    </div>
  </div>
</footer>

<a class="wa-apung" href="<?= e(wa()) ?>" target="_blank" rel="noopener" aria-label="Hubungi panitia lewat WhatsApp">
  <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.45 1.34 4.95L2 22l5.22-1.37a9.9 9.9 0 0 0 4.82 1.23h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2Zm5.8 14.06c-.24.68-1.42 1.31-1.95 1.36-.5.05-.98.24-3.3-.69-2.78-1.1-4.54-3.95-4.68-4.13-.13-.18-1.12-1.49-1.12-2.84 0-1.35.71-2.01.96-2.29.25-.28.55-.35.73-.35h.52c.17 0 .4-.06.62.48.24.57.8 1.98.87 2.12.07.14.12.31.02.49-.09.18-.14.29-.28.45-.14.16-.29.35-.41.47-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.04.93 1.92 1.21 2.2 1.35.28.14.44.12.6-.07.17-.19.7-.81.88-1.09.18-.28.37-.23.62-.14.25.09 1.6.75 1.87.89.28.14.46.21.53.32.07.12.07.66-.17 1.34Z"/></svg>
  <span>Tanya panitia</span>
</a>

<div class="lightbox" id="lightbox-pembayaran" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Perbesar gambar pembayaran">
  <button class="lightbox-tutup" type="button" aria-label="Tutup gambar">&times;</button>
  <img class="lightbox-gambar" src="" alt="">
</div>

<script>
(function () {
  "use strict";

  /* menu untuk layar kecil */
  var tombol = document.getElementById("tombol-menu");
  var menu = document.getElementById("menu");
  if (tombol && menu) {
    tombol.addEventListener("click", function () {
      var buka = menu.classList.toggle("buka");
      tombol.setAttribute("aria-expanded", buka ? "true" : "false");
      tombol.setAttribute("aria-label", buka ? "Tutup menu" : "Buka menu");
    });
  }

  /* tab biaya */
  var tabs = Array.prototype.slice.call(document.querySelectorAll("[role='tab']"));
  if (tabs.length) {
    var pilih = function (tab) {
      tabs.forEach(function (t) {
        var aktif = t === tab;
        t.setAttribute("aria-selected", aktif ? "true" : "false");
        t.setAttribute("tabindex", aktif ? "0" : "-1");
        document.getElementById(t.getAttribute("aria-controls")).hidden = !aktif;
      });
    };
    tabs.forEach(function (tab, i) {
      tab.addEventListener("click", function () { pilih(tab); });
      tab.addEventListener("keydown", function (ev) {
        var ke = ev.key === "ArrowRight" ? i + 1 : ev.key === "ArrowLeft" ? i - 1 : -1;
        if (ke < 0 || ke >= tabs.length) return;
        ev.preventDefault();
        tabs[ke].focus();
        pilih(tabs[ke]);
      });
    });
  }

  /* perbesar gambar pembayaran */
  var lightbox = document.getElementById("lightbox-pembayaran");
  var lightboxGambar = lightbox ? lightbox.querySelector(".lightbox-gambar") : null;
  var lightboxTutup = lightbox ? lightbox.querySelector(".lightbox-tutup") : null;
  var gambarAktif = null;
  var gambarPembayaran = Array.prototype.slice.call(document.querySelectorAll(".gambar-pembayaran img, .gambar-alur img"));

  if (lightbox && lightboxGambar && lightboxTutup && gambarPembayaran.length) {
    var tutupLightbox = function () {
      lightbox.classList.remove("buka");
      lightbox.setAttribute("aria-hidden", "true");
      document.body.classList.remove("lightbox-terbuka");
      if (gambarAktif) gambarAktif.focus();
    };

    var bukaLightbox = function (gambar) {
      gambarAktif = gambar;
      lightboxGambar.src = gambar.src;
      lightboxGambar.alt = gambar.alt;
      lightbox.classList.add("buka");
      lightbox.setAttribute("aria-hidden", "false");
      document.body.classList.add("lightbox-terbuka");
      lightboxTutup.focus();
    };

    gambarPembayaran.forEach(function (gambar) {
      gambar.setAttribute("tabindex", "0");
      gambar.setAttribute("role", "button");
      gambar.addEventListener("click", function () { bukaLightbox(gambar); });
      gambar.addEventListener("keydown", function (ev) {
        if (ev.key === "Enter" || ev.key === " ") {
          ev.preventDefault();
          bukaLightbox(gambar);
        }
      });
    });

    lightboxTutup.addEventListener("click", tutupLightbox);
    lightbox.addEventListener("click", function (ev) {
      if (ev.target === lightbox) tutupLightbox();
    });
    document.addEventListener("keydown", function (ev) {
      if (ev.key === "Escape" && lightbox.classList.contains("buka")) tutupLightbox();
    });
  }
})();
</script>
</body>
</html>

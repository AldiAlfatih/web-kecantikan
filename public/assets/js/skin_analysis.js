/**
 * skin_analysis.js — v3 (MediaPipe Integration)
 * Fase 3 — AI / Computer Vision Developer
 *
 * Orchestrator utama halaman /skin-analysis.
 * Menggabungkan:
 *  - FaceDetectorModule  (face_detector.js)
 *  - SkinColorAnalyzer   (skin_color_analyzer.js)
 *  - Kamera browser (getUserMedia)
 *  - UI state machine (waiting → live → detecting → analyzing → results)
 *  - Backend API calls (/api/skin-recommend & /api/skin-save)
 *
 * Tidak ada perubahan pada backend.
 */

(function () {
  'use strict';

  /* ══════════════════════════════════════════════════════════
     ELEMENT REFERENCES
  ══════════════════════════════════════════════════════════ */
  const video           = document.getElementById('saVideo');
  const canvas          = document.getElementById('saCanvas');
  const cameraEmpty     = document.getElementById('saCameraEmpty');
  const statusOverlay   = document.getElementById('saStatusOverlay');
  const statusText      = document.getElementById('saStatusText');
  const pillDot         = document.getElementById('saPillDot');
  const pillLabel       = document.getElementById('saPillLabel');

  const btnStart        = document.getElementById('saBtnStart');
  const btnStop         = document.getElementById('saBtnStop');
  const btnFlip         = document.getElementById('saBtnFlip');
  const btnAnalyzeWrap  = document.getElementById('saBtnAnalyzeWrap');
  const btnAnalyze      = document.getElementById('saBtnAnalyze');
  const btnRetake       = document.getElementById('saBtnRetake');
  const btnSave         = document.getElementById('saBtnSave');

  const panelWaiting    = document.getElementById('saPanelWaiting');
  const panelResult     = document.getElementById('saPanelResult');
  const panelSkeleton   = document.getElementById('saPanelSkeleton');
  const panelSaveSuccess= document.getElementById('saSaveSuccess');

  const toneSwatchEl    = document.getElementById('saToneSwatch');
  const toneLabelEl     = document.getElementById('saToneLabel');
  const toneUndertoneEl = document.getElementById('saToneUndertone');
  const savePrompt      = document.getElementById('saSavePrompt');
  const rekomList       = document.getElementById('saRekomList');

  /* ══════════════════════════════════════════════════════════
     STATE
  ══════════════════════════════════════════════════════════ */
  let stream              = null;
  let facingMode          = 'user';
  let latestLandmarks     = null;   // landmark terakhir dari MediaPipe
  let stableFrameCount    = 0;      // jumlah frame berturut-turut dengan wajah
  let detectorStarted     = false;  // sudah mulai face detection?
  let analysisResult      = null;   // hasil terakhir { skin_tone_level, undertone, hex }

  const STABLE_THRESHOLD  = 10;     // frame stabil sebelum "Analisis" bisa diklik

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  /* ══════════════════════════════════════════════════════════
     CANVAS RESIZE
     Canvas internal resolution harus match ukuran container CSS
     agar koordinat overlay tepat.
  ══════════════════════════════════════════════════════════ */
  function syncCanvasSize() {
    if (!canvas) return;
    const area = document.getElementById('saCameraArea');
    if (area) {
      canvas.width  = area.clientWidth;
      canvas.height = area.clientHeight;
    }
  }

  // Sync setiap kali video metadata berubah (resolusi, dsb.)
  video?.addEventListener('loadedmetadata', syncCanvasSize);
  window.addEventListener('resize', syncCanvasSize);

  /* ══════════════════════════════════════════════════════════
     CAMERA
  ══════════════════════════════════════════════════════════ */
  async function startCamera() {
    if (btnStart) btnStart.disabled = true;
    setStatus('Meminta akses kamera...', true);

    try {
      // Hentikan stream sebelumnya jika ada
      if (stream) stopStreamTracks();

      stream = await navigator.mediaDevices.getUserMedia({
        video: {
          facingMode: { ideal: facingMode },
          width:  { ideal: 1280 },
          height: { ideal: 720 },
        },
        audio: false,
      });

      video.srcObject = stream;
      video.style.display  = 'block';
      canvas.style.display = 'block';
      cameraEmpty.style.display = 'none';

      await video.play();
      syncCanvasSize();

      // Update pill
      btnStop.disabled = false;
      pillDot.classList.add('active');
      pillLabel.textContent = 'Kamera Aktif';

      // Mulai MediaPipe face detection
      startFaceDetection();

    } catch (err) {
      console.error('[SA] Camera error:', err);
      if (btnStart) btnStart.disabled = false;
      setStatus('', false);

      let msg = 'Tidak bisa mengakses kamera.';
      if (err.name === 'NotAllowedError')
        msg = 'Izin kamera ditolak. Klik ikon 🔒 di address bar lalu aktifkan izin.';
      else if (err.name === 'NotFoundError')
        msg = 'Kamera tidak ditemukan di perangkat ini.';
      else if (err.name === 'NotReadableError')
        msg = 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain lalu coba lagi.';

      showCameraEmpty('⚠️', 'Kamera tidak bisa diakses', msg);
    }
  }

  function stopCamera() {
    stopStreamTracks();
    FaceDetectorModule.stopDetection();
    SkinColorAnalyzer.clearCanvas(canvas);

    video.style.display   = 'none';
    canvas.style.display  = 'none';
    video.srcObject       = null;

    showCameraEmpty('📷', 'Kamera tidak aktif', 'Tekan tombol di bawah untuk memulai.');

    if (btnStart) btnStart.disabled = false;
    if (btnStop)  btnStop.disabled  = true;
    if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'none';

    pillDot.classList.remove('active');
    pillLabel.textContent = 'Live Preview';

    // Reset detection state
    latestLandmarks  = null;
    stableFrameCount = 0;
    detectorStarted  = false;
    setStatus('', false);
  }

  function stopStreamTracks() {
    if (!stream) return;
    stream.getTracks().forEach(t => t.stop());
    stream = null;
  }

  function flipCamera() {
    facingMode = facingMode === 'user' ? 'environment' : 'user';
    if (stream) startCamera();
  }

  function showCameraEmpty(ico, title, sub) {
    if (!cameraEmpty) return;
    cameraEmpty.style.display = 'flex';
    cameraEmpty.innerHTML     = `
      <div class="sa-empty-ico">${ico}</div>
      <p class="sa-empty-title">${title}</p>
      <p class="sa-empty-sub">${sub}</p>
    `;
  }

  /* ══════════════════════════════════════════════════════════
     FACE DETECTION — MediaPipe Integration

     Flow:
     1. startCamera() → startFaceDetection()
     2. FaceDetectorModule loads MediaPipe CDN (lazy)
     3. onLoading → tampilkan pesan loading ke user
     4. onReady   → model siap, deteksi dimulai
     5. onFace(landmarks) per-frame:
        - update latestLandmarks
        - gambar face oval di canvas (hijau kalau stabil)
        - setelah STABLE_THRESHOLD frame → tampilkan tombol Analisis
     6. onNoFace → clear canvas, reset counter
  ══════════════════════════════════════════════════════════ */
  function startFaceDetection() {
    if (detectorStarted) return;
    detectorStarted = true;

    FaceDetectorModule.startDetection(video, {

      onLoading: (msg) => {
        setStatus(msg, true);
      },

      onReady: () => {
        setStatus('Arahkan wajah ke kamera...', false);
      },

      onFace: (landmarks) => {
        latestLandmarks = landmarks;
        stableFrameCount++;

        // Gambar overlay setiap frame ada wajah
        const stable = stableFrameCount >= STABLE_THRESHOLD;
        SkinColorAnalyzer.drawFaceOverlay(canvas, landmarks, video, stable);

        if (stable) {
          // Tampilkan tombol analisis begitu wajah stabil
          if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'block';
          setStatus('✓ Wajah terdeteksi — siap dianalisis', false);
        } else {
          setStatus('Mendeteksi wajah...', true);
        }
      },

      onNoFace: () => {
        // Reset penghitung stabilitas
        stableFrameCount = 0;
        latestLandmarks  = null;

        SkinColorAnalyzer.clearCanvas(canvas);
        if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'none';
        setStatus('Wajah tidak terdeteksi — pastikan pencahayaan cukup', false);
      },

      onError: (msg) => {
        setStatus('', false);
        showCameraEmpty(
          '⚠️',
          'AI gagal dimuat',
          'Gagal memuat MediaPipe: ' + msg + '. Coba refresh halaman.'
        );
      },
    });
  }

  /* ══════════════════════════════════════════════════════════
     ANALISIS — Menggunakan landmark MediaPipe + IF-THEN rules
  ══════════════════════════════════════════════════════════ */
  async function runAnalysis() {
    if (!stream) {
      alert('Aktifkan kamera terlebih dahulu!');
      return;
    }

    if (!latestLandmarks) {
      alert('Pastikan wajah Anda terlihat jelas di kamera, dengan pencahayaan yang cukup.');
      return;
    }

    setStatus('Menganalisis warna kulit...', true);
    showAnalyzingState();

    // ─ Sampling warna dari landmark MediaPipe ─────────────────
    const sampled = SkinColorAnalyzer.sampleSkinColor(video, latestLandmarks);

    let skin_tone_level, undertone, hex;

    if (sampled && sampled.r > 0) {
      const classified = SkinColorAnalyzer.classify(sampled.r, sampled.g, sampled.b);
      skin_tone_level  = classified.skin_tone_level;
      undertone        = classified.undertone;
      hex              = sampled.hex;

      console.info('[SA] Analisis warna kulit:', {
        rgb: `rgb(${sampled.r},${sampled.g},${sampled.b})`,
        hex,
        hsv: `h=${classified.h}° s=${(classified.s * 100).toFixed(1)}% v=${(classified.v * 100).toFixed(1)}%`,
        skin_tone_level,
        undertone,
      });
    } else {
      // Fallback jika sampling gagal (pencahayaan sangat buruk)
      console.warn('[SA] Pixel sampling gagal, menggunakan nilai fallback.');
      skin_tone_level = 3;
      undertone       = 'neutral';
      hex             = '#C8A882';
    }

    analysisResult = { skin_tone_level, undertone, hex };

    // ─ Fetch rekomendasi dari backend ─────────────────────────
    await fetchRecommendations(skin_tone_level, undertone, hex);
  }

  /* ══════════════════════════════════════════════════════════
     API CALLS (Backend tidak diubah)
  ══════════════════════════════════════════════════════════ */
  async function fetchRecommendations(toneLevel, undertone, hex) {
    try {
      const resp = await fetch(window.__SA_ROUTES__.recommend, {
        method : 'POST',
        headers: {
          'Content-Type' : 'application/json',
          'X-CSRF-TOKEN' : csrfToken,
          'Accept'       : 'application/json',
        },
        body: JSON.stringify({ skin_tone_level: toneLevel, undertone }),
      });

      if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
      const data = await resp.json();

      setStatus('', false);
      data.success ? showResultState(data, hex) : showResultError();

    } catch (err) {
      console.error('[SA] API error:', err);
      setStatus('', false);
      showResultError();
    }
  }

  async function saveProfile() {
    if (!analysisResult) return;

    if (btnSave) {
      btnSave.disabled    = true;
      btnSave.textContent = 'Menyimpan...';
    }

    try {
      const resp = await fetch(window.__SA_ROUTES__.save, {
        method : 'POST',
        headers: {
          'Content-Type' : 'application/json',
          'X-CSRF-TOKEN' : csrfToken,
          'Accept'       : 'application/json',
        },
        body: JSON.stringify({
          skin_tone_level: analysisResult.skin_tone_level,
          undertone      : analysisResult.undertone,
          hex_sample     : analysisResult.hex,
        }),
      });

      const data = await resp.json();

      if (data.success) {
        if (savePrompt)       savePrompt.style.display = 'none';
        if (panelSaveSuccess) panelSaveSuccess.classList.add('visible');
      } else {
        throw new Error('Save returned success:false');
      }

    } catch (err) {
      console.error('[SA] Save error:', err);
      if (btnSave) {
        btnSave.disabled    = false;
        btnSave.textContent = 'Simpan';
      }
      alert('Gagal menyimpan profil. Periksa koneksi internet lalu coba lagi.');
    }
  }

  /* ══════════════════════════════════════════════════════════
     STATUS OVERLAY
  ══════════════════════════════════════════════════════════ */
  function setStatus(text, showSpinner = true) {
    if (!statusOverlay || !statusText) return;

    if (!text) {
      statusOverlay.classList.remove('visible');
      return;
    }

    statusText.textContent = text;

    const spinner = statusOverlay.querySelector('.sa-spinner');
    if (spinner) spinner.style.display = showSpinner ? 'inline-block' : 'none';

    statusOverlay.classList.add('visible');
  }

  /* ══════════════════════════════════════════════════════════
     UI STATE MACHINE
     waiting → skeleton → result / error
  ══════════════════════════════════════════════════════════ */
  function showAnalyzingState() {
    panelWaiting?.style.setProperty('display', 'none');
    panelResult?.style.setProperty('display', 'none');
    panelSkeleton?.style.setProperty('display', 'flex');
  }

  function showResultState(data, hex) {
    panelWaiting?.style.setProperty('display', 'none');
    panelSkeleton?.style.setProperty('display', 'none');
    panelResult?.style.setProperty('display', 'block');

    // Perbarui swatch & label
    if (toneSwatchEl)     toneSwatchEl.style.background   = hex;
    if (toneLabelEl)      toneLabelEl.textContent          = data.tone_label ?? '–';
    if (toneUndertoneEl)  toneUndertoneEl.textContent      = 'Undertone: ' + (data.undertone_label ?? '–');

    // Tampilkan save prompt
    if (savePrompt)       savePrompt.style.display         = 'flex';
    if (panelSaveSuccess) panelSaveSuccess.classList.remove('visible');

    renderRekomendasi(data.recommendations ?? []);
  }

  function showResultError() {
    panelWaiting?.style.setProperty('display', 'none');
    panelSkeleton?.style.setProperty('display', 'none');
    panelResult?.style.setProperty('display', 'block');

    if (rekomList) {
      rekomList.innerHTML = `
        <div class="sa-rekom-empty">
          ⚠️ Gagal mengambil rekomendasi.<br>
          Pastikan internet aktif lalu coba ulangi.
        </div>
      `;
    }
  }

  /* ══════════════════════════════════════════════════════════
     RENDER PRODUK REKOMENDASI
  ══════════════════════════════════════════════════════════ */
  function renderRekomendasi(items) {
    if (!rekomList) return;

    if (!items.length) {
      rekomList.innerHTML = `
        <div class="sa-rekom-empty">
          Tidak ada produk yang cocok saat ini.<br>
          Coba dengan pencahayaan yang lebih terang.
        </div>
      `;
      return;
    }

    rekomList.innerHTML = items.map(item => `
      <a class="sa-rekom-item"
         href="${item.product.url_detail}"
         data-tryon-url="${item.product.url_tryon}">
        <span class="sa-rekom-shade-dot"
              style="background:${item.hex_color};"></span>
        <span class="sa-rekom-item-info">
          <span class="sa-rekom-item-brand">${item.product.brand ?? ''}</span>
          <span class="sa-rekom-item-name">${item.product.name}</span>
          <span class="sa-rekom-item-shade">${item.shade_name} · ${cap(item.undertone)}</span>
        </span>
        <span class="sa-rekom-item-price">${item.product.price_formatted}</span>
      </a>
    `).join('');

    // Try-On button → produk pertama
    updateTryOnBtn(items[0]?.product?.url_tryon);

    // Klik item → ganti Try-On button
    rekomList.addEventListener('click', e => {
      const row = e.target.closest('.sa-rekom-item');
      if (!row) return;

      // Highlight baris terpilih
      document.querySelectorAll('.sa-rekom-item').forEach(el => {
        el.style.outline      = '';
        el.style.outlineOffset = '';
      });
      row.style.outline       = '2px solid #e65b7a';
      row.style.outlineOffset = '-2px';

      updateTryOnBtn(row.dataset.tryonUrl);
    });
  }

  function updateTryOnBtn(url) {
    const btn = document.getElementById('saBtnTryOn');
    if (btn && url) btn.href = url;
  }

  /* ══════════════════════════════════════════════════════════
     UTILITIES
  ══════════════════════════════════════════════════════════ */
  function cap(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
  }

  /* ══════════════════════════════════════════════════════════
     EVENT LISTENERS
  ══════════════════════════════════════════════════════════ */
  btnStart?.addEventListener('click', startCamera);
  btnStop?.addEventListener('click',  stopCamera);
  btnFlip?.addEventListener('click',  flipCamera);

  btnAnalyze?.addEventListener('click', runAnalysis);

  btnRetake?.addEventListener('click', () => {
    panelResult?.style.setProperty('display', 'none');
    panelWaiting?.style.setProperty('display', 'block');
    analysisResult = null;
    // Biarkan kamera dan MediaPipe tetap jalan
  });

  btnSave?.addEventListener('click', saveProfile);

  // Cleanup saat tab ditutup
  window.addEventListener('beforeunload', () => {
    stopStreamTracks();
    FaceDetectorModule.destroy();
  });

})();

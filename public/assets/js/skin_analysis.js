/**
 * skin_analysis.js
 * Fase 4 — AR Canvas Developer
 *
 * Orchestrator utama halaman Skin Tone AI.
 * Menggabungkan: FaceDetectorModule, SkinColorAnalyzer, dan ARCanvasModule.
 */

(function () {
  'use strict';

  /* ══════════════════════════════════════════════════════════
     DOM ELEMENTS
  ══════════════════════════════════════════════════════════ */
  const video           = document.getElementById('saVideo');
  const canvas          = document.getElementById('saCanvas');
  const cameraEmpty     = document.getElementById('saCameraEmpty');
  const cameraArea      = document.getElementById('saCameraArea');
  
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
  const panelSkeleton   = document.getElementById('saPanelSkeleton');
  const panelResult     = document.getElementById('saPanelResult');
  const panelSaveSuccess= document.getElementById('saSaveSuccess');

  const toneSwatchEl    = document.getElementById('saToneSwatch');
  const toneLabelEl     = document.getElementById('saToneLabel');
  const toneUndertoneEl = document.getElementById('saToneUndertone');
  const savePrompt      = document.getElementById('saSavePrompt');
  const rekomList       = document.getElementById('saRekomList');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

  /* ══════════════════════════════════════════════════════════
     STATE
  ══════════════════════════════════════════════════════════ */
  let stream              = null;
  let facingMode          = 'user';
  let latestLandmarks     = null;
  let stableFrameCount    = 0;
  let detectorStarted     = false;
  
  let currentPhase        = 'WAITING'; // WAITING | DETECTING | ANALYZING | RESULT
  let analysisResult      = null;      // { skin_tone_level, undertone, hex }
  let activeARShade       = null;      // Hex color untuk foundation AR mask

  const STABLE_THRESHOLD  = 10;

  /* ══════════════════════════════════════════════════════════
     CANVAS SIZING
  ══════════════════════════════════════════════════════════ */
  function syncCanvasSize() {
    if (!canvas || !cameraArea) return;
    // Paskan canvas dengan ukuran kontainer
    canvas.width  = cameraArea.clientWidth;
    canvas.height = cameraArea.clientHeight;
  }
  window.addEventListener('resize', syncCanvasSize);
  video?.addEventListener('loadedmetadata', syncCanvasSize);

  /* ══════════════════════════════════════════════════════════
     CAMERA CONTROL
  ══════════════════════════════════════════════════════════ */
  async function startCamera() {
    if (btnStart) btnStart.disabled = true;
    setStatus('Meminta akses kamera...', true);

    try {
      if (stream) stopStream();
      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: facingMode }, width: { ideal: 1280 }, height: { ideal: 720 } },
        audio: false,
      });

      video.srcObject = stream;
      video.style.display = 'block';
      canvas.style.display = 'block';
      if (cameraEmpty) cameraEmpty.style.display = 'none';

      await video.play();
      syncCanvasSize();

      if (btnStop) btnStop.disabled = false;
      if (pillDot) pillDot.style.background = '#22c55e';
      if (pillDot) pillDot.style.boxShadow = '0 0 0 4px rgba(34,197,94,0.15)';
      if (pillLabel) pillLabel.textContent = 'Kamera Aktif';

      currentPhase = 'DETECTING';
      startFaceDetection();

    } catch (err) {
      console.error(err);
      if (btnStart) btnStart.disabled = false;
      setStatus('Gagal mengakses kamera.', false);
    }
  }

  function stopStream() {
    if (stream) {
      stream.getTracks().forEach(t => t.stop());
      stream = null;
    }
  }

  function stopCamera() {
    stopStream();
    FaceDetectorModule.stopDetection();
    SkinColorAnalyzer.clearCanvas(canvas);

    video.style.display = 'none';
    canvas.style.display = 'none';
    video.srcObject = null;
    if (cameraEmpty) cameraEmpty.style.display = 'flex';

    if (btnStart) btnStart.disabled = false;
    if (btnStop) btnStop.disabled = true;
    if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'none';
    
    if (pillDot) pillDot.style.background = '#7d1030';
    if (pillDot) pillDot.style.boxShadow = '0 0 0 4px rgba(125,16,48,0.1)';
    if (pillLabel) pillLabel.textContent = 'Live Preview';

    latestLandmarks = null;
    stableFrameCount = 0;
    detectorStarted = false;
    currentPhase = 'WAITING';
    activeARShade = null;
    setStatus('', false);
  }

  /* ══════════════════════════════════════════════════════════
     FACE DETECTION & RENDERING LOOP
  ══════════════════════════════════════════════════════════ */
  function startFaceDetection() {
    if (detectorStarted) return;
    detectorStarted = true;

    FaceDetectorModule.startDetection(video, {
      onLoading: (msg) => setStatus(msg, true),
      onReady: () => setStatus('Arahkan wajah ke kamera...', false),
      onFace: (landmarks) => {
        latestLandmarks = landmarks;
        
        if (currentPhase === 'DETECTING') {
          stableFrameCount++;
          const stable = stableFrameCount >= STABLE_THRESHOLD;
          ARCanvasModule.drawDebugPoints(canvas, landmarks, video, stable);

          if (stable) {
            if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'block';
            setStatus('✓ Wajah siap dianalisis', false);
          } else {
            setStatus('Mendeteksi wajah...', true);
          }
        } 
        else if (currentPhase === 'RESULT' || currentPhase === 'ANALYZING') {
          // Jika sudah hasil, gambar AR Mask
          ARCanvasModule.drawARFoundation(canvas, landmarks, video, activeARShade);
        }
      },
      onNoFace: () => {
        stableFrameCount = 0;
        latestLandmarks = null;
        SkinColorAnalyzer.clearCanvas(canvas);
        if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'none';
        setStatus('Wajah tidak terdeteksi.', false);
      },
      onError: (msg) => {
        setStatus('Gagal memuat AI: ' + msg, false);
      }
    });
  }

  /* ══════════════════════════════════════════════════════════
     ANALISIS WARNA KULIT
  ══════════════════════════════════════════════════════════ */
  async function runAnalysis() {
    if (!latestLandmarks) return alert('Wajah belum terdeteksi sempurna.');

    currentPhase = 'ANALYZING';
    setStatus('Menganalisis warna kulit...', true);
    
    if (panelWaiting) panelWaiting.style.display = 'none';
    if (panelResult) panelResult.style.display = 'none';
    if (panelSkeleton) panelSkeleton.style.display = 'block';
    if (btnAnalyzeWrap) btnAnalyzeWrap.style.display = 'none';

    // 1. Pixel Sampling (Dari skin_color_analyzer.js)
    const sampled = SkinColorAnalyzer.sampleSkinColor(video, latestLandmarks);
    let skin_tone_level = 3, undertone = 'neutral', hex = '#C8A882';

    if (sampled && sampled.r > 0) {
      const cls = SkinColorAnalyzer.classify(sampled.r, sampled.g, sampled.b);
      skin_tone_level = cls.skin_tone_level;
      undertone = cls.undertone;
      hex = sampled.hex;
    }

    analysisResult = { skin_tone_level, undertone, hex };
    
    // Set background outer oval ke warna kulit user
    if (cameraArea) cameraArea.style.setProperty('--shade', hex);

    // 2. Fetch API Rekomendasi
    try {
      const resp = await fetch(window.__SA_ROUTES__.recommend, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ skin_tone_level, undertone })
      });
      const data = await resp.json();
      
      if (data.success) {
        showResults(data, hex);
      } else {
        throw new Error('API return false');
      }
    } catch (e) {
      console.error(e);
      setStatus('Gagal mengambil rekomendasi', false);
    }
  }

  function showResults(data, userHex) {
    currentPhase = 'RESULT';
    setStatus('', false);

    if (panelSkeleton) panelSkeleton.style.display = 'none';
    if (panelResult) panelResult.style.display = 'flex';
    if (savePrompt) savePrompt.style.display = 'flex';
    if (panelSaveSuccess) panelSaveSuccess.style.display = 'none';

    if (toneSwatchEl) toneSwatchEl.style.background = userHex;
    if (toneLabelEl) toneLabelEl.textContent = data.tone_label ?? '–';
    if (toneUndertoneEl) toneUndertoneEl.textContent = 'Undertone: ' + (data.undertone_label ?? '–');

    renderRekomendasi(data.recommendations ?? []);
  }

  /* ══════════════════════════════════════════════════════════
     UI RENDER & EVENT LISTENERS
  ══════════════════════════════════════════════════════════ */
  function renderRekomendasi(items) {
    if (!rekomList) return;

    if (!items.length) {
      rekomList.innerHTML = `<div class="sa-rekom-empty">Belum ada produk yang cocok dengan warna kulit ini.</div>`;
      activeARShade = null;
      return;
    }

    rekomList.innerHTML = items.map((item, index) => `
      <div class="sa-rekom-item" data-hex="${item.hex_color}">
        <div class="sa-rekom-shade-dot" style="background:${item.hex_color};"></div>
        <div class="sa-rekom-item-info">
          <span class="sa-rekom-item-brand">${item.product.brand ?? ''}</span>
          <span class="sa-rekom-item-name">${item.product.name}</span>
          <span class="sa-rekom-item-shade">${item.shade_name} · ${item.undertone}</span>
        </div>
        <div class="sa-rekom-item-price">${item.product.price_formatted}</div>
      </div>
    `).join('');

    // Default AR tidak aktif sampai user klik produk
    activeARShade = null;

    // Klik untuk ganti AR shade
    rekomList.querySelectorAll('.sa-rekom-item').forEach(el => {
      el.addEventListener('click', (e) => {
        // Reset styles
        rekomList.querySelectorAll('.sa-rekom-item').forEach(i => {
          i.style.borderColor = 'rgba(0,0,0,0.08)';
          i.style.background = '#fff';
        });
        // Active styles
        el.style.borderColor = '#e65b7a';
        el.style.background = 'rgba(230,91,122,0.05)';
        
        // Update AR Overlay
        activeARShade = el.getAttribute('data-hex');
      });
    });
  }

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

  async function saveProfile() {
    if (!analysisResult) return;
    if (btnSave) {
      btnSave.disabled = true;
      btnSave.textContent = 'Menyimpan...';
    }

    try {
      const resp = await fetch(window.__SA_ROUTES__.save, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
          skin_tone_level: analysisResult.skin_tone_level,
          undertone: analysisResult.undertone,
          hex_sample: analysisResult.hex,
        })
      });
      const data = await resp.json();
      
      if (data.success) {
        if (savePrompt) savePrompt.style.display = 'none';
        if (panelSaveSuccess) panelSaveSuccess.style.display = 'block';
      }
    } catch (err) {
      console.error(err);
      alert('Gagal menyimpan profil.');
    } finally {
      if (btnSave) {
        btnSave.disabled = false;
        btnSave.textContent = 'Simpan';
      }
    }
  }

  // --- EVENTS ---
  btnStart?.addEventListener('click', startCamera);
  btnStop?.addEventListener('click', stopCamera);
  btnFlip?.addEventListener('click', () => {
    facingMode = facingMode === 'user' ? 'environment' : 'user';
    if (stream) startCamera();
  });
  
  btnAnalyze?.addEventListener('click', runAnalysis);
  
  btnRetake?.addEventListener('click', () => {
    currentPhase = 'DETECTING';
    activeARShade = null;
    if (cameraArea) cameraArea.style.setProperty('--shade', '#e8ded7'); // reset outer bg

    if (panelResult) panelResult.style.display = 'none';
    if (panelWaiting) panelWaiting.style.display = 'block';
    
    // Stabilizer reset
    stableFrameCount = 0;
  });

  btnSave?.addEventListener('click', saveProfile);

  window.addEventListener('beforeunload', stopCamera);

})();

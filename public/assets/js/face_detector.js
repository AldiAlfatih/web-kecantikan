/**
 * face_detector.js
 * Fase 3 — AI / Computer Vision Developer
 *
 * MediaPipe Face Mesh wrapper.
 * - Lazy-loads MediaPipe dari CDN hanya saat diperlukan (tidak memperlambat load awal)
 * - Menjalankan deteksi wajah ~15 FPS untuk efisiensi baterai & CPU
 * - Mengekspos API sederhana: startDetection / stopDetection / destroy
 */

window.FaceDetectorModule = (function () {
  'use strict';

  /* ── Konfigurasi ─────────────────────────────────────────── */
  const CDN_BASE   = 'https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh@0.4';
  const TARGET_FPS = 15;                    // frame rate target (hemat CPU)
  const FRAME_GAP  = 1000 / TARGET_FPS;     // ms antar frame

  /* ── Internal state ──────────────────────────────────────── */
  let faceMesh      = null;
  let isRunning     = false;
  let rafId         = null;
  let lastFrameTime = 0;
  let videoRef      = null;

  /* ── Callbacks dari pemanggil ────────────────────────────── */
  let _onFace    = null;
  let _onNoFace  = null;
  let _onLoading = null;
  let _onReady   = null;
  let _onError   = null;

  /* ══════════════════════════════════════════════════════════
     SCRIPT LOADER
     Muat script CDN secara dinamis (hanya sekali, idempotent)
  ══════════════════════════════════════════════════════════ */
  function loadScript(src) {
    return new Promise((resolve, reject) => {
      // Cek apakah sudah dimuat sebelumnya
      if (document.querySelector(`script[data-mp="${src}"]`)) {
        resolve();
        return;
      }
      const s           = document.createElement('script');
      s.src             = src;
      s.dataset.mp      = src;
      s.crossOrigin     = 'anonymous';
      s.onload          = resolve;
      s.onerror         = () => reject(new Error(`Gagal memuat: ${src}`));
      document.head.appendChild(s);
    });
  }

  async function loadMediaPipe() {
    _onLoading?.('Memuat library AI (MediaPipe)...');
    // face_mesh.js menyertakan semua dependency yang dibutuhkan
    await loadScript(`${CDN_BASE}/face_mesh.js`);
    _onLoading?.('Menginisialisasi model deteksi wajah...');
  }

  /* ══════════════════════════════════════════════════════════
     FACE MESH INITIALIZATION
  ══════════════════════════════════════════════════════════ */
  async function initFaceMesh() {
    if (faceMesh) return; // sudah pernah diinisialisasi

    // Muat model dari CDN — file WASM & model binary akan di-cache browser
    faceMesh = new FaceMesh({
      locateFile: (file) => `${CDN_BASE}/${file}`,
    });

    faceMesh.setOptions({
      maxNumFaces          : 1,       // hanya 1 wajah (performa lebih baik)
      refineLandmarks      : true,    // aktifkan iris landmark (468 → 478 titik)
      minDetectionConfidence: 0.55,
      minTrackingConfidence : 0.55,
    });

    // Callback hasil deteksi per-frame
    faceMesh.onResults((results) => {
      const faces = results.multiFaceLandmarks;
      if (faces && faces.length > 0) {
        // Wajah terdeteksi — kirim array 478 landmark
        _onFace?.(faces[0], results);
      } else {
        // Tidak ada wajah di frame ini
        _onNoFace?.();
      }
    });

    // Tunggu model selesai loading (bisa 2-5 detik pertama kali)
    await faceMesh.initialize();
    _onReady?.();
  }

  /* ══════════════════════════════════════════════════════════
     DETECTION LOOP (requestAnimationFrame @ ~15 FPS)
  ══════════════════════════════════════════════════════════ */
  function detectionLoop(timestamp) {
    if (!isRunning) return;

    // Throttle ke TARGET_FPS
    if (timestamp - lastFrameTime >= FRAME_GAP) {
      lastFrameTime = timestamp;

      if (faceMesh && videoRef && videoRef.readyState >= 2) {
        // Kirim frame ke MediaPipe (async tapi tidak di-await agar loop tidak blocking)
        faceMesh.send({ image: videoRef }).catch(() => {
          // Abaikan error pada frame individual (bisa terjadi saat video resize)
        });
      }
    }

    rafId = requestAnimationFrame(detectionLoop);
  }

  /* ══════════════════════════════════════════════════════════
     PUBLIC API
  ══════════════════════════════════════════════════════════ */

  /**
   * Mulai deteksi wajah pada video element yang diberikan.
   * MediaPipe akan di-load dari CDN secara lazy.
   *
   * @param {HTMLVideoElement} videoEl  - element video yang sudah punya stream
   * @param {Object}           callbacks
   *   .onFace(landmarks, results)      - dipanggil setiap frame ada wajah
   *   .onNoFace()                      - dipanggil setiap frame tanpa wajah
   *   .onLoading(message)              - status saat loading CDN/model
   *   .onReady()                       - model siap, deteksi dimulai
   *   .onError(message)                - error loading
   */
  async function startDetection(videoEl, callbacks = {}) {
    videoRef   = videoEl;
    _onFace    = callbacks.onFace    ?? null;
    _onNoFace  = callbacks.onNoFace  ?? null;
    _onLoading = callbacks.onLoading ?? null;
    _onReady   = callbacks.onReady   ?? null;
    _onError   = callbacks.onError   ?? null;
    isRunning  = true;

    try {
      await loadMediaPipe();
      await initFaceMesh();
      rafId = requestAnimationFrame(detectionLoop);
    } catch (err) {
      console.error('[FaceDetector]', err);
      isRunning = false;
      _onError?.(err.message ?? 'Unknown error');
    }
  }

  /**
   * Hentikan loop deteksi (stream kamera tetap jalan).
   */
  function stopDetection() {
    isRunning = false;
    if (rafId) {
      cancelAnimationFrame(rafId);
      rafId = null;
    }
  }

  /**
   * Hancurkan model sepenuhnya (bebaskan memori WASM).
   * Panggil ini saat halaman di-unload.
   */
  function destroy() {
    stopDetection();
    if (faceMesh) {
      try { faceMesh.close(); } catch (_) {}
      faceMesh = null;
    }
  }

  return { startDetection, stopDetection, destroy };

})();

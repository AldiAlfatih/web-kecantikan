/**
 * skin_color_analyzer.js
 * Fase 3 — AI / Computer Vision Developer
 *
 * Modul analisis warna kulit berbasis MediaPipe Face Mesh landmarks:
 * 1. PIXEL SAMPLING — ambil warna rata-rata dari titik landmark kulit wajah
 * 2. RULE-BASED IF-THEN — klasifikasi skin_tone_level & undertone dari HSV
 * 3. CANVAS OVERLAY — gambar face oval + titik sampling di atas video
 */

window.SkinColorAnalyzer = (function () {
  'use strict';

  /* ══════════════════════════════════════════════════════════
     LANDMARK INDICES (MediaPipe Face Mesh 468-point + iris)

     Dipilih dari area kulit yang relatif bebas dari:
     - Bayangan hidung & alis
     - Rambut & batas wajah
     - Area mata & bibir (warna berbeda)
  ══════════════════════════════════════════════════════════ */

  // Area pipi kanan (dari perspektif MediaPipe — original frame, bukan mirror)
  const CHEEK_RIGHT = [93, 132, 177, 215, 138];

  // Area pipi kiri
  const CHEEK_LEFT  = [323, 361, 401, 435, 367];

  // Dahi tengah (hindari garis rambut)
  const FOREHEAD    = [9, 10, 151, 108, 337];

  // Semua titik sampling digabung
  const SAMPLE_LANDMARKS = [...CHEEK_RIGHT, ...CHEEK_LEFT, ...FOREHEAD];

  // Kontur wajah (face oval) untuk outline AR
  const FACE_OVAL = [
    10, 338, 297, 332, 284, 251, 389, 356, 454, 323, 361, 288,
    397, 365, 379, 378, 400, 377, 152, 148, 176, 149, 150, 136,
    172,  58, 132,  93, 234, 127, 162,  21,  54, 103,  67, 109, 10,
  ];

  /* ══════════════════════════════════════════════════════════
     PIXEL SAMPLING

     Mengambil warna rata-rata dari area 5×5 piksel di sekitar
     setiap landmark point pada frame video yang belum di-mirror.

     Note: ctx.drawImage(videoEl) menghasilkan frame ORIGINAL
     (bukan mirror CSS). MediaPipe landmarks juga dalam koordinat
     original. Jadi sampling langsung tanpa flip. ✓
  ══════════════════════════════════════════════════════════ */

  /**
   * Sample warna 1 landmark dari frame video.
   * @returns {{ r, g, b }} atau null jika koordinat di luar frame
   */
  function samplePixel(ctx, landmarks, idx, frameW, frameH) {
    const lm = landmarks[idx];
    if (!lm) return null;

    const cx = Math.round(lm.x * frameW);
    const cy = Math.round(lm.y * frameH);
    const R  = 5; // sample radius (piksel)

    const x0 = Math.max(0, cx - R);
    const y0 = Math.max(0, cy - R);
    const w  = Math.min(R * 2, frameW - x0);
    const h  = Math.min(R * 2, frameH - y0);
    if (w <= 0 || h <= 0) return null;

    const data = ctx.getImageData(x0, y0, w, h).data;
    let sumR = 0, sumG = 0, sumB = 0, count = 0;

    for (let i = 0; i < data.length; i += 4) {
      // Abaikan piksel hampir transparan (tidak relevan)
      if (data[i + 3] < 200) continue;
      sumR += data[i];
      sumG += data[i + 1];
      sumB += data[i + 2];
      count++;
    }

    if (!count) return null;
    return {
      r: Math.round(sumR / count),
      g: Math.round(sumG / count),
      b: Math.round(sumB / count),
    };
  }

  /**
   * Sample rata-rata warna kulit dari semua SAMPLE_LANDMARKS.
   *
   * @param {HTMLVideoElement}  videoEl   - elemen video dengan stream aktif
   * @param {Array}             landmarks - array 468/478 landmark dari MediaPipe
   * @returns {{ r, g, b, hex }} atau null jika gagal
   */
  function sampleSkinColor(videoEl, landmarks) {
    if (!videoEl || !landmarks || !landmarks.length) return null;

    const W = videoEl.videoWidth  || 640;
    const H = videoEl.videoHeight || 480;

    // Buat canvas sementara untuk membaca pixel data
    const tmp = document.createElement('canvas');
    tmp.width = W;
    tmp.height = H;
    const ctx = tmp.getContext('2d');

    // Gambar frame video ke canvas (original, belum di-mirror)
    ctx.drawImage(videoEl, 0, 0, W, H);

    // Kumpulkan semua sample valid
    const pixels = SAMPLE_LANDMARKS
      .map(idx => samplePixel(ctx, landmarks, idx, W, H))
      .filter(Boolean);

    if (!pixels.length) return null;

    // Rata-rata per channel
    const r = Math.round(pixels.reduce((s, p) => s + p.r, 0) / pixels.length);
    const g = Math.round(pixels.reduce((s, p) => s + p.g, 0) / pixels.length);
    const b = Math.round(pixels.reduce((s, p) => s + p.b, 0) / pixels.length);

    return { r, g, b, hex: rgbToHex(r, g, b) };
  }

  /* ══════════════════════════════════════════════════════════
     RULE-BASED IF-THEN CLASSIFICATION

     Menggunakan ruang warna HSV (Hue-Saturation-Value) karena:
     - Value (V) sangat berkorelasi dengan kecerahan kulit
     - Hue (H) membedakan undertone hangat vs dingin vs netral

     Rule Tree:
     ┌─ TONE LEVEL (berdasarkan V)
     │   V ≥ 0.82 → 1 (Fair)
     │   V ≥ 0.68 → 2 (Light)
     │   V ≥ 0.54 → 3 (Medium / Kuning Langsat)
     │   V ≥ 0.40 → 4 (Tan / Sawo Matang Terang)
     │   V ≥ 0.26 → 5 (Deep / Sawo Matang Gelap)
     │   V <  0.26 → 6 (Dark)
     │
     └─ UNDERTONE (berdasarkan H)
         H ∈ [0°–35°] ∪ [340°–360°] → warm (merah/oranye)
         H ∈ [180°–270°]             → cool (biru/ungu)
         else                        → neutral
  ══════════════════════════════════════════════════════════ */

  /**
   * Klasifikasi warna kulit dari nilai RGB rata-rata.
   *
   * @param {number} r  0–255
   * @param {number} g  0–255
   * @param {number} b  0–255
   * @returns {{ skin_tone_level: number, undertone: string, h, s, v }}
   */
  function classify(r, g, b) {
    const { h, s, v } = rgbToHsv(r, g, b);

    // ── TONE LEVEL ──────────────────────────────────────────
    let skin_tone_level;
    if      (v >= 0.82)              skin_tone_level = 1; // Fair
    else if (v >= 0.68)              skin_tone_level = 2; // Light
    else if (v >= 0.54)              skin_tone_level = 3; // Medium
    else if (v >= 0.40)              skin_tone_level = 4; // Tan
    else if (v >= 0.26)              skin_tone_level = 5; // Deep
    else                             skin_tone_level = 6; // Dark

    // ── UNDERTONE ───────────────────────────────────────────
    let undertone;
    if      ((h >= 0 && h <= 35) || h >= 340) undertone = 'warm';
    else if (h >= 180 && h <= 270)            undertone = 'cool';
    else                                      undertone = 'neutral';

    return { skin_tone_level, undertone, h, s, v };
  }

  /* ══════════════════════════════════════════════════════════
     CANVAS OVERLAY DRAWING

     Note tentang koordinat:
     - Video + canvas keduanya punya CSS `transform: scaleX(-1)` (mirror)
     - MediaPipe landmarks dalam koordinat original (belum mirror)
     - Saat kita gambar di canvas di posisi (x, y), CSS mirror akan
       membuatnya tampil di posisi (canvasWidth - x, y) secara visual
     - Video juga CSS-mirror: piksel asli di x tampil di (clientWidth - x) visual
     - Kedua transformasi saling "cancel out" → alignment tetap benar ✓

     Untuk menangani object-fit: cover (video mungkin terkrop):
     - Hitung scale & offset agar koordinat landmark memetakan ke
       area video yang sebenarnya tampil di layar.
  ══════════════════════════════════════════════════════════ */

  /**
   * Hitung transformasi cover untuk memetakan koordinat video ke canvas.
   * (Diperlukan karena video menggunakan object-fit: cover)
   */
  function computeCoverTransform(canvasEl, videoEl) {
    const cW = canvasEl.width;
    const cH = canvasEl.height;
    const vW = videoEl.videoWidth  || cW;
    const vH = videoEl.videoHeight || cH;

    // Cover: scale ke rasio yang lebih besar (tidak ada letterbox, tapi ada crop)
    const scale   = Math.max(cW / vW, cH / vH);
    const offsetX = (cW - vW * scale) / 2;
    const offsetY = (cH - vH * scale) / 2;

    return { scale, offsetX, offsetY, vW, vH };
  }

  /** Konversi landmark (0–1 normalized) ke koordinat canvas pixel */
  function lmToCanvas(lm, transform) {
    const { scale, offsetX, offsetY, vW, vH } = transform;
    return {
      x: lm.x * vW * scale + offsetX,
      y: lm.y * vH * scale + offsetY,
    };
  }

  /**
   * Gambar face mesh overlay di atas canvas.
   *
   * @param {HTMLCanvasElement} canvasEl
   * @param {Array}             landmarks  - array landmark dari MediaPipe
   * @param {HTMLVideoElement}  videoEl    - untuk menghitung cover transform
   * @param {boolean}           stable     - true = wajah stabil (warna berbeda)
   */
  function drawFaceOverlay(canvasEl, landmarks, videoEl, stable) {
    const ctx = canvasEl.getContext('2d');
    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);

    if (!landmarks || !landmarks.length) return;

    const T = computeCoverTransform(canvasEl, videoEl);

    // ① Face oval outline
    ctx.beginPath();
    FACE_OVAL.forEach((idx, i) => {
      const lm = landmarks[idx];
      if (!lm) return;
      const p = lmToCanvas(lm, T);
      i === 0 ? ctx.moveTo(p.x, p.y) : ctx.lineTo(p.x, p.y);
    });
    ctx.closePath();

    if (stable) {
      // Wajah stabil → outline hijau
      ctx.strokeStyle = 'rgba(34,197,94,0.80)';
      ctx.fillStyle   = 'rgba(34,197,94,0.04)';
    } else {
      // Belum stabil → outline merah muda (brand color)
      ctx.strokeStyle = 'rgba(230,91,122,0.72)';
      ctx.fillStyle   = 'rgba(230,91,122,0.04)';
    }
    ctx.lineWidth = 2;
    ctx.stroke();
    ctx.fill();

    // ② Titik-titik sampling (pipi + dahi)
    const dotColor = stable ? 'rgba(34,197,94,0.85)' : 'rgba(230,91,122,0.80)';
    SAMPLE_LANDMARKS.forEach(idx => {
      const lm = landmarks[idx];
      if (!lm) return;
      const p = lmToCanvas(lm, T);

      ctx.beginPath();
      ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
      ctx.fillStyle   = dotColor;
      ctx.fill();
      ctx.strokeStyle = 'rgba(255,255,255,0.88)';
      ctx.lineWidth   = 1.5;
      ctx.stroke();
    });
  }

  /** Bersihkan canvas overlay. */
  function clearCanvas(canvasEl) {
    if (!canvasEl) return;
    canvasEl.getContext('2d').clearRect(0, 0, canvasEl.width, canvasEl.height);
  }

  /* ══════════════════════════════════════════════════════════
     COLOR SPACE UTILITIES
  ══════════════════════════════════════════════════════════ */

  function rgbToHsv(r, g, b) {
    const rn = r / 255, gn = g / 255, bn = b / 255;
    const max = Math.max(rn, gn, bn);
    const min = Math.min(rn, gn, bn);
    const d   = max - min;

    let h = 0;
    if (d > 0) {
      if      (max === rn) h = ((gn - bn) / d) % 6;
      else if (max === gn) h = (bn - rn) / d + 2;
      else                 h = (rn - gn) / d + 4;
      h = Math.round(h * 60);
      if (h < 0) h += 360;
    }

    return { h, s: max === 0 ? 0 : d / max, v: max };
  }

  function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
  }

  /* ── Public API ──────────────────────────────────────────── */
  return {
    sampleSkinColor,
    classify,
    drawFaceOverlay,
    clearCanvas,
    FACE_OVAL,
    SAMPLE_LANDMARKS,
  };

})();

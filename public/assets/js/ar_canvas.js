/**
 * ar_canvas.js
 * Fase 4 — AR Canvas Developer
 *
 * Menggambar overlay foundation menggunakan MediaPipe Face Mesh.
 * Menggunakan fill 'evenodd' untuk mewarnai wajah tetapi melubangi
 * area mata, alis, dan bibir.
 */

window.ARCanvasModule = (function () {
  'use strict';

  /* ══════════════════════════════════════════════════════════
     LANDMARK INDICES
  ══════════════════════════════════════════════════════════ */
  const FACE_OVAL = [
    10, 338, 297, 332, 284, 251, 389, 356, 454, 323, 361, 288,
    397, 365, 379, 378, 400, 377, 152, 148, 176, 149, 150, 136,
    172,  58, 132,  93, 234, 127, 162,  21,  54, 103,  67, 109,
  ];

  const LIPS_OUTER = [
    61, 146, 91, 181, 84, 17, 314, 405, 321, 375, 291, 308,
    324, 318, 402, 317, 14, 87, 178, 88, 95
  ];

  const LEFT_EYE = [
    33, 7, 163, 144, 145, 153, 154, 155, 133,
    173, 157, 158, 159, 160, 161, 246
  ];

  const RIGHT_EYE = [
    263, 249, 390, 373, 374, 380, 381, 382, 362,
    398, 384, 385, 386, 387, 388, 466
  ];

  const LEFT_EYEBROW = [70, 63, 105, 66, 107, 55, 65, 52, 53, 46];
  const RIGHT_EYEBROW = [300, 293, 334, 296, 336, 285, 295, 282, 283, 276];

  /* ══════════════════════════════════════════════════════════
     CORE DRAWING LOGIC
  ══════════════════════════════════════════════════════════ */

  /**
   * Mengonversi persentase landmark ke koordinat canvas berdasarkan video cover
   */
  function computeTransform(canvasEl, videoEl) {
    const cW = canvasEl.width;
    const cH = canvasEl.height;
    const vW = videoEl.videoWidth || cW;
    const vH = videoEl.videoHeight || cH;

    const scale = Math.max(cW / vW, cH / vH);
    const offsetX = (cW - vW * scale) / 2;
    const offsetY = (cH - vH * scale) / 2;

    return { scale, offsetX, offsetY, vW, vH };
  }

  function addPolygonToPath(ctx, landmarks, indices, transform) {
    const { scale, offsetX, offsetY, vW, vH } = transform;
    
    indices.forEach((idx, i) => {
      const lm = landmarks[idx];
      if (!lm) return;
      const x = lm.x * vW * scale + offsetX;
      const y = lm.y * vH * scale + offsetY;
      
      if (i === 0) ctx.moveTo(x, y);
      else ctx.lineTo(x, y);
    });
    ctx.closePath();
  }

  /**
   * Menggambar foundation AR overlay
   * 
   * @param {HTMLCanvasElement} canvasEl 
   * @param {Array} landmarks - 478 MediaPipe landmarks
   * @param {HTMLVideoElement} videoEl 
   * @param {String} shadeHex - Warna foundation, null jika tidak ada
   */
  function drawARFoundation(canvasEl, landmarks, videoEl, shadeHex) {
    const ctx = canvasEl.getContext('2d');
    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);

    if (!landmarks || !landmarks.length || !shadeHex) return;

    const T = computeTransform(canvasEl, videoEl);

    // Filter feathering & opacity
    // Foundation harus membaur (soft-light atau multiply)
    ctx.save();
    
    // Feather edge menggunakan filter blur
    ctx.filter = 'blur(8px)';
    ctx.globalAlpha = 0.55; // Opacity foundation 
    ctx.globalCompositeOperation = 'multiply'; // Blending color

    ctx.beginPath();
    
    // Bentuk batas luar (Face Oval)
    addPolygonToPath(ctx, landmarks, FACE_OVAL, T);
    
    // Bentuk lubang (Mata, Alis, Bibir)
    addPolygonToPath(ctx, landmarks, LIPS_OUTER, T);
    addPolygonToPath(ctx, landmarks, LEFT_EYE, T);
    addPolygonToPath(ctx, landmarks, RIGHT_EYE, T);
    addPolygonToPath(ctx, landmarks, LEFT_EYEBROW, T);
    addPolygonToPath(ctx, landmarks, RIGHT_EYEBROW, T);

    ctx.fillStyle = shadeHex;
    
    // Fill rule evenodd: Jika shape di dalam shape, maka ia menjadi transparan (lubang)
    ctx.fill('evenodd');
    
    ctx.restore();
  }

  /**
   * Menggambar titik sampling untuk debugging
   */
  function drawDebugPoints(canvasEl, landmarks, videoEl, stable) {
    const ctx = canvasEl.getContext('2d');
    ctx.clearRect(0, 0, canvasEl.width, canvasEl.height);
    if (!landmarks || !landmarks.length) return;

    const T = computeTransform(canvasEl, videoEl);

    // Oval outline
    ctx.beginPath();
    addPolygonToPath(ctx, landmarks, FACE_OVAL, T);
    ctx.strokeStyle = stable ? 'rgba(34,197,94,0.6)' : 'rgba(230,91,122,0.6)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    // Sampling dots (dari SkinColorAnalyzer)
    if (window.SkinColorAnalyzer && window.SkinColorAnalyzer.SAMPLE_LANDMARKS) {
      ctx.fillStyle = stable ? 'rgba(34,197,94,0.8)' : 'rgba(230,91,122,0.8)';
      window.SkinColorAnalyzer.SAMPLE_LANDMARKS.forEach(idx => {
        const lm = landmarks[idx];
        if (!lm) return;
        const x = lm.x * T.vW * T.scale + T.offsetX;
        const y = lm.y * T.vH * T.scale + T.offsetY;
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, Math.PI * 2);
        ctx.fill();
      });
    }
  }

  return {
    drawARFoundation,
    drawDebugPoints
  };

})();

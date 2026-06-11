(function () {
  const video = document.getElementById("saVideo");
  const canvas = document.getElementById("saCanvas");
  const empty = document.getElementById("cameraEmpty");
  const cameraArea = document.getElementById("cameraArea");
  
  const statusOverlay = document.getElementById("saStatusOverlay");
  const statusText = document.getElementById("saStatusText");

  const btnStart = document.getElementById("btnStartCamera");
  const btnStop = document.getElementById("btnStopCamera");
  const btnFlip = document.getElementById("btnFlip");

  const shadeList  = document.getElementById("shadeList");
  const shadeLabel = document.getElementById("shadeLabel");
  const shadeHex   = document.getElementById("shadeHex");
  const shadeBadge = document.getElementById("shadeBadge");
  const shadeSwatch= document.getElementById("shadeSwatch");
  const subtitleText = document.getElementById("subtitleText");

  const shadeIdInput = document.getElementById("shadeIdInput");
  const qtyInput = document.getElementById("qtyInput");
  const qtyPlus = document.getElementById("qtyPlus");
  const qtyMinus = document.getElementById("qtyMinus");

  const productName = (window.__TRYON_PRODUCT__?.name) || "Product";
  let currentShadeHex = (window.__TRYON_SHADE__?.hex) || null;
  const initShadeId = (window.__TRYON_SHADE__?.id) || null;

  if (cameraArea && currentShadeHex) cameraArea.style.setProperty("--shade", currentShadeHex);
  if (shadeIdInput && initShadeId) shadeIdInput.value = initShadeId;

  let stream = null;
  let facingMode = "user";

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

  function syncCanvasSize() {
    if (!canvas || !cameraArea) return;
    canvas.width  = cameraArea.clientWidth;
    canvas.height = cameraArea.clientHeight;
  }
  window.addEventListener('resize', syncCanvasSize);
  video?.addEventListener('loadedmetadata', syncCanvasSize);

  async function startCamera() {
    try {
      if (stream) stopCamera();
      
      setStatus('Meminta akses kamera...', true);
      btnStart.disabled = true;

      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: facingMode }, width: { ideal: 1280 }, height: { ideal: 720 } },
        audio: false,
      });

      video.srcObject = stream;
      video.style.display = "block";
      canvas.style.display = "block";
      empty.style.display = "none";
      btnStop.disabled = false;
      
      await video.play();
      syncCanvasSize();

      // Start Face Detection
      if (window.FaceDetectorModule) {
        window.FaceDetectorModule.startDetection(video, {
          onLoading: (msg) => setStatus(msg, true),
          onReady: () => setStatus('', false),
          onFace: (landmarks) => {
            setStatus('', false);
            if (window.ARCanvasModule) {
              window.ARCanvasModule.drawARFoundation(canvas, landmarks, video, currentShadeHex);
            }
          },
          onNoFace: () => {
            setStatus('Wajah tidak terdeteksi', false);
            if (canvas) {
              const ctx = canvas.getContext('2d');
              ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
          },
          onError: (msg) => setStatus('Gagal memuat AI: ' + msg, false)
        });
      }

    } catch (err) {
      console.error(err);
      setStatus('Gagal mengakses kamera.', false);
    } finally {
      btnStart.disabled = false;
    }
  }

  function stopCamera() {
    if (stream) {
      stream.getTracks().forEach((t) => t.stop());
      stream = null;
    }
    
    if (window.FaceDetectorModule) window.FaceDetectorModule.stopDetection();

    video.srcObject = null;
    video.style.display = "none";
    canvas.style.display = "none";
    empty.style.display = "flex";
    btnStop.disabled = true;
    setStatus('', false);
  }

  function flipCamera() {
    facingMode = facingMode === "user" ? "environment" : "user";
    if (stream) startCamera();
  }

  btnStart?.addEventListener("click", startCamera);
  btnStop?.addEventListener("click", stopCamera);
  btnFlip?.addEventListener("click", flipCamera);
  window.addEventListener("beforeunload", stopCamera);

  // qty buttons
  qtyPlus?.addEventListener("click", () => {
    if (!qtyInput) return;
    qtyInput.value = String(parseInt(qtyInput.value || "1", 10) + 1);
  });

  qtyMinus?.addEventListener("click", () => {
    if (!qtyInput) return;
    const current = parseInt(qtyInput.value || "1", 10);
    qtyInput.value = String(Math.max(1, current - 1));
  });

  // Switch shade from right panel
  shadeList?.addEventListener("click", function (e) {
    const btn = e.target.closest(".shade-item");
    if (!btn) return;

    document.querySelectorAll(".shade-item").forEach((el) => el.classList.remove("active"));
    btn.classList.add("active");

    const shadeId = btn.dataset.id || "";
    currentShadeHex = btn.dataset.hex || null;
    const name = btn.dataset.name || "Shade";
    const badge = btn.dataset.badge || "Shade";

    cameraArea?.style.setProperty("--shade", currentShadeHex || "#F1D6C8");

    if (shadeLabel) shadeLabel.textContent = name;
    if (shadeHex) shadeHex.textContent = currentShadeHex;
    if (shadeBadge) shadeBadge.textContent = badge;
    if (shadeSwatch) shadeSwatch.style.background = currentShadeHex;
    if (subtitleText) subtitleText.textContent = `${productName} – ${name}`;

    if (shadeIdInput) shadeIdInput.value = shadeId;
  });
})();

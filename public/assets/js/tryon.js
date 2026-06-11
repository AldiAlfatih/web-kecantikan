(function () {
  const video = document.getElementById("cameraVideo");
  const empty = document.getElementById("cameraEmpty");
  const btnStart = document.getElementById("btnStartCamera");
  const btnStop = document.getElementById("btnStopCamera");
  const btnFlip = document.getElementById("btnFlip");

  const cameraArea = document.getElementById("cameraArea");
  const shadeList  = document.getElementById("shadeList");
  const shadeLabel = document.getElementById("shadeLabel");
  const shadeHex   = document.getElementById("shadeHex");
  const shadeBadge = document.getElementById("shadeBadge");
  const shadeSwatch= document.getElementById("shadeSwatch");
  const subtitleText = document.getElementById("subtitleText");

  // ✅ input untuk add to cart
  const shadeIdInput = document.getElementById("shadeIdInput");

  // qty controls
  const qtyInput = document.getElementById("qtyInput");
  const qtyPlus = document.getElementById("qtyPlus");
  const qtyMinus = document.getElementById("qtyMinus");

  const productName = (window.__TRYON_PRODUCT__?.name) || "Product";
  const initShade = window.__TRYON_SHADE__ || { id:null, hex:"#F1D6C8", name:"Shade", badge:"Shade" };

  if (cameraArea) cameraArea.style.setProperty("--shade", initShade.hex);
  if (shadeIdInput && initShade.id) shadeIdInput.value = initShade.id;

  let stream = null;
  let facingMode = "user";

  async function startCamera() {
    try {
      if (stream) stopCamera();

      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: facingMode } },
        audio: false,
      });

      video.srcObject = stream;
      video.style.display = "block";
      empty.style.display = "none";
      btnStop.disabled = false;

    } catch (err) {
      console.error(err);
      alert("Tidak bisa mengakses kamera. Pastikan izin kamera aktif dan pakai localhost/https.");
    }
  }

  function stopCamera() {
    if (!stream) return;
    stream.getTracks().forEach((t) => t.stop());
    stream = null;

    video.srcObject = null;
    video.style.display = "none";
    empty.style.display = "flex";
    btnStop.disabled = true;
  }

  function flipCamera() {
    facingMode = facingMode === "user" ? "environment" : "user";
    if (stream) startCamera();
  }

  btnStart?.addEventListener("click", startCamera);
  btnStop?.addEventListener("click", stopCamera);
  btnFlip?.addEventListener("click", flipCamera);
  window.addEventListener("beforeunload", stopCamera);

  // ✅ qty buttons
  qtyPlus?.addEventListener("click", () => {
    if (!qtyInput) return;
    qtyInput.value = String(parseInt(qtyInput.value || "1", 10) + 1);
  });

  qtyMinus?.addEventListener("click", () => {
    if (!qtyInput) return;
    const current = parseInt(qtyInput.value || "1", 10);
    qtyInput.value = String(Math.max(1, current - 1));
  });

  // ✅ Switch shade from right panel
  shadeList?.addEventListener("click", function (e) {
    const btn = e.target.closest(".shade-item");
    if (!btn) return;

    document.querySelectorAll(".shade-item").forEach((el) => el.classList.remove("active"));
    btn.classList.add("active");

    const shadeId = btn.dataset.id || "";
    const hex = btn.dataset.hex || "#F1D6C8";
    const name = btn.dataset.name || "Shade";
    const badge = btn.dataset.badge || "Shade";

    cameraArea?.style.setProperty("--shade", hex);

    if (shadeLabel) shadeLabel.textContent = name;
    if (shadeHex) shadeHex.textContent = hex;
    if (shadeBadge) shadeBadge.textContent = badge;
    if (shadeSwatch) shadeSwatch.style.background = hex;
    if (subtitleText) subtitleText.textContent = `${productName} – ${name}`;

    // ✅ update hidden shade_id supaya Add to Cart sesuai shade aktif
    if (shadeIdInput) shadeIdInput.value = shadeId;
  });
})();

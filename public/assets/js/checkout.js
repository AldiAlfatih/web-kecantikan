document.addEventListener("DOMContentLoaded", () => {
  const paymentMethod = document.getElementById("paymentMethod");
  const bankField = document.getElementById("bankField");
  const bankSelect = document.getElementById("bankSelect");
  const transferFields = document.getElementById("transferFields");
  const paymentProof = document.getElementById("paymentProof");
  const fileName = document.getElementById("fileName");

  const destName = document.getElementById("destName");
  const destOwner = document.getElementById("destOwner");
  const destNumber = document.getElementById("destNumber");
  const copyBtn = document.getElementById("copyBtn");

  const codNote = document.getElementById("codNote");
  const storeNote = document.getElementById("storeNote");

  const deliveryMethod = document.getElementById("deliveryMethod");
  const deliveryHint = document.getElementById("deliveryHint");
  const pickupNote = document.getElementById("pickupNote");

  const shippingRow = document.getElementById("shippingRow");
  const shippingText = document.getElementById("shippingText");
  const totalHint = document.getElementById("totalHint");

  // map rekening
  const accounts = {
    bca:     { name: "BCA",     number: "1234567890", owner: "a/n FaceShop" },
    bri:     { name: "BRI",     number: "9876543210", owner: "a/n FaceShop" },
    bni:     { name: "BNI",     number: "1122334455", owner: "a/n FaceShop" },
    mandiri: { name: "Mandiri", number: "5566778899", owner: "a/n FaceShop" },
    dana:    { name: "DANA",    number: "0812-1111-2222", owner: "a/n FaceShop" },
    gopay:   { name: "GoPay",   number: "0812-3333-4444", owner: "a/n FaceShop" },
    ovo:     { name: "OVO",     number: "0812-5555-6666", owner: "a/n FaceShop" },
  };

  function setTransferDestination(key) {
    const acc = accounts[key];
    if (!acc) {
      destName.textContent = "—";
      destNumber.textContent = "—";
      destOwner.textContent = "a/n FaceShop";
      copyBtn.disabled = true;
      copyBtn.style.opacity = 0.6;
      return;
    }

    destName.textContent = acc.name;
    destNumber.textContent = acc.number;
    destOwner.textContent = acc.owner;
    copyBtn.disabled = false;
    copyBtn.style.opacity = 1;
  }

  function applyDeliveryUI() {
    const d = deliveryMethod.value;

    if (d === "pickup") {
      deliveryHint.style.display = "none";
      pickupNote.style.display = "flex";

      // ongkir tidak berlaku
      shippingRow.style.display = "none";
      totalHint.textContent = "* Pick Up: tidak ada ongkir.";
    } else {
      deliveryHint.style.display = "block";
      pickupNote.style.display = "none";

      shippingRow.style.display = "flex";
      shippingText.textContent = "Rp 10.000 – Rp 20.000";
      totalHint.textContent = "* Total belum termasuk ongkir kurir (jika diantar). Ongkir diinformasikan oleh kurir.";
    }
  }

  function applyPaymentUI() {
    const m = paymentMethod.value;

    // reset
    codNote.style.display = "none";
    storeNote.style.display = "none";

    if (m === "transfer") {
      bankField.style.display = "block";
      transferFields.style.display = "grid";
      if (paymentProof) paymentProof.required = true;
      bankSelect.required = true;

      // set default destination jika belum dipilih
      if (!bankSelect.value) setTransferDestination(null);
      else setTransferDestination(bankSelect.value);
    }

    if (m === "cod") {
      bankField.style.display = "none";
      transferFields.style.display = "none";
      codNote.style.display = "block";
      if (paymentProof) paymentProof.required = false;
      bankSelect.required = false;
      bankSelect.value = "";
      setTransferDestination(null);
    }

    if (m === "store") {
      bankField.style.display = "none";
      transferFields.style.display = "none";
      storeNote.style.display = "block";
      if (paymentProof) paymentProof.required = false;
      bankSelect.required = false;
      bankSelect.value = "";
      setTransferDestination(null);

      // Bayar di toko harus pickup
      if (deliveryMethod.value !== "pickup") {
        deliveryMethod.value = "pickup";
        applyDeliveryUI();
      }
    }
  }

  // file name
  if (paymentProof) {
    paymentProof.addEventListener("change", () => {
      if (!paymentProof.files || !paymentProof.files[0]) {
        fileName.textContent = "JPG/PNG • max 2MB";
        return;
      }
      fileName.textContent = paymentProof.files[0].name;
    });
  }

  // copy rekening
  if (copyBtn) {
    copyBtn.addEventListener("click", async () => {
      const text = destNumber.textContent.trim();
      if (!text || text === "—") return;

      try {
        await navigator.clipboard.writeText(text);
        copyBtn.textContent = "Tersalin ✅";
        setTimeout(() => (copyBtn.textContent = "Salin No. Rek"), 1100);
      } catch (e) {
        alert("Gagal menyalin. Coba copy manual ya.");
      }
    });
  }

  // listeners
  paymentMethod.addEventListener("change", applyPaymentUI);
  deliveryMethod.addEventListener("change", () => {
    // kalau user pilih courier tapi pembayaran store → paksa pickup
    if (paymentMethod.value === "store") {
      deliveryMethod.value = "pickup";
    }
    applyDeliveryUI();
  });

  bankSelect.addEventListener("change", () => setTransferDestination(bankSelect.value));

  // init
  applyDeliveryUI();
  applyPaymentUI();
});

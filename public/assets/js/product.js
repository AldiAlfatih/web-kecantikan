document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("searchInput");
  const clearBtn = document.getElementById("searchClear");

  const gridAll = document.getElementById("produkGridAll");
  const emptyState = document.getElementById("emptyState");

  // kalau elemen utama tidak ada, stop
  if (!input || !gridAll) return;

  const grids = [
    document.getElementById("produkGrid"),
    document.getElementById("produkGridAll"),
  ].filter(Boolean);

  function normalize(s) {
    return (s || "").toString().toLowerCase().trim();
  }

  function filterCards(q) {
    const query = normalize(q);

    // toggle tombol X
    if (clearBtn) clearBtn.style.display = query ? "inline-flex" : "none";

    let anyVisibleInAll = false;

    grids.forEach((grid) => {
      const cards = grid.querySelectorAll(".product-card");

      cards.forEach((card) => {
        const name = normalize(card.dataset.name);
        const brand = normalize(card.dataset.brand);
        const category = normalize(card.dataset.category);
        const skin = normalize(card.dataset.skin);

        // match ke semua keyword ecommerce umum
        const match =
          !query ||
          name.includes(query) ||
          brand.includes(query) ||
          category.includes(query) ||
          skin.includes(query);

        card.style.display = match ? "" : "none";

        if (grid.id === "produkGridAll" && match) anyVisibleInAll = true;
      });
    });

    // empty state hanya untuk gridAll
    if (emptyState) {
      emptyState.style.display = anyVisibleInAll ? "none" : "block";
    }
  }

  input.addEventListener("input", (e) => filterCards(e.target.value));

  if (clearBtn) {
    clearBtn.addEventListener("click", () => {
      input.value = "";
      input.focus();
      filterCards("");
    });
  }
});

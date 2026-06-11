<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk | Faceshop</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/produk.css">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>

<body>
@include('layout.navbar')

<section class="rekom-header">
    <h1>Koleksi Produk</h1>
    <p>Temukan produk kecantikan yang tepat untuk kulitmu</p>
</section>

<section class="produk-page">

    <form method="GET" action="{{ route('produk') }}" id="produkFilterForm">
        <div class="produk-toolbar">
            <div class="search-wrap">
                <span class="search-ico">🔍</span>

                <input
                    type="text"
                    id="searchInput"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Cari nama, brand, kategori, jenis kulit..."
                    autocomplete="off"
                />

                <button type="button" class="search-clear" id="searchClear" aria-label="Clear">✕</button>
            </div>

            <div class="toolbar-actions">
                <a href="{{ route('keranjang') }}" class="icon-btn" title="Keranjang">🛍️</a>

                <button type="button" class="filter-btn" id="btnOpenFilter" title="Filter">
                    ☰ <span>Filter</span>
                </button>
            </div>
        </div>

        <div class="filter-panel" id="filterPanel" style="display:none;">
            <div class="filter-grid">
                <div class="filter-item">
                    <label>Brand</label>
                    <select name="brand">
                        <option value="">Semua</option>
                        @foreach(($brands ?? collect()) as $b)
                            <option value="{{ $b }}" {{ ($brandSelected ?? '') === $b ? 'selected' : '' }}>
                                {{ $b }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Kategori</label>
                    <select name="category">
                        <option value="">Semua</option>
                        @foreach(($categories ?? collect()) as $c)
                            <option value="{{ $c }}" {{ ($catSelected ?? '') === $c ? 'selected' : '' }}>
                                {{ $c }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Jenis Kulit</label>
                    <select name="skin_type">
                        <option value="">Semua</option>
                        @foreach(($skinTypes ?? collect()) as $s)
                            <option value="{{ $s }}" {{ ($skinSelected ?? '') === $s ? 'selected' : '' }}>
                                {{ $skinTypeOptions[$s] ?? ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-item">
                    <label>Harga Min</label>
                    <input type="number" name="min" value="{{ $minSelected ?? '' }}" placeholder="0">
                </div>

                <div class="filter-item">
                    <label>Harga Max</label>
                    <input type="number" name="max" value="{{ $maxSelected ?? '' }}" placeholder="999999">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-primary-pill">Terapkan</button>
                <a href="{{ route('produk') }}" class="btn-outline-pill">Reset</a>
                <button type="button" class="btn-lite" id="btnCloseFilter">Tutup</button>
            </div>
        </div>
    </form>

    {{-- SECTION TOP 3 --}}
    <div class="produk-section-head">
        <div>
            @auth
                <span class="section-mini">Cocok dengan skin tone & undertone kamu</span>
                <h2>Rekomendasi Unggulan</h2>
            @else
                <span class="section-mini">Pilihan terbaik bulan ini</span>
                <h2>Produk Terpopuler</h2>
            @endauth
        </div>

        <a href="{{ route('rekomendasi') }}" class="lihat-semua">Lihat Semua →</a>
    </div>

    <div class="produk-grid produk-grid-top">
        @forelse(($recommended ?? collect())->take(3) as $index => $product)
            <div class="product-card product-card-top">
                <span class="top-rank">#{{ $index + 1 }}</span>

                <span class="badge {{ $product->badge_class ?? 'popular' }}">
                    {{ $product->badge_text ?? 'Recommended' }}
                </span>

                <a href="{{ route('produk.show', $product->id) }}" class="card-link">
                    <div class="product-image product-image-top">
                        <img
                            src="{{ $product->image ? Storage::url($product->image) : asset('assets/image/1.png') }}"
                            alt="{{ $product->name }}"
                        >
                    </div>

                    <div class="product-info">
                        <small class="brand">{{ $product->brand }}</small>
                        <h3>{{ $product->name }}</h3>
                        <p class="skin-type">
                            {{ $skinTypeOptions[$product->skin_type] ?? ucfirst($product->skin_type ?? 'normal') }}
                        </p>
                        <div class="price">RP {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>
                </a>
            </div>
        @empty
            <div class="produk-empty" style="grid-column:1/-1;">
                @auth
                    Belum ada produk rekomendasi yang cocok untuk profilmu.
                @else
                    Produk populer belum tersedia.
                @endauth
            </div>
        @endforelse
    </div>

    {{-- SECTION SEMUA PRODUK --}}
    <div class="produk-section-head" style="margin-top: 34px;">
        <div>
            <span class="section-mini">Semua koleksi</span>
            <h2>Produk Lainnya</h2>
        </div>
    </div>

    <div class="produk-grid">
        @forelse($products as $product)
            <div class="product-card">
                <span class="badge popular">Popular</span>

                <a href="{{ route('produk.show', $product->id) }}" class="card-link">
                    <div class="product-image">
                        <img
                            src="{{ $product->image ? Storage::url($product->image) : asset('assets/image/1.png') }}"
                            alt="{{ $product->name }}"
                        >
                    </div>

                    <div class="product-info">
                        <small class="brand">{{ $product->brand }}</small>
                        <h3>{{ $product->name }}</h3>
                        <p class="skin-type">
                            {{ $skinTypeOptions[$product->skin_type] ?? ucfirst($product->skin_type ?? 'normal') }}
                        </p>
                        <div class="price">RP {{ number_format($product->price, 0, ',', '.') }}</div>
                    </div>
                </a>
            </div>
        @empty
            <div class="produk-empty" style="grid-column:1/-1;">
                Produk belum tersedia / tidak ditemukan.
            </div>
        @endforelse
    </div>

    <div class="produk-pagination-wrap">
        {{ $products->links() }}
    </div>

</section>

@include('layout.footer')

<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("searchInput");
    const clearBtn = document.getElementById("searchClear");
    const form = document.getElementById("produkFilterForm");

    const btnOpen = document.getElementById("btnOpenFilter");
    const btnClose = document.getElementById("btnCloseFilter");
    const panel = document.getElementById("filterPanel");

    function toggleClear() {
        if (!clearBtn || !input) return;
        clearBtn.style.display = input.value.trim() ? "inline-flex" : "none";
    }

    toggleClear();

    clearBtn?.addEventListener("click", () => {
        input.value = "";
        toggleClear();
        form.submit();
    });

    input?.addEventListener("input", toggleClear);

    input?.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            form.submit();
        }
    });

    btnOpen?.addEventListener("click", () => {
        panel.style.display = (panel.style.display === "none" || !panel.style.display)
            ? "block"
            : "none";
    });

    btnClose?.addEventListener("click", () => {
        panel.style.display = "none";
    });
});
</script>

</body>
</html>
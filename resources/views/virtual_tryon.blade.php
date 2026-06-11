<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Virtual Try-On | Faceshop</title>

  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/tryon.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>

<body class="faceshop-body">

@include('layout.navbar')

<section class="tryon-page">

  <header class="tryon-head">
    <h1 class="tryon-title">Virtual <span>Try-On</span></h1>
    <p class="tryon-subtitle" id="subtitleText">
      {{ $product?->name ?? 'Demo Product' }} – {{ $activeShade?->shade_name ?? 'Light Beige' }}
    </p>

  </header>

  <div class="tryon-wrap">

    {{-- LEFT: CAMERA --}}
    <div class="tryon-card camera-card">

      <div class="card-top">
        <div class="mini-pill">
          <span class="dot"></span>
          Live Preview
        </div>

        <button class="icon-btn" type="button" id="btnFlip" title="Ganti Kamera">↺</button>
      </div>

      <div class="camera-stage">
        <div class="camera-frame">

          {{-- AREA LUAR OVAL (WARNA SHADE) --}}
          <div class="camera-area" id="cameraArea" style="--shade: {{ $activeShade->hex_color }};">

            {{-- OVAL CAMERA --}}
            <div class="camera-oval">
              <video id="cameraVideo" autoplay playsinline muted></video>

              <div class="camera-empty" id="cameraEmpty">
                <div class="empty-ico">📷</div>
                <div class="empty-text">
                  <b>Kamera belum aktif</b>
                  <small>Tekan tombol di bawah untuk mulai.</small>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>

      <div class="camera-actions">
        <button type="button" class="btn-primary" id="btnStartCamera">
          <span class="btn-ico">📸</span> Aktifkan Kamera
        </button>

        <button type="button" class="btn-secondary" id="btnStopCamera" disabled>
          Matikan
        </button>
      </div>

      <p class="camera-note">
        * Simulasi warna berbasis shade. Hasil bisa berbeda karena pencahayaan kamera.
      </p>
    </div>

    {{-- RIGHT: PRODUCT --}}
    <aside class="tryon-card product-card">

      <div class="product-badges">
        <span class="badge" id="shadeBadge">{{ $activeShade->tone ?? 'Shade' }}</span>
      </div>

      <div class="product-img">
        <img src="{{ $image }}" alt="{{ $product->name }}">
      </div>

      <div class="product-body">
        <small class="brand">{{ $product->brand }}</small>
        <h3 class="name">{{ $product->name }}</h3>

        {{-- SHADE ROW --}}
        <div class="shade-row">
          <div class="shade-swatch" id="shadeSwatch" style="background: {{ $activeShade->hex_color }};"></div>

          <div class="shade-info">
            <b id="shadeLabel">{{ $activeShade->shade_name }}</b>
            <small id="shadeHex">{{ $activeShade->hex_color }}</small>
          </div>
        </div>

        {{-- SHADE SWITCH LIST --}}
        <div class="shade-section">
          <p class="shade-title">Ganti Shade</p>

          <div class="shade-list" id="shadeList">
            @foreach($shades as $s)
              <button
                type="button"
                class="shade-item {{ $s->id === $activeShade->id ? 'active' : '' }}"
                data-id="{{ $s->id }}"
                data-hex="{{ $s->hex_color }}"
                data-name="{{ $s->shade_name }}"
                data-badge="{{ $s->tone ?? 'Shade' }}"
              >
                <span class="shade-dot" style="background: {{ $s->hex_color }}"></span>
                <span class="shade-name">{{ $s->shade_name }}</span>
              </button>
            @endforeach
          </div>

          <p class="shade-hint">
            * Kamu bisa coba semua shade produk ini tanpa kembali ke halaman detail.
          </p>
        </div>

        <div class="price">
          RP {{ number_format($product->price, 0, ',', '.') }}
        </div>

        {{-- ✅ ADD TO CART FORM (langsung dari try-on) --}}
        <form action="{{ route('keranjang.tambah') }}" method="POST" class="tryon-cart-form">
          @csrf
          <input type="hidden" name="product_id" value="{{ $product->id }}">
          <input type="hidden" name="shade_id" id="shadeIdInput" value="{{ $activeShade->id }}">

          <div class="qty-row">
            <span class="qty-label">Qty</span>
            <div class="qty-control">
              <button type="button" class="qty-btn" id="qtyMinus">−</button>
              <input type="text" name="qty" id="qtyInput" value="1" readonly>
              <button type="button" class="qty-btn" id="qtyPlus">+</button>
            </div>
          </div>

          <button type="submit" class="btn-cart-tryon">
            + Tambah ke Keranjang
          </button>

          <div class="tryon-quick-links">
            <a href="{{ route('keranjang') }}" class="btn-lite-link">Lihat Keranjang</a>
            <a href="{{ route('checkout.show') }}" class="btn-lite-link primary">Checkout</a>
          </div>
        </form>

      </div>

      <div class="tryon-bottom">
        <a href="{{ route('produk.show', $product->id) }}" class="btn-wide">
          Kembali ke detail produk
        </a>
      </div>

    </aside>

  </div>

</section>

@include('layout.footer')

<script>
  window.__TRYON_PRODUCT__ = { name: @json($product->name) };
  window.__TRYON_SHADE__ = {
    id: @json($activeShade->id),
    hex: @json($activeShade->hex_color),
    name: @json($activeShade->shade_name),
    badge: @json($activeShade->tone ?? 'Shade')
  };
</script>

<script src="{{ asset('assets/js/tryon.js') }}"></script>
</body>
</html>

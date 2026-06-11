<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} | Faceshop</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/detail-produk.css">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>
<body>

@include('layout.navbar')

@php
    use Illuminate\Support\Facades\Storage;

    $fallbackImg = asset('assets/image/1.png');
    $productImg = !empty($product->image) ? Storage::url($product->image) : $fallbackImg;
@endphp

<section class="detail-container">

    <div class="detail-image">
        <img
            src="{{ $productImg }}"
            alt="{{ $product->name }}"
            onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
        >
    </div>

    <div class="detail-info">
        <small class="brand">{{ $product->brand }}</small>
        <h1 class="product-name">{{ $product->name }}</h1>

        <h3 class="section-title">Deskripsi</h3>
        <p class="description">{{ $product->description }}</p>

        <form action="{{ route('keranjang.tambah') }}" method="POST" id="cartForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <select name="shade_id" id="shade_id" class="shade-select" required>
                <option value="">-- Pilih Shade --</option>

                @foreach($product->shades as $shade)
                    @php
                        $isRecommended = in_array($shade->id, $recommendedShadeIds ?? []);
                        $stock = (int) ($shade->stock ?? 0);
                        $isOut = $stock <= 0;
                    @endphp

                    <option
                        value="{{ $shade->id }}"
                        data-tryon-url="{{ route('tryon.show', ['product' => $product->id, 'shade' => $shade->id]) }}"
                        data-stock="{{ $stock }}"
                        {{ $isOut ? 'disabled' : '' }}
                        {{ $isRecommended ? 'selected' : '' }}
                    >
                        {{ $shade->shade_name }}
                        ({{ ucfirst($shade->tone) }} • {{ ucfirst($shade->undertone) }})
                        {{ $isRecommended ? ' ⭐ Cocok untukmu' : '' }}
                        {{ $isOut ? ' - HABIS' : '' }}
                    </option>
                @endforeach
            </select>

            <p id="stockInfo" style="margin:8px 0; font-size:14px; color:#666;">
                Pilih shade untuk melihat stok.
            </p>

            <div class="price-qty">
                <span class="price">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>

                <div class="qty-control">
                    <button type="button" id="btnMinus">−</button>
                    <input type="text" id="qty" name="qty" value="1" readonly>
                    <button type="button" id="btnPlus">+</button>
                </div>
            </div>

            <button type="submit" class="btn-cart" id="btnCart" disabled style="opacity:.6; cursor:not-allowed;">
                Tambah Ke Keranjang
            </button>
        </form>

        <a href="#" class="btn-tryon" id="btnTryOn" style="pointer-events:none; opacity:.6;">
            Virtual Try On
        </a>
    </div>
</section>

@include('layout.footer')

<script>
const shadeSelect = document.getElementById('shade_id');
const btnTryOn = document.getElementById('btnTryOn');
const btnCart  = document.getElementById('btnCart');
const stockInfo = document.getElementById('stockInfo');

const qtyInput = document.getElementById('qty');
const btnPlus  = document.getElementById('btnPlus');
const btnMinus = document.getElementById('btnMinus');

function setCartDisabled(disabled, message) {
  btnCart.disabled = disabled;
  btnCart.style.opacity = disabled ? '.6' : '1';
  btnCart.style.cursor  = disabled ? 'not-allowed' : 'pointer';
  if (message) stockInfo.textContent = message;
}

function setTryOnDisabled(disabled, url = '#') {
  btnTryOn.href = disabled ? '#' : url;
  btnTryOn.style.pointerEvents = disabled ? 'none' : 'auto';
  btnTryOn.style.opacity = disabled ? '.6' : '1';
}

function setQtyMaxByStock(stock) {
  if (stock <= 0) {
    qtyInput.value = 1;
    btnPlus.disabled = true;
    btnMinus.disabled = true;
    return;
  }

  btnPlus.disabled = false;
  btnMinus.disabled = false;

  if (parseInt(qtyInput.value) > stock) {
    qtyInput.value = stock;
  }
}

btnPlus.addEventListener('click', () => {
  const opt = shadeSelect.options[shadeSelect.selectedIndex];
  const stock = parseInt(opt?.getAttribute('data-stock') || '0');
  let q = parseInt(qtyInput.value);
  if (q < stock) qtyInput.value = q + 1;
});

btnMinus.addEventListener('click', () => {
  let q = parseInt(qtyInput.value);
  if (q > 1) qtyInput.value = q - 1;
});

shadeSelect.addEventListener('change', function () {
  const opt = shadeSelect.options[shadeSelect.selectedIndex];

  if (!opt || !opt.value) {
    stockInfo.textContent = 'Pilih shade untuk melihat stok.';
    setCartDisabled(true);
    setTryOnDisabled(true);
    setQtyMaxByStock(0);
    return;
  }

  const stock = parseInt(opt.getAttribute('data-stock') || '0');
  const url = opt.getAttribute('data-tryon-url') || '#';

  if (stock <= 0) {
    setCartDisabled(true, 'Stok shade ini HABIS.');
    setTryOnDisabled(true);
    setQtyMaxByStock(0);
    return;
  }

  setCartDisabled(false, 'Stok tersedia: ' + stock);
  setTryOnDisabled(false, url);
  setQtyMaxByStock(stock);
});

// default saat load
stockInfo.textContent = 'Pilih shade untuk melihat stok.';
setCartDisabled(true);
setTryOnDisabled(true);
setQtyMaxByStock(0);

// Auto trigger jika ada shade yang sudah terpilih (misal: recommended)
window.addEventListener('DOMContentLoaded', function () {
  if (shadeSelect && shadeSelect.value) {
    shadeSelect.dispatchEvent(new Event('change'));
  }
});
</script>

</body>
</html>
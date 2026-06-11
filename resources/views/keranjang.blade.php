<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Faceshop</title>

    {{-- ✅ FIX TYPO INI --}}
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/keranjang.css">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">


    {{-- ICON --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>

@include('layout.navbar')

<section class="cart-section">

    <h1 class="cart-title">Keranjang <span>Belanja</span></h1>

    <div class="cart-container">

        {{-- LIST PRODUK --}}
        <div class="cart-list">

            @forelse($items as $index => $item)
                @php
                    $shadeStock = (int)($item['shade_stock'] ?? ($item['shade']->stock ?? 0));
                    $isOut = $shadeStock <= 0;
                @endphp

                <div class="cart-item">

                    {{-- tombol hapus --}}
                    <form action="{{ route('keranjang.hapus') }}" method="POST" class="btn-delete">
                        @csrf
                        <input type="hidden" name="index" value="{{ $index }}">
                        <button type="submit" title="Hapus item">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>

                    <div class="cart-thumb">
                        <img
                            src="{{ !empty($item['product']->image) ? Storage::url($item['product']->image) : asset('assets/image/1.png') }}"
                            alt="{{ $item['product']->name }}"
                            onerror="this.onerror=null;this.src='{{ asset('assets/image/1.png') }}';"
                        >
                    </div>

                    <div class="cart-info">
                        <small class="cart-brand">{{ $item['product']->brand }}</small>
                        <h4 class="cart-name">{{ $item['product']->name }}</h4>

                        <div class="cart-meta">
                            <span class="meta-pill">
                                Jenis: {{ $item['product']->category ?? '-' }}
                            </span>
                            <span class="meta-pill">
                                Kulit: {{ $item['product']->skin_type ?? '-' }}
                            </span>
                        </div>

                        {{-- SHADE --}}
                        <div class="shade-row">
                            <span class="shade-label">Shade:</span>

                            @if(!empty($item['shade']))
                                <span class="shade-name">{{ $item['shade']->shade_name }}</span>

                                @if(!empty($item['shade']->hex_color))
                                    <span class="shade-dot" style="background: {{ $item['shade']->hex_color }};"></span>
                                @endif
                            @else
                                <span class="shade-name">{{ $item['shade_text'] ?? '-' }}</span>
                            @endif
                        </div>

                        {{-- ✅ INFO STOK HABIS --}}
                        @if($isOut)
                            <div style="margin-top:6px; color:#c00; font-weight:700;">
                                SHADE HABIS (tidak bisa checkout)
                            </div>
                        @else
                            <div style="margin-top:6px; color:#666; font-size:13px;">
                                Stok tersedia: {{ $shadeStock }}
                            </div>
                        @endif

                        {{-- QTY --}}
                        <div class="qty-control">
                            {{-- minus --}}
                            <form action="{{ route('keranjang.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="index" value="{{ $index }}">
                                <input type="hidden" name="qty" value="{{ max(1, $item['qty'] - 1) }}">
                                <button type="submit" class="qty-btn" title="Kurangi" {{ $isOut ? 'disabled' : '' }}>−</button>
                            </form>

                            <span class="qty">{{ $item['qty'] }}</span>

                            {{-- plus --}}
                            <form action="{{ route('keranjang.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="index" value="{{ $index }}">
                                <input type="hidden" name="qty" value="{{ $item['qty'] + 1 }}">
                                <button type="submit" class="qty-btn" title="Tambah" {{ $isOut ? 'disabled' : '' }}>+</button>
                            </form>
                        </div>

                        @if($isOut)
                            <div style="margin-top:6px; color:#c00; font-size:12px;">
                                Item ini harus dihapus sebelum checkout.
                            </div>
                        @endif
                    </div>

                    <div class="cart-price">
                        <div class="price-label">Subtotal</div>
                        <div class="price-total">
                            Rp {{ number_format($item['subtotal'],0,',','.') }}
                        </div>

                        <div class="price-unit">
                            Rp {{ number_format($item['product']->price,0,',','.') }} / item
                        </div>
                    </div>

                </div>
            @empty
                <div class="cart-empty">
                    <h3>Keranjang masih kosong</h3>
                    <p>Yuk mulai belanja produk favoritmu.</p>
                    <a href="{{ route('produk') }}" class="btn-primary">Lihat Produk</a>
                </div>
            @endforelse

        </div>

        {{-- RINGKASAN --}}
        @php
            $lineItems = count($items);
            $totalQty  = collect($items)->sum('qty');

            // ✅ kalau ada item yang shade_stock <= 0, disable checkout
            $hasOutOfStock = collect($items)->contains(fn($it) => (int)($it['shade_stock'] ?? 0) <= 0);
        @endphp

        <div class="cart-summary">
            <h3>Ringkasan Pesanan</h3>

            <div class="receipt">

                <div class="r-head">
                    <div>
                        <div class="r-store">FACESHOP</div>
                        <div class="r-date">{{ now()->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="r-badge">Cart</div>
                </div>

                <div class="r-sep"></div>

                <div class="r-row">
                    <span>Jumlah produk</span>
                    <b>{{ $lineItems }} item</b>
                </div>

                <div class="r-row">
                    <span>Total quantity</span>
                    <b>{{ $totalQty }} pcs</b>
                </div>

                <div class="r-sep"></div>

                {{-- daftar item kecil --}}
                <div class="r-items">
                    @foreach($items as $it)
                        <div class="r-item">
                            <div class="r-item-left">
                                <div class="r-item-name">{{ $it['product']->name }}</div>
                                <div class="r-item-sub">
                                    {{ $it['qty'] }} x Rp {{ number_format($it['product']->price,0,',','.') }}
                                </div>
                            </div>
                            <div class="r-item-right">
                                Rp {{ number_format($it['subtotal'],0,',','.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="r-sep strong"></div>

                <div class="r-total">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($total,0,',','.') }}</span>
                </div>

                <div class="r-note">
                    <small>Ongkos kirim akan dihitung di halaman checkout.</small>
                </div>
            </div>

            {{-- ✅ CHECKOUT DISABLED kalau ada stok habis --}}
            <a href="{{ route('checkout.show') }}"
               class="btn-checkout"
               style="{{ $hasOutOfStock ? 'pointer-events:none; opacity:.6; cursor:not-allowed;' : '' }}">
                Check Out
            </a>

            @if($hasOutOfStock)
                <div style="margin-top:10px; color:#c00; font-size:13px;">
                    Hapus item yang stoknya habis dulu sebelum checkout.
                </div>
            @endif

            <a href="{{ route('produk') }}" class="continue">Lanjut Belanja</a>
        </div>

    </div>
</section>

@include('layout.footer')

</body>
</html>
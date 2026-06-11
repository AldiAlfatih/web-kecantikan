<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya | Faceshop</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/orders.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>
<body class="faceshop-body">

@include('layout.navbar')

<section class="orders-page">
    <div class="orders-head">
        <h2 class="orders-title">Pesanan <span>Saya</span></h2>
        <p class="orders-subtitle">Pantau status pesananmu sampai selesai.</p>
    </div>

    <div class="orders-wrap">
        @forelse ($orders as $order)
            <a href="{{ route('orders.show', $order->id) }}" class="order-link">
                <div class="order-card">
                    <div class="order-header">
                        <div class="oh-left">
                            <h4 class="order-code">Pesanan #{{ $order->order_code }}</h4>
                            <div class="order-date">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>

                        {{-- ✅ STATUS BADGE (SESUAI ENUM DB) --}}
                        <span class="order-status status-{{ $order->status }}">
                            {{ ucwords(str_replace('_',' ', $order->status)) }}
                        </span>
                    </div>

                    <div class="order-divider"></div>

                    <div class="order-meta">
                        <div class="order-items">
                            {{ $order->items->count() }} item
                        </div>

                        <div class="order-total">
                            <span>Total</span>
                            <b>Rp {{ number_format($order->total_price, 0, ',', '.') }}</b>
                        </div>
                    </div>

                    <div class="order-cta">
                        <span>Lihat detail</span>
                        <span class="arrow">→</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="empty-orders">
                <h3>Belum ada pesanan</h3>
                <p>Yuk mulai belanja produk favoritmu dulu.</p>
                <a class="btn-primary" href="{{ route('produk') }}">Mulai Belanja</a>
            </div>
        @endforelse
    </div>
</section>

@include('layout.footer')

</body>
</html>
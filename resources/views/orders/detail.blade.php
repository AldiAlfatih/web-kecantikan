<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Pesanan | Faceshop</title>

  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/detailpesanan.css">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>

<body class="faceshop-body">
@include('layout.navbar')

<section class="order-page">
  <div class="order-head">
    <h1 class="order-title">Detail <span>Pesanan</span></h1>
    <p class="order-subtitle">Cek item yang kamu beli, waktu pemesanan, dan status pesanan.</p>
  </div>

  <div class="order-wrap">
    <div class="order-card">

      <div class="receipt">
        <div class="receipt-brand">
          <b>FACESHOP</b>
          <small>{{ $order->created_at->format('d M Y, H:i') }}</small>
        </div>

        <div class="receipt-line"></div>

        <div class="receipt-row">
          <span class="receipt-muted">No Pesanan</span>
          <b>#{{ $order->order_code }}</b>
        </div>

        <div class="receipt-row">
          <span class="receipt-muted">Tanggal</span>
          <span>{{ $order->created_at->format('d F Y, H:i') }}</span>
        </div>

        <div class="receipt-row">
          <span class="receipt-muted">Status</span>
          <span class="status-pill status-{{ $order->status }}">
            {{ ucwords(str_replace('_',' ', $order->status)) }}
          </span>
        </div>

        <div class="receipt-line"></div>

        @php
          $items = $order->items;
          $jumlahProduk = $items->count();
          $totalQty = $items->sum('qty');
          $subtotal = $items->sum('subtotal');
        @endphp

        <div class="receipt-row">
          <span class="receipt-muted">Jumlah produk</span>
          <b>{{ $jumlahProduk }}</b>
        </div>

        <div class="receipt-row">
          <span class="receipt-muted">Total quantity</span>
          <b>{{ $totalQty }}</b>
        </div>

        <div class="receipt-line"></div>

        <div class="receipt-items">
          @foreach($items as $it)
            <div class="receipt-item">
              <div class="r-left">
                <b>{{ $it->product->name }}</b>
                <small>
                  {{ $it->product->brand }}
                  @if($it->shade)
                    • Shade: {{ $it->shade->shade_name }}
                  @endif
                  • x{{ $it->qty }}
                </small>
                <small class="muted">Harga: Rp {{ number_format($it->price,0,',','.') }} / item</small>
              </div>

              <div class="r-right">
                Rp {{ number_format($it->subtotal,0,',','.') }}
              </div>
            </div>
          @endforeach
        </div>

        <div class="receipt-line"></div>

        <div class="receipt-row">
          <span class="receipt-muted">Subtotal</span>
          <b>Rp {{ number_format($subtotal,0,',','.') }}</b>
        </div>

        <div class="receipt-total">
          <span>TOTAL</span>
          <span>Rp {{ number_format($order->total_price,0,',','.') }}</span>
        </div>

        <p class="receipt-hint">
          * Total di atas belum termasuk ongkir kurir (jika diantar). Ongkir diinformasikan oleh kurir.
        </p>
      </div>

      {{-- INFO PEMBAYARAN --}}
      <div class="info-box">
        <h3>Informasi Pesanan</h3>
        <div class="info-row">
          <span>Status Pesanan</span>
          <b>{{ ucwords(str_replace('_',' ', $order->status)) }}</b>
        </div>
        <div class="info-row">
          <span>Dibuat</span>
          <b>{{ $order->created_at->format('d M Y, H:i') }}</b>
        </div>
        <div class="info-row">
          <span>Kode</span>
          <b>#{{ $order->order_code }}</b>
        </div>
      </div>

      <div class="info-box">
        <h3>Pembayaran</h3>
        <div class="info-row">
          <span>Metode</span>
          <b>{{ strtoupper($order->payment->method ?? '-') }}</b>
        </div>
        <div class="info-row">
          <span>Status Pembayaran</span>
          <b>{{ ucwords(str_replace('_',' ', $order->payment->status ?? '-')) }}</b>
        </div>
      </div>

      <div class="order-actions">
        <a class="btn-primary" href="{{ route('produk') }}">Lanjut Belanja</a>
        <a class="btn-secondary" href="{{ route('orders.index') }}">Kembali ke Pesanan</a>
      </div>

    </div>
  </div>
</section>

@include('layout.footer')
</body>
</html>

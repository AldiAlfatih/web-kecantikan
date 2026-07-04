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
  @if(session('success'))
      <div style="background: #dcfce7; border-left: 4px solid #16a34a; color: #15803d; padding: 12px 16px; border-radius: 10px; margin: 10px auto; max-width: 980px; font-size: 14px; font-weight: 700;">
          {{ session('success') }}
      </div>
  @endif

  @if(session('error'))
      <div style="background: #ffe8e8; border-left: 4px solid #b3261e; color: #b3261e; padding: 12px 16px; border-radius: 10px; margin: 10px auto; max-width: 980px; font-size: 14px; font-weight: 700;">
          {{ session('error') }}
      </div>
  @endif

  @if($errors->any())
      <div style="background: #ffe8e8; border-left: 4px solid #b3261e; color: #b3261e; padding: 12px 16px; border-radius: 10px; margin: 10px auto; max-width: 980px; font-size: 14px;">
          <ul style="margin: 0; padding-left: 16px;">
              @foreach($errors->all() as $err)
                  <li>{{ $err }}</li>
              @endforeach
          </ul>
      </div>
  @endif
  <div class="order-head">
    <h1 class="order-title">Detail <span>Pesanan</span></h1>
    <p class="order-subtitle">Cek item yang kamu beli, waktu pemesanan, dan status pesanan.</p>
  </div>

  <div class="order-wrap">
    @if($order->payment && $order->payment->method === 'transfer' && !$order->payment->payment_proof)
      <div style="background: #fff8eb; border-left: 4px solid #f59e0b; color: #b45309; padding: 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14.5px; line-height: 1.6; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
          <strong>Pesanan berhasil dibuat.</strong> Silakan lakukan pembayaran sesuai nominal yang tertera. Setelah melakukan transfer, buka menu Detail Pesanan dan unggah bukti pembayaran agar dapat diverifikasi oleh admin. Pesanan akan diproses setelah pembayaran berhasil diverifikasi.
      </div>
    @endif

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

      @if($order->payment && $order->payment->method === 'transfer')
        @php
          // Extract bank key from note
          $bankKey = null;
          if (preg_match('/Bank\/E-Wallet:\s*(\w+)/i', $order->payment->note, $matches)) {
              $bankKey = strtolower($matches[1]);
          }
          
          $rekMap = [
              'bca'       => ['name' => 'BCA',        'num' => '1234-567-890'],
              'bri'       => ['name' => 'BRI',        'num' => '0987-654-321'],
              'bni'       => ['name' => 'BNI',        'num' => '1122-3344-55'],
              'mandiri'   => ['name' => 'Mandiri',    'num' => '9988-776-655'],
              'bsi'       => ['name' => 'BSI',        'num' => '7766-554-433'],
              'cimb'      => ['name' => 'CIMB Niaga', 'num' => '6655-443-322'],
              'dana'      => ['name' => 'DANA',       'num' => '0812-xxxx-xxxx'],
              'gopay'     => ['name' => 'GoPay',      'num' => '0813-xxxx-xxxx'],
              'ovo'       => ['name' => 'OVO',        'num' => '0814-xxxx-xxxx'],
              'shopeepay' => ['name' => 'ShopeePay',  'num' => '0815-xxxx-xxxx'],
              'linkaja'   => ['name' => 'LinkAja',    'num' => '0816-xxxx-xxxx'],
              'qris'      => ['name' => 'QRIS',       'num' => 'FaceShop QRIS'],
          ];
          
          $bankDetails = $rekMap[$bankKey] ?? ['name' => strtoupper($bankKey ?? '-'), 'num' => '—'];
        @endphp

        <div class="info-box">
          <h3>Informasi Rekening Pembayaran</h3>
          <div class="info-row" style="align-items: center;">
            <div>
              <span style="display:block; font-size:12px; opacity:0.75; margin-bottom: 2px;">Transfer ke Rekening</span>
              <strong style="font-size:16px; color:#7d1030;">{{ $bankDetails['name'] }}</strong>
              <span style="display:block; font-size:12px; opacity:0.75; margin-top: 2px;">a/n FaceShop</span>
            </div>
            <div style="text-align: right;">
              <strong style="font-size: 18px; color: #d66a86;" id="rekNumber">{{ $bankDetails['num'] }}</strong>
              <button type="button" class="btn-copy-rek" onclick="copyRekDetail()" style="display:block; margin-left:auto; margin-top:6px; padding: 6px 12px; font-size: 12px; background: #7d1030; color:#fff; border:none; border-radius:6px; font-weight:700; cursor:pointer;">Salin No. Rek</button>
            </div>
          </div>
        </div>

        <div class="info-box">
          <h3>Bukti Pembayaran</h3>
          
          @if($order->payment->payment_proof)
            <div style="text-align: center; margin-bottom: 12px;">
              <p style="font-size: 13.5px; opacity: 0.85; margin-bottom: 8px;">Bukti pembayaran telah diunggah:</p>
              <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Bukti Pembayaran" style="max-width: 100%; max-height: 250px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            </div>
            
            @if($order->payment->status !== 'verified')
              <div style="text-align: center; margin-top: 10px;">
                <p style="font-size:12px; color:#b0a0a8; margin-bottom: 8px;">Ingin mengubah bukti pembayaran?</p>
                <button onclick="document.getElementById('upload-form-wrapper').style.display='block'; this.style.display='none';" style="padding: 6px 12px; font-size:12px; background: rgba(214,106,134,.18); color:#7d1030; border:none; border-radius:6px; font-weight:700; cursor:pointer;">Unggah Ulang Bukti</button>
              </div>
            @endif
          @else
            <div style="background:#eff6ff; border-left:4px solid #3b82f6; color:#1e3a8a; padding:12px 16px; border-radius:10px; margin-bottom:12px; font-size:13px; line-height:1.4;">
              Anda belum mengunggah bukti pembayaran. Silakan unggah bukti transfer di bawah ini agar pesanan Anda dapat diproses oleh admin.
            </div>
          @endif

          <div id="upload-form-wrapper" style="display: {{ $order->payment->payment_proof ? 'none' : 'block' }}; margin-top: 12px;">
            <form action="{{ route('orders.upload-proof', $order->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="upload-container" style="border: 2px dashed #d66a86; border-radius:12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('detailPaymentProof').click();">
                <input type="file" name="payment_proof" id="detailPaymentProof" accept="image/*" required style="display: none;">
                <div style="font-size: 2rem; margin-bottom: 6px;">📤</div>
                <div id="uploadText"><strong>Pilih File Bukti Pembayaran</strong><br><span style="font-size: 11px; opacity:0.75;">JPG / PNG • maks. 2MB</span></div>
                <img id="detailPreviewImg" src="" alt="Preview" style="max-width:100%; max-height:150px; border-radius:8px; margin-top:10px; display:none; margin-left:auto; margin-right:auto;">
              </div>
              <button type="submit" id="btnSubmitProof" style="display:none; width: 100%; background: #7d1030; color:#fff; border:none; border-radius:999px; padding: 12px; margin-top: 12px; font-weight: 800; cursor:pointer; box-shadow: 0 10px 18px rgba(125,16,48,.18);">Kirim Bukti Pembayaran</button>
            </form>
          </div>
        </div>

        <script>
          function copyRekDetail() {
              const rek = document.getElementById('rekNumber').textContent;
              if (rek && rek !== '—') {
                  navigator.clipboard.writeText(rek).then(() => {
                      alert('Nomor rekening berhasil disalin: ' + rek);
                  });
              }
          }

          document.getElementById('detailPaymentProof').addEventListener('change', function() {
              const img = document.getElementById('detailPreviewImg');
              const btn = document.getElementById('btnSubmitProof');
              const text = document.getElementById('uploadText');
              if (this.files[0]) {
                  img.src = URL.createObjectURL(this.files[0]);
                  img.style.display = 'block';
                  btn.style.display = 'block';
                  text.innerHTML = '<strong>' + this.files[0].name + '</strong><br><span style="font-size: 11px; opacity:0.75;">Klik kirim di bawah untuk mengunggah</span>';
              } else {
                  img.style.display = 'none';
                  btn.style.display = 'none';
                  text.innerHTML = '<strong>Pilih File Bukti Pembayaran</strong><br><span style="font-size: 11px; opacity:0.75;">JPG / PNG • maks. 2MB</span>';
              }
          });
        </script>
      @endif

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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-Out | Faceshop</title>

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/checkout.css">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>
<body class="faceshop-body">

@include('layout.navbar')

<section class="checkout-page">
    <div class="checkout-head">
        <h1 class="checkout-title">check-<span>out</span></h1>
        <p class="checkout-subtitle">Lengkapi data pesananmu. Pastikan semua field terisi.</p>
    </div>

    <div class="checkout-wrapper">

        {{-- FORM (KIRI) --}}
        <form class="checkout-card" action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
            @csrf

            @if ($errors->any())
                <div class="checkout-error">
                    <strong>Periksa lagi ya:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- =======================
               SECTION: DATA PENERIMA
            ======================= --}}
            <div class="section-box">
                <div class="section-title">
                    <h3>Data Penerima</h3>
                    <small>* wajib diisi</small>
                </div>

                <div class="field">
                    <label>Nama Lengkap <span class="req">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $user->name ?? '') }}" placeholder="Nama lengkap">
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="email" required value="{{ old('email', $user->email ?? '') }}" placeholder="Email">
                    </div>

                    <div class="field">
                        <label>No. Telp <span class="req">*</span></label>
                        <input type="text" name="phone" required value="{{ old('phone', $user->phone ?? '') }}" placeholder="Contoh: 08xxxxxxxxxx">
                    </div>
                </div>
            </div>

            {{-- =======================
               SECTION: PENGIRIMAN / PICKUP
            ======================= --}}
            <div class="section-box">
                <div class="section-title">
                    <h3>Pengiriman</h3>
                    <small>* wajib diisi</small>
                </div>

                <div class="field">
                    <label>Metode Pengambilan <span class="req">*</span></label>
                    <select name="delivery_method" id="deliveryMethod" required>
                        <option value="courier" {{ old('delivery_method','courier') === 'courier' ? 'selected' : '' }}>Diantar Kurir</option>
                        <option value="pickup"  {{ old('delivery_method') === 'pickup' ? 'selected' : '' }}>Ambil di Toko (Pick Up)</option>
                    </select>
                    <p class="hint" id="deliveryHint">
                        Ongkir tidak ditentukan toko. Jika diantar kurir, ongkir diinformasikan kurir. Estimasi dalam kota Rp 10.000 – Rp 20.000.
                    </p>
                </div>

                <div class="field" id="addressField">
                    <label>Alamat Lengkap <span class="req">*</span></label>
                    <textarea name="address" rows="4" required placeholder="Nama jalan, no rumah, kecamatan, kota, patokan"
                    >{{ old('address', $user->address ?? '') }}</textarea>
                </div>

                <div class="pickup-note" id="pickupNote" style="display:none;">
                    <div class="pickup-badge">PickUp</div>
                    <div>
                        <b>Ambil di toko</b>
                        <p class="hint" style="margin:6px 0 0;">
                            Kamu bisa ambil langsung di toko. Alamat toko & jam operasional bisa kamu tampilkan di halaman info toko (atau footer).
                        </p>
                    </div>
                </div>
            </div>

            {{-- =======================
               SECTION: PEMBAYARAN
            ======================= --}}
            <div class="section-box">
                <div class="section-title">
                    <h3>Pembayaran</h3>
                    <small>* wajib diisi</small>
                </div>

                <div class="grid-2">
                    <div class="field">
                        <label>Metode Pembayaran <span class="req">*</span></label>
                        <select name="method" id="paymentMethod" required>
                            <option value="transfer" {{ old('method','transfer') === 'transfer' ? 'selected' : '' }}>Transfer / E-Wallet</option>
                            <option value="cod" {{ old('method') === 'cod' ? 'selected' : '' }}>COD (Bayar di tempat)</option>
                            <option value="store" {{ old('method') === 'store' ? 'selected' : '' }}>Bayar di Toko</option>
                        </select>
                        <p class="hint" id="paymentHint">
                            Transfer: upload bukti. COD: bayar saat barang sampai. Bayar di toko: khusus Pick Up.
                        </p>
                    </div>

                    <div class="field" id="bankField">
                        <label>Pilih Bank / E-Wallet <span class="req" id="bankReq">*</span></label>
                        <select name="bank" id="bankSelect">
                            <option value="">-- Pilih --</option>
                            <optgroup label="Bank">
                                <option value="bca" {{ old('bank')==='bca' ? 'selected':'' }}>BCA</option>
                                <option value="bri" {{ old('bank')==='bri' ? 'selected':'' }}>BRI</option>
                                <option value="bni" {{ old('bank')==='bni' ? 'selected':'' }}>BNI</option>
                                <option value="mandiri" {{ old('bank')==='mandiri' ? 'selected':'' }}>Mandiri</option>
                            </optgroup>
                            <optgroup label="E-Wallet">
                                <option value="dana" {{ old('bank')==='dana' ? 'selected':'' }}>DANA</option>
                                <option value="gopay" {{ old('bank')==='gopay' ? 'selected':'' }}>GoPay</option>
                                <option value="ovo" {{ old('bank')==='ovo' ? 'selected':'' }}>OVO</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                {{-- Transfer section --}}
                <div class="transfer-grid" id="transferFields">
                    <div class="field">
                        <label>Masukkan Bukti Transfer <span class="req" id="proofReq">*</span></label>

                        <label class="upload-box" for="paymentProof">
                            <span class="upload-icon">⬆</span>
                            <span class="upload-text">
                                <b>Upload bukti transfer</b>
                                <small id="fileName">JPG/PNG • max 2MB</small>
                            </span>
                            <input type="file" name="payment_proof" id="paymentProof" accept="image/*">
                        </label>

                        <p class="hint" style="margin-top:10px;">
                            Pastikan nominal sesuai total. Setelah transfer, upload bukti.
                        </p>
                    </div>

                    <div class="transfer-dest">
                        <div class="dest-head">
                            <div>
                                <div class="dest-label">Transfer ke</div>
                                <div class="dest-name" id="destName">—</div>
                                <div class="dest-sub" id="destOwner">a/n FaceShop</div>
                            </div>
                            <div class="dest-number" id="destNumber">—</div>
                        </div>

                        <button type="button" class="btn-copy" id="copyBtn">
                            Salin No. Rek
                        </button>
                    </div>
                </div>

                {{-- COD note --}}
                <div class="info-note" id="codNote" style="display:none;">
                    <b>COD</b>
                    <p class="hint" style="margin:6px 0 0;">
                        Pembayaran dilakukan saat barang sampai. Tidak perlu upload bukti.
                    </p>
                </div>

                {{-- Store note --}}
                <div class="info-note" id="storeNote" style="display:none;">
                    <b>Bayar di Toko</b>
                    <p class="hint" style="margin:6px 0 0;">
                        Metode ini hanya untuk <b>Pick Up</b>. Silakan bayar langsung saat pengambilan.
                    </p>
                </div>
            </div>

            {{-- tombol submit ada di ringkasan kanan --}}
        </form>

        {{-- RINGKASAN (KANAN) --}}
        <aside class="summary-card">
            <h3>Ringkasan Pesanan</h3>

            <div class="receipt">
                <div class="receipt-brand">
                    <b>FACESHOP</b>
                    <small>{{ now()->format('d M Y, H:i') }}</small>
                </div>

                <div class="receipt-line"></div>

                <div class="receipt-row">
                    <span>Jumlah produk</span>
                    <span>{{ count($items) }}</span>
                </div>
                <div class="receipt-row">
                    <span>Total quantity</span>
                    <span>{{ collect($items)->sum('qty') }}</span>
                </div>

                <div class="receipt-line"></div>

                <div class="receipt-items">
                    @foreach($items as $it)
                        <div class="receipt-item">
                            <div class="r-left">
                                <b>{{ $it['product']->name }}</b>
                                <small>
                                    {{ $it['product']->brand }} • {{ $it['shade']->shade_name ?? '-' }} • x{{ $it['qty'] }}
                                </small>
                            </div>
                            <div class="r-right">
                                Rp {{ number_format($it['subtotal'],0,',','.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="receipt-line"></div>

                <div class="receipt-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($total,0,',','.') }}</span>
                </div>

                <div class="receipt-row receipt-muted" id="shippingRow">
                    <span>Ongkir (estimasi)</span>
                    <span id="shippingText">Rp 10.000 – Rp 20.000</span>
                </div>

                <div class="receipt-line"></div>

                <div class="receipt-total">
                    <span>TOTAL</span>
                    {{-- total final tetap subtotal karena ongkir by kurir --}}
                    <span>Rp {{ number_format($total,0,',','.') }}</span>
                </div>

                <p class="receipt-hint" id="totalHint">
                    * Total belum termasuk ongkir kurir (jika diantar). Ongkir diinformasikan oleh kurir.
                </p>
            </div>

            <button class="btn-primary" type="submit" form="checkoutForm">Check Out</button>
            <a href="{{ route('keranjang') }}" class="btn-secondary">Kembali ke Keranjang</a>
        </aside>

    </div>
</section>

@include('layout.footer')

<script src="/assets/js/checkout.js"></script>
</body>
</html>

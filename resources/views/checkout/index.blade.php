<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-Out | Faceshop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom-select.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* =====================
           PAGE WRAPPER
        ===================== */
        .co-page {
            min-height: calc(100vh - 120px);
            background: linear-gradient(160deg, #fdf0f3 0%, #f7eef5 50%, #f0f4fd 100%);
            padding: 2rem 1rem 5rem;
        }

        /* =====================
           STEPPER
        ===================== */
        .co-stepper {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            max-width: 520px;
            margin: 0 auto 2.5rem;
        }
        .co-step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
            position: relative;
        }
        .co-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 17px;
            left: calc(50% + 20px);
            width: calc(100% - 40px);
            height: 2px;
            background: #e5d8de;
            border-radius: 2px;
            transition: background 0.4s;
        }
        .co-step.done:not(:last-child)::after,
        .co-step.active:not(:last-child)::after { background: linear-gradient(90deg,#e65b7a,#d4829a); }
        .co-step-num {
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem;
            border: 2.5px solid #e5d8de;
            background: #fff;
            color: #b0a0a8;
            z-index: 1;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .co-step.active .co-step-num {
            border-color: #e65b7a;
            background: linear-gradient(135deg,#e65b7a,#c94469);
            color: #fff;
            box-shadow: 0 4px 14px rgba(230,91,122,0.35);
        }
        .co-step.done .co-step-num {
            border-color: #e65b7a;
            background: #fce8ed;
            color: #e65b7a;
        }
        .co-step-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #b0a0a8;
            white-space: nowrap;
        }
        .co-step.active .co-step-label,
        .co-step.done .co-step-label { color: #e65b7a; }

        /* =====================
           LAYOUT
        ===================== */
        .co-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
            max-width: 1020px;
            margin: 0 auto;
            align-items: start;
        }
        @media (max-width: 800px) { .co-layout { grid-template-columns: 1fr; } }

        /* =====================
           CARD
        ===================== */
        .co-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.6rem;
            box-shadow: 0 4px 24px rgba(180,60,100,0.08), 0 1px 4px rgba(0,0,0,0.04);
            margin-bottom: 1rem;
        }
        .co-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2d1f26;
            margin: 0 0 1.3rem;
            padding-bottom: 0.65rem;
            border-bottom: 2px solid #fce8ed;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .co-card-icon { font-size: 1.15rem; }

        /* =====================
           FIELDS
        ===================== */
        .co-field { margin-bottom: 1rem; }
        .co-field:last-child { margin-bottom: 0; }
        .co-field label {
            display: block;
            font-size: 0.81rem;
            font-weight: 600;
            color: #5a4250;
            margin-bottom: 0.38rem;
        }
        .co-field input,
        .co-field textarea {
            width: 100%;
            padding: 0.62rem 0.9rem;
            border: 1.8px solid #e8dce0;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: inherit;
            background: #fff;
            color: #2d1f26;
            transition: border-color 0.22s, box-shadow 0.22s;
        }
        .co-field input:focus,
        .co-field textarea:focus {
            outline: none;
            border-color: #e65b7a;
            box-shadow: 0 0 0 3px rgba(230,91,122,0.13);
        }
        .co-field textarea { resize: vertical; }
        .req { color: #e65b7a; margin-left: 2px; }
        .co-hint { font-size: 0.76rem; color: #b0a0a8; margin-top: 0.25rem; }

        /* grids */
        .co-g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; }
        .co-g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.8rem; }
        @media (max-width: 540px) {
            .co-g2, .co-g3 { grid-template-columns: 1fr; }
        }

        /* =====================
           STEP PANELS
        ===================== */
        .co-panel { display: none; }
        .co-panel.active { display: block; }

        /* =====================
           DELIVERY OPTIONS
        ===================== */
        .delivery-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1rem; }
        .dlv-label { cursor: pointer; }
        .dlv-label input { display: none; }
        .dlv-box {
            border: 2px solid #e8dce0;
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
        }
        .dlv-box .dlv-icon { font-size: 1.7rem; }
        .dlv-box .dlv-name { font-weight: 700; font-size: 0.9rem; color: #2d1f26; margin-top: 0.25rem; }
        .dlv-box .dlv-sub { font-size: 0.75rem; color: #b0a0a8; margin-top: 0.15rem; }
        .dlv-label input:checked + .dlv-box {
            border-color: #e65b7a;
            background: linear-gradient(135deg,#fff0f3,#fff8f9);
            box-shadow: 0 4px 12px rgba(230,91,122,0.15);
        }
        .dlv-label input:checked + .dlv-box .dlv-name { color: #e65b7a; }

        /* =====================
           ADDRESS LOADING STATE
        ===================== */
        .addr-loading {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 0.8rem; color: #b0a0a8; padding: 0.4rem 0;
        }
        .addr-loading-spin {
            width: 14px; height: 14px;
            border: 2px solid #e8c8d2; border-top-color: #e65b7a;
            border-radius: 50%; animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* =====================
           PAYMENT OPTIONS
        ===================== */
        .pay-opts { display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1rem; }
        .pay-label { cursor: pointer; }
        .pay-label input { display: none; }
        .pay-box {
            border: 1.8px solid #e8dce0;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            display: flex; align-items: center; gap: 0.85rem;
            transition: all 0.2s;
        }
        .pay-icon { font-size: 1.4rem; }
        .pay-name { font-weight: 700; font-size: 0.9rem; color: #2d1f26; }
        .pay-sub { font-size: 0.75rem; color: #b0a0a8; }
        .pay-label input:checked + .pay-box {
            border-color: #e65b7a;
            background: linear-gradient(135deg,#fff0f3,#fff8f9);
            box-shadow: 0 2px 10px rgba(230,91,122,0.12);
        }
        .pay-label input:checked + .pay-box .pay-name { color: #e65b7a; }

        /* Bank grid */
        .bank-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 0.5rem; }
        @media (max-width:480px) { .bank-grid { grid-template-columns: repeat(3,1fr); } }
        .bank-label { cursor: pointer; }
        .bank-label input { display: none; }
        .bank-box {
            border: 1.8px solid #e8dce0;
            border-radius: 9px;
            padding: 0.55rem 0.3rem;
            text-align: center;
            font-size: 0.78rem;
            font-weight: 600;
            color: #5a4250;
            transition: all 0.18s;
        }
        .bank-label input:checked + .bank-box {
            border-color: #e65b7a;
            background: #fce8ed;
            color: #e65b7a;
        }

        /* Rek info */
        .rek-box {
            background: linear-gradient(135deg,#fce8ed,#fff5f7);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            display: flex; justify-content: space-between; align-items: center;
            margin: 0.8rem 0;
            border: 1.5px solid #f3c6d0;
        }
        .rek-label { font-size: 0.75rem; color: #b0a0a8; }
        .rek-name { font-weight: 700; font-size: 1rem; color: #2d1f26; }
        .rek-owner { font-size: 0.78rem; color: #b0a0a8; }
        .rek-num { font-size: 1.1rem; font-weight: 800; color: #e65b7a; }
        .btn-copy {
            background: #e65b7a; color: #fff; border: none;
            border-radius: 7px; padding: 0.35rem 0.85rem;
            font-size: 0.78rem; font-weight: 600; cursor: pointer; margin-top: 0.4rem;
            transition: opacity 0.2s;
        }
        .btn-copy:hover { opacity: 0.85; }

        /* Upload */
        .upload-box {
            border: 2px dashed #e8c8d2; border-radius: 12px;
            padding: 1.3rem; text-align: center; cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
        }
        .upload-box:hover { border-color: #e65b7a; background: #fff8f9; }
        .upload-box input { display: none; }
        #previewImg { max-width:100%; max-height:140px; border-radius:8px; margin-top:0.7rem; display:none; }

        /* Info notes */
        .info-note {
            border-radius: 10px; padding: 0.85rem 1rem;
            font-size: 0.85rem; display: none;
        }
        .info-note-green { background:#f0fdf4; border-left:4px solid #34d399; color:#065f46; }
        .info-note-blue  { background:#eff6ff; border-left:4px solid #60a5fa; color:#1e40af; }

        /* Pickup note */
        .pickup-note {
            background: linear-gradient(135deg,#f0fdf4,#f7fef8);
            border: 1.5px solid #86efac;
            border-radius: 12px; padding: 0.9rem 1rem;
            font-size: 0.85rem; color: #065f46; display: none;
        }

        /* =====================
           SUB TITLES
        ===================== */
        .co-sub {
            font-size: 0.78rem; font-weight: 700; color: #e65b7a;
            text-transform: uppercase; letter-spacing: 0.07em;
            margin: 1.1rem 0 0.55rem;
        }
        .co-sub:first-child { margin-top: 0; }

        /* =====================
           REVIEW (Step 3)
        ===================== */
        .rv-group { margin-bottom: 1rem; }
        .rv-group-title {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.07em; color: #e65b7a; margin-bottom: 0.45rem;
        }
        .rv-row { display:flex; gap:0.5rem; font-size:0.875rem; margin-bottom:0.2rem; }
        .rv-lbl { color:#b0a0a8; min-width:130px; flex-shrink:0; }
        .rv-val { color:#2d1f26; font-weight:600; }

        /* =====================
           NAV BUTTONS
        ===================== */
        .co-nav { display:flex; gap:0.8rem; margin-top:1.5rem; }
        .btn-next {
            flex:1; background: linear-gradient(135deg,#e65b7a,#c94469);
            color:#fff; border:none; border-radius:12px;
            padding:0.8rem 1.5rem; font-size:0.95rem; font-weight:700;
            font-family:inherit; cursor:pointer;
            box-shadow: 0 4px 16px rgba(230,91,122,0.3);
            transition: opacity 0.2s, transform 0.1s;
        }
        .btn-next:hover { opacity:0.92; transform:translateY(-1px); }
        .btn-back {
            background:#f3eff1; color:#5a4250; border:none;
            border-radius:12px; padding:0.8rem 1rem;
            font-size:0.88rem; font-weight:600; font-family:inherit;
            cursor:pointer; transition:background 0.2s;
            white-space: nowrap;
        }
        .btn-back:hover { background:#ecdde2; }
        .btn-submit {
            flex:1; background: linear-gradient(135deg,#e65b7a,#c94469);
            color:#fff; border:none; border-radius:12px;
            padding:0.85rem 1.5rem; font-size:1rem; font-weight:700;
            font-family:inherit; cursor:pointer;
            box-shadow: 0 4px 16px rgba(230,91,122,0.3);
            transition: opacity 0.2s;
        }
        .btn-submit:hover { opacity:0.9; }

        /* =====================
           SUMMARY CARD
        ===================== */
        .summary-card {
            background:#fff;
            border-radius:16px;
            padding:1.5rem;
            box-shadow: 0 4px 24px rgba(180,60,100,0.08), 0 1px 4px rgba(0,0,0,0.04);
            position:sticky; top:80px;
        }
        .summary-title {
            font-size:1rem; font-weight:700; color:#2d1f26;
            margin:0 0 1rem;
            padding-bottom:0.6rem;
            border-bottom:2px solid #fce8ed;
        }
        .s-item { display:flex; gap:0.75rem; margin-bottom:0.9rem; align-items:flex-start; }
        .s-img {
            width:52px; height:52px; object-fit:cover;
            border-radius:10px; background:#f5eff2; flex-shrink:0;
            display:flex; align-items:center; justify-content:center; font-size:1.4rem;
        }
        .s-info { flex:1; min-width:0; }
        .s-name { font-size:0.85rem; font-weight:700; color:#2d1f26; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .s-sub  { font-size:0.75rem; color:#b0a0a8; margin-top:0.1rem; }
        .s-price{ font-size:0.85rem; font-weight:700; color:#e65b7a; white-space:nowrap; }
        .s-divider { border:none; border-top:1px solid #f0e8eb; margin:0.85rem 0; }
        .s-row { display:flex; justify-content:space-between; font-size:0.84rem; color:#5a4250; margin-bottom:0.4rem; }
        .s-total { display:flex; justify-content:space-between; font-size:1rem; font-weight:800; color:#2d1f26; margin-top:0.4rem; }
        .s-total span:last-child { color:#e65b7a; }

        /* error */
        .co-error {
            background:#fce8ed; border-left:4px solid #e65b7a;
            border-radius:10px; padding:0.8rem 1rem;
            margin-bottom:1rem; font-size:0.85rem; color:#c94469;
        }
        .co-error ul { margin:0.3rem 0 0 1rem; padding:0; }
    </style>
</head>
<body class="faceshop-body">
@include('layout.navbar')

@php
  $banks = [
    ['id'=>'bca',       'name'=>'BCA'],
    ['id'=>'bri',       'name'=>'BRI'],
    ['id'=>'bni',       'name'=>'BNI'],
    ['id'=>'mandiri',   'name'=>'Mandiri'],
    ['id'=>'bsi',       'name'=>'BSI'],
    ['id'=>'cimb',      'name'=>'CIMB'],
    ['id'=>'dana',      'name'=>'DANA'],
    ['id'=>'gopay',     'name'=>'GoPay'],
    ['id'=>'ovo',       'name'=>'OVO'],
    ['id'=>'shopeepay', 'name'=>'ShopeePay'],
    ['id'=>'linkaja',   'name'=>'LinkAja'],
    ['id'=>'qris',      'name'=>'QRIS'],
  ];
@endphp

<div class="co-page">

    {{-- STEPPER --}}
    <div class="co-stepper">
        <div class="co-step active" id="dot1">
            <div class="co-step-num">1</div>
            <div class="co-step-label">Alamat</div>
        </div>
        <div class="co-step" id="dot2">
            <div class="co-step-num">2</div>
            <div class="co-step-label">Pembayaran</div>
        </div>
        <div class="co-step" id="dot3">
            <div class="co-step-num">3</div>
            <div class="co-step-label">Konfirmasi</div>
        </div>
    </div>

    <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @if ($errors->any())
            <div style="max-width:1020px;margin:0 auto 1rem;">
                <div class="co-error">
                    <strong>Periksa lagi ya:</strong>
                    <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            </div>
        @endif

        <div class="co-layout">

            {{-- ====== LEFT: PANELS ====== --}}
            <div>

                {{-- ░░ STEP 1 — ALAMAT ░░ --}}
                <div class="co-panel active" id="panel1">

                    <div class="co-card">
                        <div class="co-card-title"><span class="co-card-icon">👤</span>Data Penerima</div>
                        <div class="co-g2">
                            <div class="co-field">
                                <label>Nama Lengkap<span class="req">*</span></label>
                                <input type="text" name="name" id="f_name" required
                                    value="{{ old('name', $user->name ?? '') }}" placeholder="Nama lengkap">
                            </div>
                            <div class="co-field">
                                <label>No. Telepon<span class="req">*</span></label>
                                <input type="text" name="phone" id="f_phone" required
                                    value="{{ old('phone', $user->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="co-field">
                            <label>Email<span class="req">*</span></label>
                            <input type="email" name="email" id="f_email" required
                                value="{{ old('email', $user->email ?? '') }}" placeholder="Email aktif">
                        </div>
                    </div>

                    <div class="co-card">
                        <div class="co-card-title"><span class="co-card-icon">🚚</span>Metode Pengiriman</div>
                        <div class="delivery-grid">
                            <label class="dlv-label">
                                <input type="radio" name="delivery_method" value="courier"
                                    id="dlvCourier" {{ old('delivery_method','courier')==='courier' ? 'checked':'' }}>
                                <div class="dlv-box">
                                    <div class="dlv-icon">🚚</div>
                                    <div class="dlv-name">Diantar Kurir</div>
                                    <div class="dlv-sub">~Rp 10.000–20.000</div>
                                </div>
                            </label>
                            <label class="dlv-label">
                                <input type="radio" name="delivery_method" value="pickup"
                                    id="dlvPickup" {{ old('delivery_method')==='pickup' ? 'checked':'' }}>
                                <div class="dlv-box">
                                    <div class="dlv-icon">🏪</div>
                                    <div class="dlv-name">Ambil di Toko</div>
                                    <div class="dlv-sub">Gratis ongkir</div>
                                </div>
                            </label>
                        </div>

                        {{-- ADDRESS BLOCK --}}
                        <div id="addrBlock">
                            <div class="addr-section-title" style="margin-top:0.5rem;">📍 Alamat Pengiriman</div>

                            <div class="addr-grid-2">
                                <div class="co-field">
                                    <label class="addr-label">Provinsi<span class="req">*</span></label>
                                    <select name="province_name" id="f_province" class="custom-sel" disabled>
                                        <option value="">-- Pilih Provinsi --</option>
                                    </select>
                                    <input type="hidden" name="province" id="h_province">
                                </div>
                                <div class="co-field">
                                    <label class="addr-label">Kota / Kabupaten<span class="req">*</span></label>
                                    <select name="city_name" id="f_city" class="custom-sel" disabled>
                                        <option value="">-- Pilih Provinsi dulu --</option>
                                    </select>
                                    <input type="hidden" name="city" id="h_city">
                                </div>
                            </div>

                            <div class="addr-grid-3">
                                <div class="co-field">
                                    <label class="addr-label">Kecamatan<span class="req">*</span></label>
                                    <select name="district_name" id="f_district" class="custom-sel" disabled>
                                        <option value="">-- Pilih Kota dulu --</option>
                                    </select>
                                    <input type="hidden" name="district" id="h_district">
                                </div>
                                <div class="co-field">
                                    <label class="addr-label">Kelurahan / Desa<span class="req">*</span></label>
                                    <select name="village_name" id="f_village" class="custom-sel" disabled>
                                        <option value="">-- Pilih Kecamatan dulu --</option>
                                    </select>
                                    <input type="hidden" name="village" id="h_village">
                                </div>
                                <div class="co-field">
                                    <label class="addr-label">Kode Pos<span class="req">*</span></label>
                                    <input type="text" name="postal_code" id="f_postal"
                                        value="{{ old('postal_code') }}"
                                        placeholder="Contoh: 90111" maxlength="10">
                                </div>
                            </div>

                            <div class="co-field">
                                <label class="addr-label">Detail Alamat (No. Rumah / Blok / Patokan)<span class="req">*</span></label>
                                <textarea name="address" id="f_address" rows="3"
                                    placeholder="Contoh: Jl. Veteran No. 10, RT 02/RW 04, depan Indomaret"
                                >{{ old('address', $user->address ?? '') }}</textarea>
                                <p class="co-hint">Isi nama jalan, nomor rumah/blok, dan patokan terdekat.</p>
                            </div>
                        </div>

                        <div class="pickup-note" id="pickupNote">
                            🏪 <strong>Ambil di Toko</strong> — Tidak perlu mengisi alamat. Tim kami akan menghubungi kamu saat pesanan siap.
                        </div>
                    </div>

                    <div class="co-nav">
                        <a href="{{ route('keranjang') }}" class="btn-back">← Keranjang</a>
                        <button type="button" class="btn-next" onclick="goStep(2)">Lanjut ke Pembayaran →</button>
                    </div>
                </div>

                {{-- ░░ STEP 2 — PEMBAYARAN ░░ --}}
                <div class="co-panel" id="panel2">
                    <div class="co-card">
                        <div class="co-card-title"><span class="co-card-icon">💳</span>Metode Pembayaran</div>

                        <div class="pay-opts">
                            <label class="pay-label">
                                <input type="radio" name="method" value="transfer" id="payTransfer"
                                    {{ old('method','transfer')==='transfer'?'checked':'' }}>
                                <div class="pay-box">
                                    <span class="pay-icon">🏦</span>
                                    <div>
                                        <div class="pay-name">Transfer / E-Wallet</div>
                                        <div class="pay-sub">Upload bukti setelah transfer</div>
                                    </div>
                                </div>
                            </label>
                            <label class="pay-label">
                                <input type="radio" name="method" value="cod" id="payCOD"
                                    {{ old('method')==='cod'?'checked':'' }}>
                                <div class="pay-box">
                                    <span class="pay-icon">💵</span>
                                    <div>
                                        <div class="pay-name">COD (Bayar di Tempat)</div>
                                        <div class="pay-sub">Bayar tunai saat barang tiba</div>
                                    </div>
                                </div>
                            </label>
                            <label class="pay-label">
                                <input type="radio" name="method" value="store" id="payStore"
                                    {{ old('method')==='store'?'checked':'' }}>
                                <div class="pay-box">
                                    <span class="pay-icon">🏪</span>
                                    <div>
                                        <div class="pay-name">Bayar di Toko</div>
                                        <div class="pay-sub">Khusus metode Pick Up</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- TRANSFER --}}
                        <div id="sec_transfer">
                            <div class="co-sub">Pilih Bank / E-Wallet<span class="req">*</span></div>
                            <div class="bank-grid">
                                @foreach($banks as $b)
                                <label class="bank-label">
                                    <input type="radio" name="bank" value="{{ $b['id'] }}"
                                        {{ old('bank')===$b['id']?'checked':'' }}>
                                    <div class="bank-box">{{ $b['name'] }}</div>
                                </label>
                                @endforeach
                            </div>

                            <div class="rek-box" style="margin-top:1rem;">
                                <div>
                                    <div class="rek-label">Transfer ke</div>
                                    <div class="rek-name" id="destName">—</div>
                                    <div class="rek-owner">a/n FaceShop</div>
                                </div>
                                <div style="text-align:right">
                                    <div class="rek-num" id="destNum">—</div>
                                    <button type="button" class="btn-copy" onclick="copyRek()">Salin No. Rek</button>
                                </div>
                            </div>

                            <div class="co-sub">Upload Bukti Transfer<span class="req">*</span></div>
                            <label class="upload-box" for="paymentProof">
                                <input type="file" name="payment_proof" id="paymentProof" accept="image/*">
                                <div style="font-size:1.8rem">📤</div>
                                <div class="upload-text"><strong>Klik untuk upload</strong><br>JPG / PNG • maks. 2MB</div>
                                <img id="previewImg" src="" alt="Preview">
                            </label>
                        </div>

                        <div class="info-note info-note-blue" id="sec_cod">
                            💵 <strong>COD</strong> — Bayar secara tunai saat barang tiba. Tidak perlu upload bukti.
                        </div>
                        <div class="info-note info-note-green" id="sec_store">
                            🏪 <strong>Bayar di Toko</strong> — Hanya untuk Pick Up. Bayar langsung saat ambil pesanan.
                        </div>
                    </div>

                    <div class="co-nav">
                        <button type="button" class="btn-back" onclick="goStep(1)">← Kembali</button>
                        <button type="button" class="btn-next" onclick="goStep(3)">Lihat Ringkasan →</button>
                    </div>
                </div>

                {{-- ░░ STEP 3 — KONFIRMASI ░░ --}}
                <div class="co-panel" id="panel3">
                    <div class="co-card">
                        <div class="co-card-title"><span class="co-card-icon">✅</span>Konfirmasi Pesanan</div>

                        <div class="rv-group">
                            <div class="rv-group-title">Data Penerima</div>
                            <div class="rv-row"><span class="rv-lbl">Nama</span><span class="rv-val" id="rv_name">—</span></div>
                            <div class="rv-row"><span class="rv-lbl">Telepon</span><span class="rv-val" id="rv_phone">—</span></div>
                            <div class="rv-row"><span class="rv-lbl">Email</span><span class="rv-val" id="rv_email">—</span></div>
                        </div>

                        <hr class="s-divider">

                        <div class="rv-group">
                            <div class="rv-group-title">Pengiriman</div>
                            <div class="rv-row"><span class="rv-lbl">Metode</span><span class="rv-val" id="rv_dlv">—</span></div>
                            <div class="rv-row" id="rv_addr_row">
                                <span class="rv-lbl">Alamat</span><span class="rv-val" id="rv_addr" style="word-break:break-word;">—</span>
                            </div>
                        </div>

                        <hr class="s-divider">

                        <div class="rv-group">
                            <div class="rv-group-title">Pembayaran</div>
                            <div class="rv-row"><span class="rv-lbl">Metode</span><span class="rv-val" id="rv_pay">—</span></div>
                            <div class="rv-row" id="rv_bank_row"><span class="rv-lbl">Bank/E-Wallet</span><span class="rv-val" id="rv_bank">—</span></div>
                            <div class="rv-row" id="rv_proof_row"><span class="rv-lbl">Bukti Transfer</span><span class="rv-val" id="rv_proof">—</span></div>
                        </div>
                    </div>

                    <div class="co-nav">
                        <button type="button" class="btn-back" onclick="goStep(2)">← Kembali</button>
                        <button type="submit" class="btn-submit">🛒 Pesan Sekarang</button>
                    </div>
                </div>

            </div>{{-- end left --}}

            {{-- ====== RIGHT: SUMMARY ====== --}}
            <aside class="summary-card">
                <div class="summary-title">🛍 Ringkasan Pesanan</div>

                @foreach($items as $it)
                <div class="s-item">
                    @if($it['product']->image)
                        <img class="s-img" src="{{ asset('storage/'.$it['product']->image) }}" alt="{{ $it['product']->name }}">
                    @else
                        <div class="s-img">🛒</div>
                    @endif
                    <div class="s-info">
                        <div class="s-name">{{ $it['product']->name }}</div>
                        <div class="s-sub">{{ $it['product']->brand }} • {{ $it['shade']->shade_name ?? '-' }} • x{{ $it['qty'] }}</div>
                    </div>
                    <div class="s-price">Rp {{ number_format($it['subtotal'],0,',','.') }}</div>
                </div>
                @endforeach

                <hr class="s-divider">

                <div class="s-row"><span>Subtotal ({{ collect($items)->sum('qty') }} item)</span><span>Rp {{ number_format($total,0,',','.') }}</span></div>
                <div class="s-row" id="ongkirRow">
                    <span>Ongkir (estimasi)</span>
                    <span id="ongkirVal">Rp 10.000–20.000</span>
                </div>

                <hr class="s-divider">

                <div class="s-total">
                    <span>Total</span>
                    <span>Rp {{ number_format($total,0,',','.') }}</span>
                </div>
                <p style="font-size:0.73rem;color:#b0a0a8;margin-top:0.5rem;">* Total belum termasuk ongkir kurir</p>
            </aside>

        </div>
    </form>
</div>

@include('layout.footer')

<script src="{{ asset('assets/js/wilayah.js') }}"></script>
<script>
/* =====================================================
   CHECKOUT MULTI-STEP + CHAINED ADDRESS
===================================================== */

// ── WILAYAH INIT ──
WilayahDropdown.init({
    provinceEl: document.getElementById('f_province'),
    cityEl:     document.getElementById('f_city'),
    districtEl: document.getElementById('f_district'),
    villageEl:  document.getElementById('f_village'),
});

// Sync hidden inputs (store name, not id)
['province','city','district','village'].forEach(key => {
    const sel = document.getElementById('f_' + key);
    const hid = document.getElementById('h_' + key);
    if (sel && hid) {
        sel.addEventListener('change', () => {
            const opt = sel.options[sel.selectedIndex];
            hid.value = opt ? opt.textContent.trim() : '';
        });
    }
});

// ── STEPPER ──
let step = 1;
function goStep(n) {
    if (n > step) {
        if (n === 2 && !validateStep1()) return;
        if (n === 3 && !validateStep2()) return;
        if (n === 3) buildReview();
    }

    document.querySelectorAll('.co-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel' + n).classList.add('active');

    document.querySelectorAll('.co-step').forEach((d, i) => {
        d.classList.remove('active','done');
        if (i + 1 < n) d.classList.add('done');
        if (i + 1 === n) d.classList.add('active');
    });

    step = n;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── VALIDATE STEP 1 ──
function validateStep1() {
    const name  = document.getElementById('f_name').value.trim();
    const phone = document.getElementById('f_phone').value.trim();
    const email = document.getElementById('f_email').value.trim();
    if (!name || !phone || !email) {
        alert('Mohon lengkapi data penerima (Nama, No. Telepon, Email).');
        return false;
    }

    if (document.getElementById('dlvCourier').checked) {
        const prov   = document.getElementById('f_province').value;
        const city   = document.getElementById('f_city').value;
        const dist   = document.getElementById('f_district').value;
        const vil    = document.getElementById('f_village').value;
        const postal = document.getElementById('f_postal').value.trim();
        const detail = document.getElementById('f_address').value.trim();

        if (!prov || !city || !dist || !vil || !postal || !detail) {
            alert('Mohon lengkapi seluruh kolom alamat pengiriman (Provinsi → Kelurahan → Kode Pos → Detail).');
            return false;
        }
    }
    return true;
}

// ── VALIDATE STEP 2 ──
function validateStep2() {
    const method = document.querySelector('input[name="method"]:checked');
    if (!method) { alert('Pilih metode pembayaran.'); return false; }
    if (method.value === 'transfer') {
        if (!document.querySelector('input[name="bank"]:checked')) {
            alert('Pilih bank / e-wallet tujuan.'); return false;
        }
        if (!document.getElementById('paymentProof').files.length) {
            alert('Upload bukti transfer terlebih dahulu.'); return false;
        }
    }
    if (method.value === 'store' && !document.getElementById('dlvPickup').checked) {
        alert('"Bayar di Toko" hanya untuk Pick Up.'); return false;
    }
    return true;
}

// ── BUILD REVIEW ──
function buildReview() {
    const v = id => document.getElementById(id);

    v('rv_name').textContent  = v('f_name').value;
    v('rv_phone').textContent = v('f_phone').value;
    v('rv_email').textContent = v('f_email').value;

    const isCourier = document.getElementById('dlvCourier').checked;
    v('rv_dlv').textContent = isCourier ? '🚚 Diantar Kurir' : '🏪 Ambil di Toko';

    v('rv_addr_row').style.display = isCourier ? '' : 'none';
    if (isCourier) {
        const optText = sel => {
            const s = document.getElementById(sel);
            return s && s.selectedIndex > 0 ? s.options[s.selectedIndex].textContent.trim() : '';
        };
        const parts = [
            v('f_address').value.trim(),
            optText('f_village') ? 'Kel. ' + optText('f_village') : '',
            optText('f_district') ? 'Kec. ' + optText('f_district') : '',
            optText('f_city'),
            optText('f_province'),
            v('f_postal').value.trim(),
        ].filter(Boolean);
        v('rv_addr').textContent = parts.join(', ');
    }

    const payVal = document.querySelector('input[name="method"]:checked').value;
    const payNames = { transfer:'🏦 Transfer/E-Wallet', cod:'💵 COD', store:'🏪 Bayar di Toko' };
    v('rv_pay').textContent = payNames[payVal] || payVal;

    if (payVal === 'transfer') {
        v('rv_bank_row').style.display = '';
        v('rv_proof_row').style.display = '';
        const bank = document.querySelector('input[name="bank"]:checked');
        v('rv_bank').textContent  = bank ? bank.value.toUpperCase() : '—';
        const file = document.getElementById('paymentProof').files[0];
        v('rv_proof').textContent = file ? file.name : '—';
    } else {
        v('rv_bank_row').style.display  = 'none';
        v('rv_proof_row').style.display = 'none';
    }
}

// ── DELIVERY TOGGLE ──
function toggleDelivery() {
    const isCourier = document.getElementById('dlvCourier').checked;
    document.getElementById('addrBlock').style.display   = isCourier ? '' : 'none';
    document.getElementById('pickupNote').style.display  = isCourier ? 'none' : '';
    document.getElementById('ongkirRow').style.display   = isCourier ? '' : 'none';
    document.getElementById('ongkirVal').textContent     = isCourier ? 'Rp 10.000–20.000' : 'Gratis';
}
document.getElementById('dlvCourier').addEventListener('change', toggleDelivery);
document.getElementById('dlvPickup').addEventListener('change', toggleDelivery);
toggleDelivery();

// ── PAYMENT TOGGLE ──
function togglePayment() {
    const v = document.querySelector('input[name="method"]:checked')?.value;
    document.getElementById('sec_transfer').style.display = v === 'transfer' ? '' : 'none';
    document.getElementById('sec_cod').style.display      = v === 'cod'      ? 'block' : 'none';
    document.getElementById('sec_store').style.display    = v === 'store'    ? 'block' : 'none';
}
document.querySelectorAll('input[name="method"]').forEach(r => r.addEventListener('change', togglePayment));
togglePayment();

// ── BANK REK DATA ──
const rekMap = {
    bca:       { name:'BCA',        num:'1234-567-890' },
    bri:       { name:'BRI',        num:'0987-654-321' },
    bni:       { name:'BNI',        num:'1122-3344-55' },
    mandiri:   { name:'Mandiri',    num:'9988-776-655' },
    bsi:       { name:'BSI',        num:'7766-554-433' },
    cimb:      { name:'CIMB Niaga', num:'6655-443-322' },
    dana:      { name:'DANA',       num:'0812-xxxx-xxxx' },
    gopay:     { name:'GoPay',      num:'0813-xxxx-xxxx' },
    ovo:       { name:'OVO',        num:'0814-xxxx-xxxx' },
    shopeepay: { name:'ShopeePay',  num:'0815-xxxx-xxxx' },
    linkaja:   { name:'LinkAja',    num:'0816-xxxx-xxxx' },
    qris:      { name:'QRIS',       num:'FaceShop QRIS' },
};
document.querySelectorAll('input[name="bank"]').forEach(r => {
    r.addEventListener('change', () => {
        const d = rekMap[r.value] || {};
        document.getElementById('destName').textContent = d.name || '—';
        document.getElementById('destNum').textContent  = d.num  || '—';
    });
});
function copyRek() {
    const n = document.getElementById('destNum').textContent;
    if (n && n !== '—') navigator.clipboard.writeText(n).then(() => alert('Disalin: ' + n));
}

// ── PREVIEW UPLOAD ──
document.getElementById('paymentProof').addEventListener('change', function() {
    const img = document.getElementById('previewImg');
    if (this.files[0]) { img.src = URL.createObjectURL(this.files[0]); img.style.display = 'block'; }
    else img.style.display = 'none';
});
</script>
</body>
</html>

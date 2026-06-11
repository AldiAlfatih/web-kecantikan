<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Faceshop</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>

<body>

@include('layout.navbar')

@php
    use Illuminate\Support\Facades\Storage;

    // fallback image (public/assets/image/1.png)
    $fallbackImg = asset('assets/image/1.png');

    /**
     * Helper kecil untuk URL gambar produk.
     * - Jika $p->image ada => ambil dari storage disk public
     * - Jika kosong => fallback
     */
    $imgUrl = function ($p) use ($fallbackImg) {
        if (!empty($p?->image)) {
            return Storage::url($p->image);
        }
        return $fallbackImg;
    };
@endphp

{{-- =====================================================
   GUEST (BELUM LOGIN)
===================================================== --}}
@if(!$isLogin)

<section class="hero">
    <div class="hero-text">
        <h1>
            Temukan <span class="highlight">Kecantikan</span><br>
            yang Sesuai dengan<br>
            <span class="highlight-soft">Warna Kulitmu!</span>
        </h1>

        <p>
            Dapatkan rekomendasi produk kosmetik terbaik yang sesuai
            dengan jenis kulit dan warna kulitmu melalui analisis kulit personal kami.
        </p>

        <div class="hero-btn">
            <a href="{{ route('daftar.show') }}" class="btn-primary">Mulai Skin Quiz →</a>
            <a href="{{ route('produk') }}" class="btn-outline">Lihat Produk</a>
        </div>
    </div>

    <div class="hero-image">
        <img src="{{ asset('assets/image/hero-product.png') }}" alt="Produk">
    </div>
</section>

<section class="product">
    <div class="section-header">
        <div>
            <small>Koleksi Terbaik</small>
            <h2>Produk <span>Unggulan</span></h2>
        </div>

        <a href="{{ route('produk') }}" class="btn-lite">Lihat Semua →</a>
    </div>

    <div class="product-list">
        @forelse(($bestSellers ?? collect()) as $p)
            <a href="{{ route('produk.show', $p->id) }}" class="card card-link">
                <span class="badge">Best Seller</span>

                <img
                    src="{{ $imgUrl($p) }}"
                    alt="{{ $p->name }}"
                    onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
                >

                <h4>{{ $p->brand ?? 'FaceShop' }}</h4>
                <h3>{{ $p->name }}</h3>
                <p>{{ $p->skin_type ?? 'Semua Jenis Kulit' }}</p>

                <strong>RP {{ number_format($p->price ?? 0, 0, ',', '.') }}</strong>
            </a>
        @empty
            @for($i=0;$i<3;$i++)
                <div class="card">
                    <span class="badge">Best Seller</span>
                    <img src="{{ $fallbackImg }}" alt="Produk">
                    <h4>FaceShop</h4>
                    <h3>Produk</h3>
                    <p>—</p>
                    <strong>RP 0</strong>
                </div>
            @endfor
        @endforelse
    </div>
</section>

@endif


{{-- =====================================================
   USER LOGIN (SUDAH LOGIN)
===================================================== --}}
@if($isLogin)

<section class="home-user-hero">
    <div class="home-user-hero-inner">
        <h1>Hai, {{ Auth::user()->name ?? 'Beauty' }} 👋</h1>
        <p>
            Kami sudah menyiapkan rekomendasi produk yang cocok untuk tone & undertone kamu.
            <a href="{{ route('profile') }}" class="hero-link">Ubah profil</a>
        </p>

        <div class="home-hero-actions">
            <a href="{{ route('rekomendasi') }}" class="btn-primary-pill">Lihat Semua Rekomendasi →</a>
            <a href="{{ route('produk') }}" class="btn-outline-pill">Jelajahi Produk</a>
        </div>
    </div>
</section>

<section class="product">
    <div class="section-header">
        <div>
            <small>Untuk Kamu</small>
            <h2>Produk <span>Rekomendasi</span></h2>
        </div>

        <a href="{{ route('rekomendasi') }}" class="btn-lite">Lihat Semua →</a>
    </div>

    <div class="product-list">
        @forelse(($recommendedProducts ?? collect()) as $p)
            <a href="{{ route('produk.show', $p->id) }}" class="card card-link">
                <span class="badge">Recommended</span>

                <img
                    src="{{ $imgUrl($p) }}"
                    alt="{{ $p->name }}"
                    onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
                >

                <h4>{{ $p->brand ?? 'FaceShop' }}</h4>
                <h3>{{ $p->name }}</h3>
                <p>Sesuai dengan analisis kulitmu</p>

                <strong>RP {{ number_format($p->price ?? 0, 0, ',', '.') }}</strong>
            </a>
        @empty
            <div class="empty-card">
                <h3>Belum ada rekomendasi 😭</h3>
                <p>Lengkapi profil PCA dulu supaya rekomendasi muncul.</p>
                <a href="{{ route('profile') }}" class="btn-primary">Lengkapi Profil →</a>
            </div>
        @endforelse
    </div>
</section>


<section class="product">
    <div class="section-header">
        <div>
            <small>Koleksi Lengkap</small>
            <h2>Produk <span>Lainnya</span></h2>
        </div>

        <a href="{{ route('produk') }}" class="btn-lite">Lihat Semua →</a>
    </div>

    <div class="product-list">
        @forelse(($otherProducts ?? collect()) as $p)
            <a href="{{ route('produk.show', $p->id) }}" class="card card-link">

                <img
                    src="{{ $imgUrl($p) }}"
                    alt="{{ $p->name }}"
                    onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
                >

                <h4>{{ $p->brand ?? 'FaceShop' }}</h4>
                <h3>{{ $p->name }}</h3>
                <p>{{ $p->skin_type ?? 'Semua Jenis Kulit' }}</p>

                <strong>RP {{ number_format($p->price ?? 0, 0, ',', '.') }}</strong>
            </a>
        @empty
            @for($i=0;$i<6;$i++)
                <div class="card">
                    <img src="{{ $fallbackImg }}" alt="Produk">
                    <h4>FaceShop</h4>
                    <h3>Produk</h3>
                    <p>—</p>
                    <strong>RP 0</strong>
                </div>
            @endfor
        @endforelse
    </div>
</section>

@endif

@include('layout.footer')

</body>
</html>
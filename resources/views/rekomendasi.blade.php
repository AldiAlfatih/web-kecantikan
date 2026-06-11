<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekomendasi | Faceshop</title>

  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/rekomendasi.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">

</head>
<body class="faceshop-body">

@include('layout.navbar')

@php
  use Illuminate\Support\Facades\Storage;

  $fallbackImg = asset('assets/image/1.png');

  $productImg = function ($product) use ($fallbackImg) {
      if ($product && !empty($product->image)) {
          return Storage::url($product->image);
      }
      return $fallbackImg;
  };
@endphp

<section class="rekom-header">
  <div class="rekom-header-inner">
    <h1>Rekomendasi Personal Untukmu</h1>
    <p>Sistem menganalisis profil warna kulitmu dan memilih produk terbaik.</p>
  </div>
</section>

<section class="rekom-page">

  {{-- Jika PCA belum lengkap --}}
  @if(!$pca || !$tone || !$undertone)

    <div class="rekom-alert">
      <div class="rekom-alert-text">
        <b>Profil analisis warna belum lengkap</b>
        <p>Lengkapi profil PCA terlebih dahulu untuk mendapatkan rekomendasi.</p>
      </div>
      <a href="{{ route('profile') }}" class="btn-primary-mini">
        Lengkapi Profil
      </a>
    </div>

  @else

    <div class="rekom-top">
      <div>
        <h2>Produk yang Cocok Untukmu</h2>

        <div class="match-pill">
          Tone: <b>{{ $tone }}</b> •
          Undertone: <b>{{ $undertone }}</b>
        </div>

        @if($pca->season)
          <div class="season-pill">
            Season: <b>{{ ucfirst($pca->season) }}</b>
          </div>
        @endif
      </div>
    </div>

  @endif

  {{-- GRID PRODUK --}}
  <div class="rekom-grid">

    @forelse($recommendations as $shade)

      @php
        $product = $shade->product; // biar ringkas dan aman
      @endphp

      <div class="rekom-card">

        <span class="badge">Direkomendasikan</span>

        <a href="{{ route('produk.show', $product->id) }}" class="rekom-link">

          <div class="rekom-img">
            <img
              src="{{ $productImg($product) }}"
              alt="{{ $product->name }}"
              onerror="this.onerror=null;this.src='{{ $fallbackImg }}';"
            >
          </div>

          <div class="rekom-body">

            <small class="brand">{{ $product->brand }}</small>

            <h3 class="name">
              {{ $product->name }}
            </h3>

            <div class="shade-line">
              <span class="dot" style="background: {{ $shade->hex_color ?? '#ddd' }}"></span>

              <div class="shade-text">
                <b>{{ $shade->shade_name }}</b>
                <small>
                  {{ ucfirst($shade->tone) }} • {{ ucfirst($shade->undertone) }}
                </small>
              </div>
            </div>

            <div class="reason">
              Cocok untuk undertone {{ $undertone }}
            </div>

            <div class="price">
              Rp {{ number_format($product->price, 0, ',', '.') }}
            </div>

            <div class="cta">
              Lihat Detail →
            </div>

          </div>
        </a>
      </div>

    @empty

      @if($tone && $undertone)
        <div class="rekom-empty">
          <b>Tidak ada produk yang benar-benar cocok.</b>
          <p>
            Sistem belum menemukan kecocokan optimal
            berdasarkan tone {{ $tone }} dan undertone {{ $undertone }}.
          </p>
          <a href="{{ route('profile') }}" class="btn-primary-mini">
            Perbarui Profil
          </a>
        </div>
      @endif

    @endforelse

  </div>

</section>

@include('layout.footer')

</body>
</html>
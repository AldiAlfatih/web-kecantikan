<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Skin Tone AI | Faceshop</title>

  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/tryon.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/skin_analysis.css') }}">
</head>

<body class="faceshop-body">

@include('layout.navbar')

<section class="tryon-page">

  <header class="tryon-head">
    <div class="mini-pill" style="margin-bottom: 14px;">
      <span class="dot"></span>
      AI · Client-Side · Real-Time
    </div>
    <h1 class="tryon-title">Skin Tone <span>AI</span></h1>
    <p class="tryon-subtitle" style="max-width: 600px; margin: 0 auto; line-height: 1.6;">
      Aktifkan kamera, posisikan wajahmu di dalam oval, dan biarkan AI mendeteksi warna kulitmu 
      untuk menemukan shade foundation yang paling cocok.
    </p>
  </header>

  <div class="tryon-wrap">

    {{-- LEFT: CAMERA --}}
    <div class="tryon-card camera-card">

      <div class="card-top">
        <div class="mini-pill" id="saPill">
          <span class="dot" id="saPillDot"></span>
          <span id="saPillLabel">Live Preview</span>
        </div>

        <button class="icon-btn" type="button" id="saBtnFlip" title="Ganti Kamera">↺</button>
      </div>

      <div class="camera-stage">
        <div class="camera-frame">

          {{-- AREA LUAR (Bisa diisi warna skin tone setelah deteksi) --}}
          <div class="camera-area" id="saCameraArea" style="--shade: #e8ded7; position: relative; overflow: hidden;">
            
            <video id="saVideo" autoplay playsinline muted></video>
            
            {{-- AR Canvas --}}
            <canvas id="saCanvas"></canvas>

            <div class="camera-empty" id="saCameraEmpty" style="position: absolute; z-index: 1;">
              <div class="empty-text">
                <b style="font-size: 24px; opacity: 0.5;">📷</b>
                <b style="margin-top: 8px; display: block;">Kamera belum aktif</b>
                <small style="display: block;">Tekan tombol di bawah untuk mulai.</small>
              </div>
            </div>
            
            {{-- Status overlay untuk loading AI --}}
            <div class="sa-status-overlay" id="saStatusOverlay">
              <span class="sa-spinner"></span>
              <span id="saStatusText">Memulai kamera...</span>
            </div>

          </div>

        </div>
      </div>

      <div class="camera-actions">
        <button type="button" class="btn-primary" id="saBtnStart">
          Aktifkan Kamera
        </button>

        <button type="button" class="btn-secondary" id="saBtnStop" disabled>
          Matikan
        </button>
      </div>

      <div style="margin-top: 12px; display: none;" id="saBtnAnalyzeWrap">
        <button type="button" class="btn-wide" id="saBtnAnalyze" style="width: 100%; border: none; font-size: 14.5px;">
          Analisis Sekarang
        </button>
      </div>

      <p class="camera-note">
        * Proses AI berjalan 100% di browser — privasi data kamera terjamin.
      </p>

    </div>

    {{-- RIGHT: RESULT & RECOMMENDATIONS --}}
    <aside class="tryon-card product-card" style="display: flex; flex-direction: column;">

      {{-- STATE 1: Waiting --}}
      <div id="saPanelWaiting" style="text-align: center; padding: 40px 20px; flex: 1;">
        <div style="font-size: 48px; opacity: 0.2; margin-bottom: 20px;">🎨</div>
        <h3 style="font-size: 18px; color: #7d1030; margin-bottom: 10px;">Siap untuk dianalisis</h3>
        <p style="font-size: 13.5px; color: #777; line-height: 1.6;">
          Posisikan wajah tepat di area oval dengan pencahayaan yang terang dan merata, 
          lalu klik <b>Analisis Sekarang</b>.
        </p>

        @if(isset($pca) && $pca)
          <div style="margin-top: 30px; text-align: left; padding: 16px; background: rgba(0,0,0,0.03); border-radius: 16px; border: 1px solid rgba(0,0,0,0.05);">
            <p style="font-size: 12px; font-weight: 800; color: #555; margin: 0 0 8px;">Profil Terakhir Anda:</p>
            <p style="font-size: 13px; color: #333; margin: 0;">
              Tone: <b>{{ $toneLevels[$pca->skin_tone_level] ?? 'Tidak diketahui' }}</b><br>
              Undertone: <b>{{ ucfirst($pca->undertone) }}</b>
            </p>
          </div>
        @endif
      </div>

      {{-- STATE 2: Skeleton / Loading --}}
      <div id="saPanelSkeleton" style="display: none; padding: 20px;">
        <p style="font-size: 13px; font-weight: 700; color: #999; margin-bottom: 16px;">Menganalisis & mencari rekomendasi...</p>
        @for($i = 0; $i < 3; $i++)
          <div style="display: flex; gap: 12px; margin-bottom: 16px; padding: 12px; border: 1px solid rgba(0,0,0,0.05); border-radius: 16px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: #f0e8e4; flex-shrink: 0;"></div>
            <div style="flex: 1;">
              <div style="height: 10px; width: 60%; background: #f0e8e4; border-radius: 4px; margin-bottom: 8px;"></div>
              <div style="height: 10px; width: 40%; background: #f0e8e4; border-radius: 4px;"></div>
            </div>
          </div>
        @endfor
      </div>

      {{-- STATE 3: Results --}}
      <div id="saPanelResult" style="display: none; flex: 1; display: flex; flex-direction: column;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <h3 style="font-size: 16px; font-weight: 800; color: #2f2f2f; margin: 0;">Hasil Analisis</h3>
          <button type="button" id="saBtnRetake" class="btn-secondary" style="padding: 6px 12px; font-size: 12px; width: auto;">
            Ulang
          </button>
        </div>

        <div class="shade-row" style="margin-bottom: 20px;">
          <div class="shade-swatch" id="saToneSwatch" style="background: #C8A882; border-radius: 50%;"></div>
          <div class="shade-info">
            <b id="saToneLabel" style="font-size: 14px;">Medium</b>
            <small id="saToneUndertone" style="font-size: 12px; opacity: 0.7;">Undertone: Warm</small>
          </div>
        </div>

        <div id="saSavePrompt" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: rgba(214,106,134,0.08); border-radius: 14px; margin-bottom: 20px; border: 1px solid rgba(125,16,48,0.1);">
          <div style="font-size: 12px; color: #7d1030;">
            <b style="display: block; font-size: 13px;">Simpan Profil?</b>
            Tingkatkan rekomendasi produkmu.
          </div>
          <button type="button" id="saBtnSave" class="btn-primary" style="padding: 8px 16px; font-size: 12px; width: auto;">Simpan</button>
        </div>

        <div id="saSaveSuccess" style="display: none; padding: 10px 14px; background: rgba(34,197,94,0.1); color: #166534; font-size: 12.5px; font-weight: 700; border-radius: 14px; margin-bottom: 20px;">
          Profil berhasil diperbarui.
        </div>

        <h4 style="font-size: 13px; font-weight: 800; color: #7d1030; margin-bottom: 12px; text-transform: uppercase;">Produk Rekomendasi</h4>
        
        <div id="saRekomList" style="flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; padding-bottom: 20px;">
          {{-- Diisi oleh JS --}}
        </div>

        <div style="margin-top: auto; padding-top: 16px; border-top: 1px solid rgba(0,0,0,0.08);">
          <a href="{{ route('rekomendasi') }}" class="btn-wide" style="display: block; text-align: center; width: 100%; box-sizing: border-box;">
            Lihat Semua Rekomendasi
          </a>
        </div>

      </div>

    </aside>

  </div>

</section>

@include('layout.footer')

<script>
  window.__SA_ROUTES__ = {
    recommend : "{{ route('api.skin.recommend') }}",
    save      : "{{ route('api.skin.save') }}",
  };
</script>

<script src="{{ asset('assets/js/face_detector.js') }}"></script>
<script src="{{ asset('assets/js/ar_canvas.js') }}"></script>
<script src="{{ asset('assets/js/skin_color_analyzer.js') }}"></script>
<script src="{{ asset('assets/js/skin_analysis.js') }}"></script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Analisis Warna Kulit | Faceshop</title>

  {{-- Google Fonts: Poppins (mengikuti design system existing) --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  {{-- CSS Existing (global) --}}
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">

  {{-- CSS Halaman Baru --}}
  <link rel="stylesheet" href="{{ asset('assets/css/skin_analysis.css') }}">
</head>
<body class="faceshop-body">

{{-- NAVBAR EXISTING --}}
@include('layout.navbar')

{{-- =====================================================
     PAGE HEADER
===================================================== --}}
<section class="sa-header">
  <div class="sa-header-inner">
    <div class="sa-badge">
      <span class="sa-badge-dot"></span>
      AI · Client-Side · Real-Time
    </div>
    <h1 class="sa-title">Analisis <span>Warna Kulit</span></h1>
    <p class="sa-subtitle">
      Aktifkan kamera, arahkan wajah, dan biarkan sistem mendeteksi warna kulitmu
      secara otomatis untuk rekomendasi produk yang paling cocok.
    </p>
  </div>
</section>

{{-- =====================================================
     MAIN CONTENT
===================================================== --}}
<section class="sa-page">
  <div class="sa-wrap">

    {{-- ================================================
         LEFT CARD — KAMERA
    ================================================ --}}
    <div class="sa-card sa-card-camera">

      {{-- Top bar --}}
      <div class="sa-card-top">
        <div class="sa-pill">
          <span class="sa-pill-dot" id="saPillDot"></span>
          <span id="saPillLabel">Live Preview</span>
        </div>

        <div style="display:flex; gap:8px; align-items:center;">
          <button class="sa-icon-btn" type="button" id="saBtnFlip" title="Ganti Kamera (depan/belakang)">
            ↺
          </button>
        </div>
      </div>

      {{-- Camera frame --}}
      <div class="sa-camera-frame">
        <div class="sa-camera-area" id="saCameraArea">

          {{-- Video stream --}}
          <video id="saVideo" autoplay playsinline muted></video>

          {{-- Canvas AR Overlay (MediaPipe Face Mesh — aktif sejak Fase 3) --}}
          <canvas id="saCanvas"></canvas>

          {{-- Placeholder saat kamera off --}}
          <div class="sa-camera-empty" id="saCameraEmpty">
            <div class="sa-empty-ico">📷</div>
            <p class="sa-empty-title">Kamera belum aktif</p>
            <p class="sa-empty-sub">Tekan tombol di bawah untuk mulai.<br>Izin kamera akan diminta satu kali.</p>
          </div>

          {{-- Status overlay (over video) --}}
          <div class="sa-status-overlay" id="saStatusOverlay">
            <span class="sa-spinner"></span>
            <span id="saStatusText">Memulai kamera...</span>
          </div>

        </div>
      </div>{{-- /camera-frame --}}

      {{-- Camera action buttons --}}
      <div class="sa-camera-actions">
        <button type="button" class="sa-btn-start" id="saBtnStart">
          📸 Aktifkan Kamera
        </button>

        <button type="button" class="sa-btn-stop" id="saBtnStop" disabled>
          Matikan
        </button>
      </div>

      {{-- Tombol Analisis (muncul setelah kamera aktif, dishow via JS) --}}
      <div style="margin-top: 12px; display:none;" id="saBtnAnalyzeWrap">
        <button
          type="button"
          id="saBtnAnalyze"
          style="
            width:100%;
            background: linear-gradient(to right,#f06b83,#e65b7a);
            color:#fff; border:none; border-radius:999px;
            padding:14px 16px; font-size:15px; font-weight:900;
            cursor:pointer; font-family:'Poppins',sans-serif;
            box-shadow:0 12px 22px rgba(230,91,122,.22);
            display:flex; align-items:center; justify-content:center; gap:8px;
          "
        >
          🔍 Analisis Sekarang
        </button>
      </div>

      <p class="sa-camera-note">
        * Proses AI (MediaPipe Face Mesh) berjalan 100% di browser Anda — tidak ada data kamera
        yang dikirim ke server. Izin kamera hanya digunakan selama halaman ini terbuka.
      </p>

    </div>{{-- /sa-card-camera --}}

    {{-- ================================================
         RIGHT CARD — HASIL & REKOMENDASI
    ================================================ --}}
    <aside class="sa-card sa-card-result">

      {{-- STATE 1: Menunggu analisis --}}
      <div id="saPanelWaiting" class="sa-waiting">
        <div class="sa-waiting-ico">🎨</div>
        <h3>Siap untuk dianalisis</h3>
        <p>
          Aktifkan kamera, arahkan wajah ke depan dengan pencahayaan yang cukup,
          lalu klik <b>"Analisis Sekarang"</b>.
        </p>

        @if($pca)
          <div style="
            margin-top:20px;
            padding:14px;
            border-radius:14px;
            background:rgba(214,106,134,.08);
            border:1px solid rgba(125,16,48,.12);
            text-align:left;
          ">
            <p style="font-size:12px; font-weight:700; color:#7d1030; margin:0 0 6px;">
              ✅ Profil PCA Sebelumnya
            </p>
            <p style="font-size:13px; color:#555; margin:0; line-height:1.5;">
              Tone: <b>{{ $toneLevels[$pca->skin_tone_level] ?? 'Tidak diketahui' }}</b><br>
              Undertone: <b>{{ ucfirst($pca->undertone) }}</b>
              @if($pca->season)
                <br>Season: <b>{{ $pca->season }}</b>
              @endif
            </p>
          </div>
        @endif
      </div>

      {{-- STATE 2: Skeleton Loader (tampil saat fetch API) --}}
      <div id="saPanelSkeleton" class="sa-skeleton" style="display:none;">
        <div style="font-size:13px; font-weight:700; color:#999; margin-bottom:4px; padding:0 4px;">
          Mengambil rekomendasi produk...
        </div>
        @for($i = 0; $i < 3; $i++)
        <div class="sa-skeleton-item">
          <div class="sa-skel-dot"></div>
          <div class="sa-skel-lines">
            <div class="sa-skel-line"></div>
            <div class="sa-skel-line"></div>
          </div>
        </div>
        @endfor
      </div>

      {{-- STATE 3: Hasil Analisis --}}
      <div id="saPanelResult" class="sa-result" style="display:none;">

        {{-- Header: judul + tombol ulang --}}
        <div class="sa-result-head">
          <h3 class="sa-result-title">✨ Hasil Analisis</h3>
          <button type="button" class="sa-result-retake" id="saBtnRetake">
            Ulang
          </button>
        </div>

        {{-- Tone summary: swatch + label --}}
        <div class="sa-tone-row">
          <div class="sa-tone-swatch" id="saToneSwatch" style="background:#C8A882;"></div>
          <div class="sa-tone-info">
            <span class="sa-tone-label" id="saToneLabel">Sedang / Kuning Langsat (Medium)</span>
            <span class="sa-tone-undertone" id="saToneUndertone">Undertone: Warm</span>
          </div>
        </div>

        {{-- Simpan ke profil --}}
        <div class="sa-save-prompt" id="saSavePrompt" style="display:none;">
          <div class="sa-save-text">
            <span>Simpan ke profilmu?</span>
            Hasil analisis ini akan memperbarui rekomendasi Anda.
          </div>
          <button type="button" class="sa-btn-save" id="saBtnSave">Simpan</button>
        </div>

        {{-- Simpan berhasil --}}
        <div class="sa-save-success" id="saSaveSuccess">
          ✅ Profil berhasil diperbarui dari hasil analisis AI.
        </div>

        {{-- Daftar produk rekomendasi --}}
        <p class="sa-rekom-head">Produk yang Cocok Untukmu</p>

        <div class="sa-rekom-list" id="saRekomList">
          {{-- Diisi oleh JavaScript --}}
        </div>

        {{-- CTA: Try-On + Lihat Semua --}}
        <div class="sa-rekom-actions">
          <a href="{{ route('rekomendasi') }}" class="sa-btn-tryon" id="saBtnTryOn">
            👁️ Virtual Try-On
          </a>
          <a href="{{ route('rekomendasi') }}" class="sa-btn-all-rekom">
            Lihat Semua →
          </a>
        </div>

      </div>{{-- /saPanelResult --}}

    </aside>{{-- /sa-card-result --}}

  </div>{{-- /sa-wrap --}}
</section>

@include('layout.footer')

{{-- =====================================================
     DATA DARI BLADE → JS (routes, dll.)
===================================================== --}}
<script>
  window.__SA_ROUTES__ = {
    recommend : "{{ route('api.skin.recommend') }}",
    save      : "{{ route('api.skin.save') }}",
  };
</script>

{{-- =====================================================
     JS: Load berurutan (dependency order)
     1. face_detector.js      — MediaPipe Face Mesh wrapper
     2. skin_color_analyzer.js — pixel sampling + IF-THEN rules + canvas drawing
     3. skin_analysis.js       — orchestrator utama
===================================================== --}}
<script src="{{ asset('assets/js/face_detector.js') }}"></script>
<script src="{{ asset('assets/js/skin_color_analyzer.js') }}"></script>
<script src="{{ asset('assets/js/skin_analysis.js') }}"></script>

</body>
</html>

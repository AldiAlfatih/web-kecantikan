<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya | Faceshop</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/profile.css">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/custom-select.css') }}">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Override profile.css fields to match elegant style */
    .field input[type="text"],
    .field input[type="email"],
    .field textarea,
    .field select {
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
    .field input:focus,
    .field select:focus,
    .field textarea:focus {
      outline: none;
      border-color: #e65b7a;
      box-shadow: 0 0 0 3px rgba(230,91,122,0.13);
    }
    .field input:disabled {
      background: #f5f0f2;
      color: #aaa;
      cursor: not-allowed;
    }

    /* Address section title */
    .addr-block-title {
      font-size: 0.8rem;
      font-weight: 700;
      color: #e65b7a;
      text-transform: uppercase;
      letter-spacing: 0.07em;
      margin: 1.2rem 0 0.6rem;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      padding-bottom: 0.4rem;
      border-bottom: 1.5px solid #fce8ed;
    }

    /* Current address badge */
    .current-addr-badge {
      background: linear-gradient(135deg, #fce8ed, #fff5f7);
      border: 1.5px solid #f3c6d0;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.82rem;
      color: #5a4250;
      margin-top: 0.7rem;
    }
    .current-addr-badge b { color: #e65b7a; }
    .current-addr-badge small { color: #b0a0a8; display: block; margin-top: 0.2rem; }

    .addr-hint {
      font-size: 0.76rem;
      color: #b0a0a8;
      margin-top: 0.25rem;
    }
  </style>
</head>
<body class="faceshop-body">

@include('layout.navbar')

@php
  $profile = $user->profile;
  $pca = $user->pcaProfile;

  $p_skin_type  = strtolower($profile->skin_type ?? '');
  $p_undertone  = strtolower($pca->undertone ?? '');
  $p_vein_color = strtolower($pca->vein_color ?? '');
  $p_tone_level = (int) ($pca->skin_tone_level ?? 0);

  $savedAddress = $user->address ?? '';
@endphp

<section class="profile-section">
  <div class="profile-head">
    <div>
      <h1 class="profile-title">Profil Saya</h1>
      <p class="profile-subtitle">Atur data diri dan preferensi untuk rekomendasi shade yang lebih akurat.</p>
    </div>

    @if(session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif
  </div>

  @if ($errors->any())
    <div class="alert-error">
      <b>Gagal menyimpan:</b>
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="profile-tabs">
    <a href="{{ route('profile') }}" class="tab active">Profil & Preferensi</a>
    <a href="{{ route('orders.index') }}" class="tab">Pesanan Saya</a>
  </div>

  <form class="profile-wrapper" method="POST" action="{{ route('profile.update') }}">
    @csrf

    {{-- ══════════ KIRI ══════════ --}}
    <div class="profile-card">
      <div class="card-title">
        <h2>Informasi Pribadi</h2>
        <small>* wajib diisi</small>
      </div>

      <div class="field">
        <label>Nama Lengkap <span class="req">*</span></label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Nama lengkap">
      </div>

      <div class="field">
        <label>Email</label>
        <input type="email" value="{{ $user->email }}" disabled>
        <small class="hint">Email tidak bisa diubah.</small>
      </div>

      <div class="field">
        <label>Nomor Telepon <span class="req">*</span></label>
        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
      </div>

      {{-- ════ ALAMAT GAYA SHOPEE ════ --}}
      <div class="addr-block-title">📍 Alamat Pengiriman</div>

      {{-- Row 1: Provinsi + Kota --}}
      <div class="addr-grid-2">
        <div class="field">
          <label class="addr-label">Provinsi<span class="req">*</span></label>
          <select name="province_name" id="p_province" class="custom-sel" disabled>
            <option value="">-- Pilih Provinsi --</option>
          </select>
          <input type="hidden" name="province" id="ph_province">
        </div>
        <div class="field">
          <label class="addr-label">Kota / Kabupaten<span class="req">*</span></label>
          <select name="city_name" id="p_city" class="custom-sel" disabled>
            <option value="">-- Pilih Provinsi dulu --</option>
          </select>
          <input type="hidden" name="city" id="ph_city">
        </div>
      </div>

      {{-- Row 2: Kecamatan + Kelurahan + Kode Pos --}}
      <div class="addr-grid-3" style="margin-bottom:0.75rem;">
        <div class="field">
          <label class="addr-label">Kecamatan<span class="req">*</span></label>
          <select name="district_name" id="p_district" class="custom-sel" disabled>
            <option value="">-- Pilih Kota dulu --</option>
          </select>
          <input type="hidden" name="district" id="ph_district">
        </div>
        <div class="field">
          <label class="addr-label">Kelurahan / Desa<span class="req">*</span></label>
          <select name="village_name" id="p_village" class="custom-sel" disabled>
            <option value="">-- Pilih Kecamatan dulu --</option>
          </select>
          <input type="hidden" name="village" id="ph_village">
        </div>
        <div class="field">
          <label class="addr-label">Kode Pos</label>
          <input type="text" name="postal_code" id="p_postal"
            value="{{ old('postal_code') }}"
            placeholder="Contoh: 91111" maxlength="10">
        </div>
      </div>

      {{-- Row 3: Detail --}}
      <div class="field">
        <label class="addr-label">Detail Alamat (No. Rumah / Blok / Patokan)</label>
        <textarea name="address_detail" id="p_detail" rows="3"
          placeholder="Contoh: Jl. Budi Utomo No. 12, RT 03/RW 02, dekat masjid Al-Ikhlas"
        >{{ old('address_detail') }}</textarea>
        <p class="addr-hint">Masukkan nama jalan, nomor rumah/blok, dan patokan terdekat.</p>
      </div>

      {{-- Preview alamat tersimpan --}}
      @if($savedAddress)
        <div class="current-addr-badge">
          <b>📌 Alamat Tersimpan:</b><br>
          {{ $savedAddress }}
          <small>Isi form di atas untuk memperbarui alamat.</small>
        </div>
      @endif
    </div>

    {{-- ══════════ KANAN ══════════ --}}
    <div class="profile-card">
      <div class="card-title">
        <h2>Profil Warna Kulit</h2>
        <small>* wajib diisi</small>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Jenis Kulit <span class="req">*</span></label>
          <select name="skin_type" class="custom-sel" id="s_skin_type">
            @foreach ([
              'normal' => 'Normal',
              'berminyak' => 'Berminyak',
              'kering' => 'Kering',
              'kombinasi' => 'Kombinasi',
              'sensitif' => 'Sensitif',
            ] as $val => $label)
              <option value="{{ $val }}" {{ old('skin_type', $profile->skin_type ?? '') === $val ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="field">
          <label>Warna Urat Nadi</label>
          <select name="vein_color" class="custom-sel" id="s_vein">
            <option value="" {{ old('vein_color', $p_vein_color) ? '' : 'selected' }}>— (opsional)</option>
            <option value="blue_purple" {{ old('vein_color', $p_vein_color) === 'blue_purple' ? 'selected' : '' }}>Kebiruan / Ungu</option>
            <option value="green_olive" {{ old('vein_color', $p_vein_color) === 'green_olive' ? 'selected' : '' }}>Kehijauan / Olive</option>
            <option value="mixed"       {{ old('vein_color', $p_vein_color) === 'mixed'       ? 'selected' : '' }}>Campuran / Sulit dibedakan</option>
          </select>
          <small class="hint">Lihat di cahaya alami dekat jendela.</small>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Tingkat Kecerahan Kulit <span class="req">*</span></label>
          <select name="skin_tone_level" id="s_tone" class="custom-sel" required>
            <option value="1" {{ (string)old('skin_tone_level',$p_tone_level?:'') === '1' ? 'selected':'' }}>Sangat Terang (Fair)</option>
            <option value="2" {{ (string)old('skin_tone_level',$p_tone_level?:'') === '2' ? 'selected':'' }}>Terang (Light)</option>
            <option value="3" {{ (string)old('skin_tone_level',$p_tone_level?:'') === '3' ? 'selected':'' }}>Sedang / Kuning Langsat (Medium)</option>
            <option value="4" {{ (string)old('skin_tone_level',$p_tone_level?:'') === '4' ? 'selected':'' }}>Sawo Matang Terang (Tan)</option>
            <option value="5" {{ (string)old('skin_tone_level',$p_tone_level?:'') === '5' ? 'selected':'' }}>Sawo Matang Gelap (Deep)</option>
            <option value="6" {{ (string)old('skin_tone_level',$p_tone_level?:'') === '6' ? 'selected':'' }}>Gelap (Dark)</option>
          </select>
        </div>

        <div class="field">
          <label>Undertone (otomatis)</label>
          <input type="text" value="{{ $p_undertone ? strtoupper($p_undertone) : '-' }}" disabled>
          <small class="hint">Dihitung dari warna urat nadi.</small>
        </div>
      </div>

      <div class="field">
        <label>Masalah Kulit (opsional)</label>
        @php
          $problems = ['Jerawat','Komedo','Pori-pori Besar','Kulit Kusam','Hiperpigmentasi','Bekas Jerawat'];
          $selectedFromDb = ($profile && $profile->skin_problem)
            ? array_map('trim', explode(',', $profile->skin_problem))
            : [];
          $selected = old('skin_problem')
            ? old('skin_problem')
            : array_map(fn($x) => ucwords($x), $selectedFromDb);
        @endphp
        <div class="pill-grid">
          @foreach ($problems as $problem)
            <label class="pill">
              <input type="checkbox" name="skin_problem[]" value="{{ $problem }}"
                {{ in_array($problem, $selected) ? 'checked' : '' }}>
              <span>{{ $problem }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="profile-action">
        <button type="submit" class="btn-save">Simpan Perubahan</button>
      </div>

      <div class="note">
        <b>Catatan:</b> Rekomendasi shade memakai <b>Tingkat Kecerahan</b> & <b>Undertone</b> kamu.
      </div>
    </div>
  </form>
</section>

@include('layout.footer')

<script src="{{ asset('assets/js/wilayah.js') }}"></script>
<script>
/* =====================================================
   PROFILE — CHAINED WILAYAH DROPDOWN
===================================================== */
WilayahDropdown.init({
    provinceEl: document.getElementById('p_province'),
    cityEl:     document.getElementById('p_city'),
    districtEl: document.getElementById('p_district'),
    villageEl:  document.getElementById('p_village'),
});

// Sync hidden inputs with the textual name
[
    ['p_province', 'ph_province'],
    ['p_city',     'ph_city'],
    ['p_district', 'ph_district'],
    ['p_village',  'ph_village'],
].forEach(([selId, hidId]) => {
    const sel = document.getElementById(selId);
    const hid = document.getElementById(hidId);
    if (sel && hid) {
        sel.addEventListener('change', () => {
            const opt = sel.options[sel.selectedIndex];
            hid.value = opt && opt.value ? opt.textContent.trim() : '';
        });
    }
});

// Init custom selects for non-wilayah selects in the profile card
document.querySelectorAll('#s_skin_type, #s_vein, #s_tone').forEach(el => {
    if (window.CustomSelect) CustomSelect.build(el);
});
</script>
</body>
</html>
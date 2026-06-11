<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Saya | Faceshop</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/profile.css">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
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

    {{-- KIRI --}}
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

      <div class="field">
        <label>Alamat Lengkap <span class="req">*</span></label>
        <textarea name="address" rows="4" placeholder="Alamat lengkap untuk pengiriman">{{ old('address', $user->address) }}</textarea>
      </div>
    </div>

    {{-- KANAN --}}
    <div class="profile-card">
      <div class="card-title">
        <h2>Profil Warna Kulit</h2>
        <small>* wajib diisi</small>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Jenis Kulit <span class="req">*</span></label>
          <select name="skin_type">
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
          <label>Warna Urat Nadi (opsional)</label>
          <select name="vein_color">
            <option value="" {{ old('vein_color', $p_vein_color) ? '' : 'selected' }}>-</option>

            <option value="blue_purple" {{ old('vein_color', $p_vein_color) === 'blue_purple' ? 'selected' : '' }}>
              Kebiruan / Ungu
            </option>

            <option value="green_olive" {{ old('vein_color', $p_vein_color) === 'green_olive' ? 'selected' : '' }}>
              Kehijauan / Olive
            </option>

            <option value="mixed" {{ old('vein_color', $p_vein_color) === 'mixed' ? 'selected' : '' }}>
              Campuran / Sulit dibedakan
            </option>
          </select>

          <small class="hint">Lihat di cahaya alami dekat jendela.</small>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Tingkat Kecerahan Kulit <span class="req">*</span></label>
          <select name="skin_tone_level" required>
            <option value="1" {{ (string) old('skin_tone_level', $p_tone_level ?: '') === '1' ? 'selected' : '' }}>Sangat terang (Fair)</option>
            <option value="2" {{ (string) old('skin_tone_level', $p_tone_level ?: '') === '2' ? 'selected' : '' }}>Terang (Light)</option>
            <option value="3" {{ (string) old('skin_tone_level', $p_tone_level ?: '') === '3' ? 'selected' : '' }}>Sedang / Kuning langsat (Medium)</option>
            <option value="4" {{ (string) old('skin_tone_level', $p_tone_level ?: '') === '4' ? 'selected' : '' }}>Sawo matang terang (Tan)</option>
            <option value="5" {{ (string) old('skin_tone_level', $p_tone_level ?: '') === '5' ? 'selected' : '' }}>Sawo matang gelap (Deep)</option>
            <option value="6" {{ (string) old('skin_tone_level', $p_tone_level ?: '') === '6' ? 'selected' : '' }}>Gelap (Dark)</option>
          </select>
        </div>

        <div class="field">
          <label>Undertone (hasil otomatis)</label>
          <input type="text" value="{{ $p_undertone ? strtoupper($p_undertone) : '-' }}" disabled>
          <small class="hint">Undertone dihitung otomatis dari warna urat nadi.</small>
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
        <b>Catatan:</b> Rekomendasi shade akan memakai <b>Tingkat Kecerahan</b> & <b>Undertone</b> kamu.
      </div>
    </div>
  </form>
</section>

@include('layout.footer')
</body>
</html>
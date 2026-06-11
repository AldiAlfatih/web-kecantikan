<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar | FaceShop</title>

  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/daftar.css">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
</head>

<body>

@include('layout.navbar')

<section class="daftar-section">
  <div class="daftar-wrapper">

    <h1>Selamat Datang di FaceShop</h1>
    <p class="subtitle">Daftar untuk mendapatkan rekomendasi personal</p>

    <div class="daftar-card">
      <h2>Daftar</h2>

      {{-- ✅ ALERT SUCCESS --}}
      @if(session('success'))
        <div class="alert-success">
          {{ session('success') }}
        </div>
      @endif

      {{-- ✅ ALERT ERROR VALIDATION --}}
      @if($errors->any())
        <div class="alert-error">
          <b>Oops, ada yang salah:</b>
          <ul>
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('daftar.store') }}">
        @csrf

        <!-- ========================
             KOLOM KIRI (DATA AKUN)
        ======================== -->
        <div class="form-col">
          <h3>Data Akun</h3>

          <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
          </div>

          <div class="form-group">
            <label>Alamat</label>
            <input type="text" name="address" value="{{ old('address') }}" placeholder="Masukkan alamat" required>
          </div>

          <div class="form-group">
            <label>Nomor Telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Masukkan nomor telepon" required>
          </div>
        </div>

        <!-- ========================
             KOLOM KANAN (PROFIL KECANTIKAN)
        ======================== -->
        <div class="form-col">
          <h3>Profil Kecantikan</h3>

          <div class="form-group">
            <label>Jenis Kulit</label>
            <select name="skin_type" required>
              <option value="">Pilih jenis kulit</option>
              <option value="Normal" {{ old('skin_type')=='Normal'?'selected':'' }}>Normal</option>
              <option value="Berminyak" {{ old('skin_type')=='Berminyak'?'selected':'' }}>Berminyak</option>
              <option value="Kering" {{ old('skin_type')=='Kering'?'selected':'' }}>Kering</option>
              <option value="Sensitif" {{ old('skin_type')=='Sensitif'?'selected':'' }}>Sensitif</option>
              <option value="Kombinasi" {{ old('skin_type')=='Kombinasi'?'selected':'' }}>Kombinasi</option>
            </select>
          </div>

          <div class="form-group">
            <label>Warna Kulit</label>
            <select name="skin_tone_level" required>
              <option value="">Pilih warna kulit</option>
              <option value="1" {{ old('skin_tone_level')=='1'?'selected':'' }}>Sangat terang</option>
              <option value="2" {{ old('skin_tone_level')=='2'?'selected':'' }}>Terang</option>
              <option value="3" {{ old('skin_tone_level')=='3'?'selected':'' }}>Sedang</option>
              <option value="4" {{ old('skin_tone_level')=='4'?'selected':'' }}>Sawo matang terang</option>
              <option value="5" {{ old('skin_tone_level')=='5'?'selected':'' }}>Sawo matang gelap</option>
              <option value="6" {{ old('skin_tone_level')=='6'?'selected':'' }}>Gelap</option>
            </select>
          </div>

          <div class="form-group">
            <label>Undertone</label>
            <select name="undertone" required>
              <option value="">Pilih undertone</option>
              <option value="warm" {{ old('undertone')=='warm'?'selected':'' }}>Warm</option>
              <option value="neutral" {{ old('undertone')=='neutral'?'selected':'' }}>Neutral</option>
              <option value="cool" {{ old('undertone')=='cool'?'selected':'' }}>Cool</option>
            </select>
          </div>

          <div class="form-group">
            <label>Masalah Kulit</label>
            <select name="skin_problem" required>
              <option value="">Pilih masalah kulit</option>
              <option value="Jerawat" {{ old('skin_problem')=='Jerawat'?'selected':'' }}>Jerawat</option>
              <option value="Kusam" {{ old('skin_problem')=='Kusam'?'selected':'' }}>Kusam</option>
              <option value="Flek" {{ old('skin_problem')=='Flek'?'selected':'' }}>Flek</option>
              <option value="Pori-pori" {{ old('skin_problem')=='Pori-pori'?'selected':'' }}>Pori-pori besar</option>
              <option value="Kemerahan" {{ old('skin_problem')=='Kemerahan'?'selected':'' }}>Kemerahan</option>
            </select>
          </div>

          <div class="form-group">
            <label>Warna Pembuluh Darah</label>
            <select name="vein_color" required>
              <option value="">Pilih warna pembuluh darah</option>
              <option value="blue_purple" {{ old('vein_color')=='blue_purple'?'selected':'' }}>Biru / Ungu</option>
              <option value="green_olive" {{ old('vein_color')=='green_olive'?'selected':'' }}>Hijau / Olive</option>
              <option value="mixed" {{ old('vein_color')=='mixed'?'selected':'' }}>Campuran</option>
            </select>
          </div>
        </div>

        <!-- ========================
             ACTION BUTTON
        ======================== -->
        <div class="form-actions">
          <button type="submit" class="btn-daftar">Daftar</button>

          <div class="login-text">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk sekarang</a>
          </div>
        </div>
      </form>

    </div>
  </div>
</section>

@include('layout.footer')

</body>
</html>
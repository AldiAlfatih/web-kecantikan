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
            <input type="password" name="password" placeholder="Masukkan password (min. 8 karakter)" required>
          </div>

          <div class="form-group">
            <label>Verifikasi Password</label>
            <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
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
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Faceshop</title>

  <link rel="stylesheet" href="/assets/css/style.css" />
  <link rel="stylesheet" href="/assets/css/login.css">
  <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}" />
</head>
<body>

@include('layout.navbar')

<section class="login-section">
  <div class="login-wrapper">

    <h1>Selamat Datang di FaceShop</h1>
    <p class="subtitle">Masuk untuk melihat rekomendasi personal</p>

    <div class="login-card">
      <h2>Login</h2>

      <form method="POST" action="{{ route('login.process') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn-login-main">Login</button>

        {{-- ✅ tombol admin ke filament --}}
        <a href="{{ route('filament.admin.auth.login') }}" class="btn-admin-link">
          Login sebagai Admin
        </a>
      </form>

      <p class="register-text">
        Belum punya akun?
        <a href="{{ route('daftar.show') }}">Daftar sekarang</a>
      </p>
    </div>

  </div>
</section>

@include('layout.footer')

</body>
</html>
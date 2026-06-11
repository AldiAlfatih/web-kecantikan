<div class="navbar">
    {{-- KIRI --}}
    <div class="logo">
        <img src="/assets/image/faceshop.png" alt="Faceshop">
        <span>FACESHOP</span>
    </div>

    {{-- TENGAH (CUMA 3 MENU) --}}
    <nav class="nav-menu" id="navMenu">
        <a href="{{ route('home') }}">Beranda</a>
        <a href="{{ route('produk') }}">Produk</a>

        @auth
            <a href="{{ route('rekomendasi') }}">Rekomendasi</a>
        @else
            <a href="{{ route('login') }}">Rekomendasi</a>
        @endauth
    </nav>

    {{-- KANAN --}}
    <div class="nav-right">
        {{-- tombol hamburger (HANYA MUNCUL DI HP) --}}
        <button class="nav-toggle" id="navToggle" type="button" aria-label="Toggle menu">
            ☰
        </button>

        @auth
            {{-- ICON KERANJANG & PESANAN (DI SAMPING KIRI PROFILE) --}}
            <div class="nav-actions">
                <a class="icon-btn" href="{{ route('keranjang') }}" title="Keranjang">
                    🛒
                    {{-- kalau nanti mau badge jumlah item: --}}
                    {{-- <span class="badge-count">{{ $cartCount ?? 0 }}</span> --}}
                </a>

                <a class="icon-btn" href="{{ route('orders.index') }}" title="Pesanan Saya">
                    📦
                </a>
            </div>

            {{-- DROPDOWN PROFILE (CUMA PROFIL + LOGOUT) --}}
            <div class="user-menu">
                <button type="button" id="userToggle" class="user-name">
                    <span class="user-text">{{ Auth::user()->name }}</span>
                    <span class="chev">▾</span>
                </button>

                <div id="userDropdown" class="dropdown">
                    <a href="{{ route('profile') }}">Profil</a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}">
                <button class="btn-login">Login</button>
            </a>
        @endauth
    </div>
</div>

{{-- SCRIPT DROPDOWN + MOBILE MENU --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    // ===== DROPDOWN USER =====
    const userToggle = document.getElementById("userToggle");
    const userDropdown = document.getElementById("userDropdown");

    if (userToggle && userDropdown) {
        userToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle("show");
        });

        document.addEventListener("click", function () {
            userDropdown.classList.remove("show");
        });

        userDropdown.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }

    // ===== MOBILE MENU TOGGLE =====
    const navToggle = document.getElementById("navToggle");
    const navMenu = document.getElementById("navMenu");

    if (navToggle && navMenu) {
        navToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            navMenu.classList.toggle("open");
        });

        document.addEventListener("click", function () {
            navMenu.classList.remove("open");
        });

        navMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });
    }

});
</script>
# 💄 FaceShop — E-Commerce Kosmetik & Beauty Platform Berbasis AI & AR

Platform e-commerce kosmetik modern yang dilengkapi dengan teknologi kecerdasan buatan (**AI Skin Tone Analyzer**) dan Augmented Reality (**AR Virtual Try-On**) untuk membantu pengguna menemukan dan mencoba produk kecantikan (seperti *foundation* & *shade makeup*) yang paling sesuai secara akurat langsung melalui kamera *browser*.

---

## 🌟 Fitur Utama

- 🔍 **AI Skin Tone Analyzer**: Analisis warna kulit (*Tone* & *Undertone*) secara *real-time* via kamera tanpa perlu foto diunggah ke server (*100% Client-Side Privacy*).
- 🪞 **AR Virtual Try-On**: Simulasi pemakaian shade foundation pada wajah dengan pelacakan kontur 478 titik (MediaPipe Face Mesh) dan perpaduan warna alami (*Soft-Light Blending*).
- 🎨 **Personal Color Analysis (PCA)**: Rekomendasi produk berbasis tone, undertone, dan warna palet musiman (*Seasonal Color*).
- 🛍️ **Katalog Produk & Shade Lengkap**: Filter kategori, pencarian produk, varian shade warna dengan visual hex picker.
- 🛒 **Manajemen Keranjang & Checkout**: Sistem pemesanan, konfirmasi pembayaran, dan upload bukti transfer.
- 📊 **Admin Dashboard (Filament)**: Manajemen produk, shade warna, pesanan customer, serta visualisasi grafik penjualan interaktif.

---

## 📋 Prasyarat Sistem

Pastikan perangkat Anda sudah terinstal:

- **PHP** >= 8.2 (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, dan **`intl`** aktif)
- **Composer** (PHP Dependency Manager)
- **Node.js** (v18+) & **NPM**
- **Web Server & Database**: XAMPP / Laragon (MySQL / MariaDB & Apache)
- **Browser Modern**: Google Chrome, Microsoft Edge, atau Mozilla Firefox (Mendukung izin akses kamera)

> [!IMPORTANT]
> **Penting untuk Pengguna XAMPP**:
> Pastikan ekstensi **`intl`** telah aktif di `php.ini`.
> 1. Buka XAMPP Control Panel ➔ Klik **Config** pada Apache ➔ Pilih **PHP (php.ini)**.
> 2. Cari `;extension=intl` lalu hilangkan tanda titik koma (`;`) menjadi: `extension=intl`.
> 3. Simpan file lalu *Restart Apache*.

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi di komputer lokal:

### 1. Clone atau Buka Folder Proyek
Buka terminal/PowerShell pada direktori proyek:
```bash
cd c:\xampp\htdocs\web_kecantikan\web-kecantikan
```

### 2. Install Dependensi PHP & Node.js
```bash
# Install paket PHP / Laravel
composer install

# Install paket Frontend (Vite & Asset)
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env` (jika belum ada):
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan pengaturan database Anda:
```env
APP_NAME="FaceShop"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_kecantikan
DB_USERNAME=root
DB_PASSWORD=
```
*(Pastikan database `web_kecantikan` sudah dibuat di MySQL/phpMyAdmin)*.

### 4. Generate Application Key & Storage Link
```bash
# Generate encryption key
php artisan key:generate

# Link storage publik untuk gambar produk/bukti transfer
php artisan storage:link
```

### 5. Jalankan Migrasi & Seeder Database
Untuk membuat tabel database beserta data dummy (produk, shade, dan akun pengujian):
```bash
php artisan migrate --seed
```
*(Gunakan `php artisan migrate:fresh --seed` jika ingin mereset ulang database dari awal)*.

### 6. Menjalankan Server Aplikasi
Jalankan dua perintah berikut di terminal terpisah:

**Terminal 1 (Backend Server):**
```bash
php artisan serve
```

**Terminal 2 (Frontend Asset Bundler):**
```bash
npm run dev
```

Aplikasi sekarang dapat diakses melalui browser di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)** 🎉

---

## 🔐 Akun Pengujian (Demo Accounts)

Setelah menjalankan seeder, Anda dapat langsung login menggunakan akun default berikut:

### 👤 Akun Admin
- **URL Admin Panel**: `http://127.0.0.1:8000/admin`
- **Email**: `admin@faceshop.test`
- **Password**: `admin12345`

### 👥 Akun Pelanggan (Customer)
- **URL Login**: `http://127.0.0.1:8000/login`

| Nama | Email | Password | Karakteristik Kulit |
| :--- | :--- | :--- | :--- |
| **Aulia Putri** | `aulia@example.com` | `password123` | Fair / Cool Undertone |
| **Nadira Safitri** | `nadira@example.com` | `password123` | Light / Warm Undertone |
| **Rani Maharani** | `rani@example.com` | `password123` | Medium / Neutral Undertone |
| **Dewi Anggraini** | `dewi@example.com` | `password123` | Tan / Warm Undertone |
| **Maya Lestari** | `maya@example.com` | `password123` | Deep / Warm Undertone |

*(Atau klik menu **Daftar** untuk membuat akun baru)*.

---

## 📖 Panduan Penggunaan Fitur

### 1. 🔍 Menggunakan AI Skin Tone Analyzer (Analisis Warna Kulit)
1. Buka menu **Rekomendasi** atau navigasi ke `/skin-analysis`.
2. Klik tombol **"Aktifkan Kamera"** dan berikan izin (*allow*) akses kamera pada browser.
3. Posisikan wajah tepat di dalam area panduan lingkaran/oval kamera.
4. Klik tombol **"Analisis Sekarang"**.
5. Sistem akan membaca piksel warna kulit wajah dan menampilkan:
   - Kategori *Skin Tone* (Fair / Light / Medium / Tan / Deep)
   - Kategori *Undertone* (Cool / Warm / Neutral)
   - Rekomendasi shade produk foundation yang paling cocok.

### 2. 🪞 Menggunakan AR Virtual Try-On (Coba Makeup Virtual)
- **Dari Hasil Analisis**: Klik salah satu shade rekomendasi dari panel hasil analisis untuk langsung melihat pengaplikasian warna pada wajah di kamera.
- **Dari Detail Produk**: Buka halaman produk kosmetik, pilih varian shade, lalu klik tombol **"Try-On"** untuk menguji tampilan shade tersebut secara *real-time*.

### 3. 🛍️ Belanja & Checkout
1. Masukkan produk & shade yang diinginkan ke **Keranjang**.
2. Buka halaman Keranjang (`/keranjang`) lalu klik **Checkout**.
3. Lengkapi informasi pengiriman dan buat pesanan.
4. Buka halaman **Pesanan Saya** (`/pesanan-saya`) untuk melihat nomor pesanan dan mengunggah bukti pembayaran.

### 4. ⚙️ Mengelola Toko (Admin Panel)
1. Buka `/admin` dan login dengan akun administrator.
2. Fitur yang tersedia:
   - **Dashboard**: Statistik pendapatan, jumlah pesanan, dan chart tren 7 & 30 hari.
   - **Produk & Shade**: Tambah/edit produk, upload foto, dan atur kode warna HEX shade dengan interactive color picker.
   - **Pesanan**: Verifikasi bukti pembayaran, ubah status pesanan (pending/proses/selesai/dibatalkan).

---

## 🛠️ Struktur Direktori Utama

```
web-kecantikan/
├── app/
│   ├── Filament/             # Admin panel resource, widget & pages
│   ├── Http/Controllers/     # Controller web & API (Skin Analysis, Order, Produk, dll)
│   └── Models/                  # Model Eloquent (User, Product, Shade, Order, dll)
├── database/
│   ├── migrations/           # Skema tabel database
│   └── seeders/              # Data inisialisasi produk & akun demo
├── public/
│   └── assets/js/
│       ├── face_detector.js        # MediaPipe Face Mesh tracking
│       ├── skin_color_analyzer.js  # Algoritma HSV Skin Tone classifier
│       ├── ar_canvas.js            # HTML5 Canvas AR foundation overlay
│       └── skin_analysis.js        # UI Orchestrator
├── resources/
│   └── views/                # Template Blade (layout, home, produk, checkout, dll)
└── routes/
    └── web.php               # Rute halaman web & API endpoint
```

---

## ❓ Troubleshooting (Masalah Umum)

<details>
<summary><b>1. Kamera tidak mau menyala di fitur Analisis / Try-On</b></summary>

- Pastikan browser diberi izin untuk mengakses kamera (klik ikon gembok/kamera di samping URL browser ➔ Izinkan Kamera).
- Pastikan tidak ada aplikasi lain (seperti Zoom, Teams, OBS) yang sedang menggunakan webcam.
- Fitur kamera memerlukan environment yang aman (`http://localhost`, `http://127.0.0.1`, atau `https://`).
</details>

<details>
<summary><b>2. Error `ext-intl is missing` saat composer install</b></summary>

- Aktifkan baris `extension=intl` di file `php.ini` XAMPP Anda, kemudian simpan dan restart web server Apache.
</details>

<details>
<summary><b>3. Gambar produk tidak tampil</b></summary>

- Jalankan perintah `php artisan storage:link` di terminal untuk menghubungkan storage Laravel dengan folder publik.
</details>

---

## 📄 Lisensi
Proyek ini dikembangkan untuk keperluan aplikasi e-commerce kecantikan berbasis web menggunakan [Laravel Framework](https://laravel.com).

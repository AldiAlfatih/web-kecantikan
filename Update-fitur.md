# Dokumentasi Implementasi Fitur: Skin Tone AI & Virtual Try-On

Dokumen ini memuat detail implementasi penambahan fitur "Skin Tone AI" (Deteksi Warna Kulit berbasis AI) dan "Virtual Try-On" (Simulasi AR Produk) pada project web e-commerce kosmetik. Fitur ini dirancang khusus untuk memfasilitasi pengguna menemukan produk *foundation* yang akurat dengan kulit mereka secara langsung melalui kamera browser.

---

## 1. Ikhtisar Fitur Utama

Pembaruan pada sistem ini memperkenalkan dua modul fungsional utama yang saling terintegrasi:

1. **AI Skin Tone Analyzer**
   Sistem yang secara *real-time* menangkap warna kulit pengguna melalui kamera *device*, memproses warna piksel dari area spesifik (pipi dan dahi), lalu mengklasifikasikan tipe kulit berdasarkan *Tone* (Light, Medium, Tan, Deep) dan *Undertone* (Cool, Warm, Neutral). Fitur ini berjalan 100% *client-side* (langsung di browser) sehingga sangat aman dari aspek privasi (tidak ada foto yang dikirim atau disimpan ke server).

2. **AR Virtual Try-On**
   Pengalaman simulasi (*Augmented Reality*) di mana warna *foundation* yang dipilih akan dilapisi (di-*overlay*) secara langsung pada wajah pengguna melalui video kamera. Teknologi pemetaan wajah memastikan warna produk hanya menempel di area kulit, tanpa menutupi mata dan bibir.

---

## 2. Teknologi yang Digunakan

Untuk memastikan performa yang cepat dan realistis, fitur ini dibangun menggunakan kombinasi teknologi berikut:

*   **AI Face Tracking (Pelacakan Wajah): Menggunakan MediaPipe Face Mesh (Google)**
    MediaPipe Face Mesh adalah librari kecerdasan buatan (*open-source*) buatan Google yang dikhususkan untuk melacak anatomi wajah manusia secara *real-time*. Keunggulan teknologi ini adalah:
    *   **Tingkat Presisi Tinggi (478 Titik):** Sistem memetakan wajah menjadi *mesh* (jaring) yang terdiri dari 478 titik koordinat. Berkat titik yang padat ini, sistem mampu melacak persis di mana ujung bibir, letak kelopak mata, atau batas garis rahang. Ini yang membuat warna *foundation* hanya menempel di area kulit tanpa meluber ke mata.
    *   **Berjalan di Browser (Client-Side):** Proses kalkulasi deteksi wajah dieksekusi murni di dalam *browser* perangkat pengguna (HP/Laptop), bukan di server. Artinya, privasi pengguna 100% aman karena rekaman kamera tidak pernah diunggah ke server, sekaligus membuat server aplikasi tetap ringan.
*   **Skin Tone Analysis: Custom Computer Vision berbasis HSV**
    Daripada bergantung pada API berbayar, sistem ini menggunakan algoritma deteksi warna buatan sendiri (`skin_color_analyzer.js`) yang menargetkan sampel piksel di area netral wajah. Algoritma ini mengonversi cahaya kamera dari format RGB ke spektrum HSV (Hue, Saturation, Value) karena HSV jauh lebih stabil dan tahan terhadap bayangan atau perubahan cahaya ruangan.
*   **AR Virtual Try-On: HTML5 Canvas 2D API & Compositing**
    Sistem merender visual AR murni menggunakan standar Canvas HTML5 tanpa plugin 3D berat (seperti WebGL/Unity). Koordinat dari MediaPipe dikonversi menjadi poligon dinamis. Agar filter terlihat realistis seperti *foundation* asli, diterapkan teknik *blending mode* (`soft-light` untuk *tinting* dan `normal` untuk *coverage*) serta pemotongan lubang (*masking*) di area mata dan bibir (*evenodd fill rule*).

---

## 3. Rincian Pembaruan File & Direktori

Pembaruan ini ditambahkan secara modular ke dalam struktur Laravel agar *codebase* tetap rapi dan mudah dibaca.

### A. Modul Inti (JavaScript)
Seluruh proses algoritma kecerdasan buatan diletakkan di dalam folder `public/assets/js/`:
* **`face_detector.js`**: Menangani akses kamera (MediaDevices API) dan inisialisasi modul MediaPipe Face Mesh untuk menghasilkan 478 titik koordinat wajah.
* **`skin_color_analyzer.js`**: Mengandung logika untuk mengekstrak matriks warna dari *canvas frame*, menghitung nilai rata-rata, lalu menjalankan aturan percabangan (*if-else*) untuk mengelompokkan warna ke dalam standar Tone & Undertone.
* **`ar_canvas.js`**: Modul AR yang bertugas menggambar filter warna. Mengonversi titik koordinat menjadi poligon wajah, lalu mencampurkan warna menggunakan blending agar menyatu natural dengan kulit.
* **`skin_analysis.js` & `tryon.js`**: File *orchestrator* utama yang mengatur logika antarmuka (UI) dan menggabungkan semua modul deteksi.

### B. Controller & Views (Laravel)
* **`resources/views/layout/navbar.blade.php`**: Penambahan fitur *routing*. Menu "Rekomendasi" kini ditautkan secara langsung ke halaman *Skin Tone AI*.
* **`resources/views/skin_analysis.blade.php`** (Baru): Halaman utama tempat fitur deteksi AI bekerja.
* **`resources/views/virtual_tryon.blade.php`** (Refactor): Halaman Try-On dari detail produk yang telah ditingkatkan agar terintegrasi dengan mesin AR baru (kamera *full-screen*, *tracking* MediaPipe).

---

## 4. Alur Penggunaan (User Flow)

Berikut adalah panduan untuk menjalankan dan menguji fitur:

### Skenario 1: Analisis Warna Kulit
1. Jalankan aplikasi dan buka di browser (disarankan Google Chrome).
2. Login sebagai pengguna, kemudian klik menu **Rekomendasi** di navbar.
3. Sistem akan meminta izin akses kamera. Klik **"Aktifkan Kamera"**.
4. Posisikan wajah di depan layar. Indikator *tracking* akan muncul di area wajah jika deteksi berhasil.
5. Klik tombol **"Analisis Sekarang"** yang muncul di bawah kamera.
6. Hasil klasifikasi warna kulit (*Tone* & *Undertone*) beserta rekomendasi produk *foundation* yang cocok akan muncul di panel sebelah kanan.

### Skenario 2: Simulasi AR (Virtual Try-On)
1. Setelah Skenario 1 selesai, klik salah satu kotak *shade* produk di daftar rekomendasi.
2. Secara *real-time*, filter *foundation* akan langsung terpasang di atas wajah pada video kamera.
3. Fitur ini juga bisa dites dengan mengklik tombol **"Try-On"** dari dalam halaman Detail Produk. Pengguna bisa mengganti warna *shade* dan melihat perubahan warnanya secara seketika di wajah.

---

## 5. Panduan Modifikasi & Konfigurasi Kode

Jika ada kebutuhan untuk mendemonstrasikan cara mengubah konfigurasi atau membongkar kode di masa depan, berikut adalah panduannya:

**A. Mengubah Aturan Klasifikasi Warna (Tone & Undertone)**
Buka file `public/assets/js/skin_color_analyzer.js`.
Cari *function* `classify(r, g, b)`. Sistem klasifikasi ini menggunakan rentang persentase ruang warna (HSV *Thresholds*). Angka pada kondisi `if-else` (contoh: `h < 30`, `s < 0.25`) dapat dimodifikasi jika ingin menyesuaikan akurasi bacaan warna agar lebih pas dengan kondisi pencahayaan tertentu.

**B. Menyesuaikan Ketebalan/Opasitas Filter AR di Wajah**
Buka file `public/assets/js/ar_canvas.js`.
Cari *function* `drawARFoundation`. Pada bagian *Layer 1 (Tint & Undertone)* dan *Layer 2 (Coverage/Foundation)*, nilai `ctx.globalAlpha` dapat dinaikkan atau diturunkan. Nilai saat ini (Layer 1: `0.35` dan Layer 2: `0.05`) adalah kalibrasi optimal agar warna menempel tipis tanpa terlihat seperti topeng padat.

**C. Mengubah Algoritma Pengambilan Data (Rekomendasi)**
Buka `app/Http/Controllers/Api/SkinRecommendationController.php`. Algoritma di dalamnya mencocokkan parameter *Tone* dan *Undertone* pengguna dengan parameter produk di *database*. Kriteria *query filtering* atau jumlah produk yang ditampilkan dapat dimodifikasi di file ini.

---
*Dokumen teknis implementasi pengembangan fitur e-commerce.*
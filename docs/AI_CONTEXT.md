# AI Context

Project ini adalah web existing dari client. Sistem utama sudah ada dan tidak boleh di-rebuild dari awal.

Dokumen `/docs/system-requirement.md` adalah dokumen kebutuhan untuk PENAMBAHAN FITUR BARU, bukan kebutuhan keseluruhan sistem.

## Fitur Baru yang Akan Ditambahkan
Fitur baru adalah fitur analisis warna kulit dan simulasi kosmetik berbasis kamera browser.

Kebutuhan fitur:
1. Akses kamera browser.
2. Deteksi wajah otomatis menggunakan MediaPipe Face Mesh.
3. Analisis warna kulit menggunakan rule-based IF-THEN.
4. Rekomendasi produk dari database Laravel + MySQL.
5. AR overlay shade foundation menggunakan HTML5 Canvas.
6. Proses AI dan AR berjalan di sisi client/browser.

## Aturan untuk AI Agent
- Jangan rebuild project dari awal.
- Jangan menghapus atau merusak fitur lama.
- Jangan mengubah desain utama tanpa alasan.
- Analisis dulu struktur folder project.
- Identifikasi halaman, controller, route, model, view, asset JS/CSS yang relevan.
- Buat rencana implementasi sebelum coding.
- Implementasi harus bertahap dan aman.

## Role-Based Workflow for AI Agent

AI agent tidak boleh langsung coding.

Urutan peran yang harus digunakan:

### 1. System Analyst / Codebase Auditor
Tugas:
- Membaca struktur folder project existing.
- Memahami framework, route, controller, model, view, asset JS/CSS, dan database.
- Membedakan fitur existing dan fitur baru yang akan ditambahkan.
- Menentukan titik integrasi paling aman.

### 2. Solution Architect
Tugas:
- Membuat rencana implementasi fitur tambahan.
- Menentukan file baru dan file existing yang perlu diubah.
- Memastikan fitur baru tidak merusak desain, routing, database, dan fitur lama.

### 3. Backend Laravel Developer
Tugas:
- Menyiapkan database produk kosmetik, shade, kategori warna kulit, dan rule IF-THEN.
- Membuat model, migration, seeder, controller, dan endpoint rekomendasi jika dibutuhkan.

### 4. Frontend Developer
Tugas:
- Membuat tampilan kamera.
- Membuat UI hasil analisis warna kulit.
- Membuat UI rekomendasi produk.
- Menjaga desain agar tetap sesuai web existing.

### 5. AI / Computer Vision Developer
Tugas:
- Mengintegrasikan MediaPipe Face Mesh.
- Melacak area wajah secara real-time.
- Mengambil sampel warna kulit dari area wajah.
- Menjalankan proses AI di client-side/browser.

### 6. AR Canvas Developer
Tugas:
- Membuat simulasi shade foundation menggunakan HTML5 Canvas.
- Menampilkan overlay warna secara responsif di wajah pengguna.

### 7. QA Tester
Tugas:
- Membuat skenario pengujian kamera, deteksi wajah, analisis warna, rekomendasi produk, AR overlay, responsivitas, dan performa browser.
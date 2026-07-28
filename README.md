<div align="center">

# 🍱 ALFAGO

### Marketplace & layanan pesan-antar internal sekolah

Pesan makanan dan minuman dari toko atau PKL sekitar sekolah,<br>
lalu antarkan langsung ke kelas dengan proses yang cepat dan terorganisir.

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com)

</div>

---

## 📖 Tentang ALFAGO

**ALFAGO** adalah aplikasi marketplace internal sekolah yang mempertemukan siswa dengan toko sekolah dan pedagang kaki lima (PKL). Siswa dapat mencari produk, memasukkannya ke keranjang, memilih kelas tujuan, lalu membuat pesanan menggunakan metode pembayaran tunai atau QRIS.

Di sisi pengelola, tersedia panel admin untuk mengatur seluruh data master, memproses pesanan, memantau performa penjualan, dan membuat teks konfirmasi WhatsApp yang sudah dikelompokkan berdasarkan vendor.

## ✨ Fitur Utama

### Untuk pengguna

- 🔎 Katalog publik dengan pencarian secara real-time
- 🗂️ Filter produk berdasarkan kategori
- 🏪 Informasi produk, harga, stok, dan vendor
- 🛒 Keranjang belanja interaktif menggunakan Livewire
- 📍 Pemilihan kelas sebagai lokasi pengantaran
- 💵 Dukungan pembayaran **Cash** dan **QRIS**
- 📝 Catatan tambahan pada saat checkout
- 🧾 Riwayat dan detail pesanan
- 📦 Pemantauan status pesanan

### Untuk admin

- 📊 Dashboard analitik penjualan 30 hari
- 📈 Statistik omzet, produk terlaris, kelas teratas, vendor, dan pembayaran
- 🧑‍🍳 Pengelolaan vendor toko maupun PKL
- 🍜 Pengelolaan produk, kategori, kelas, dan banner
- 🚚 Pengaturan biaya pengantaran
- 🔄 Pemrosesan status pesanan secara bertahap
- 💬 Pembuatan teks WhatsApp otomatis untuk setiap vendor
- 🔗 Link grup WhatsApp yang dapat diatur dari data vendor
- 🔐 Pembatasan halaman berdasarkan peran admin dan pengguna

## 🔄 Alur Pesanan

```mermaid
flowchart LR
    A[🔎 Pilih Produk] --> B[🛒 Keranjang]
    B --> C[📍 Pilih Kelas]
    C --> D[💳 Checkout]
    D --> E[⏳ Pending]
    E --> F[👨‍🍳 Diproses]
    F --> G[✅ Selesai]
```

Saat pesanan dikonfirmasi, ALFAGO membuat teks pesanan terpisah untuk masing-masing vendor. Admin dapat menyalin teks tersebut dan langsung membuka grup WhatsApp vendor terkait.

## 🧰 Teknologi

| Bagian | Teknologi |
|---|---|
| Backend | PHP 8.3+, Laravel 13 |
| Antarmuka interaktif | Livewire 4, Alpine.js |
| Styling | Tailwind CSS 4 |
| Grafik | Chart.js 4 |
| Database | MySQL 8 |
| Asset bundler | Vite 8 |
| Pengujian | PHPUnit 12 |

## 🚀 Menjalankan Aplikasi

### Prasyarat

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- MySQL 8
- Ekstensi PHP yang dibutuhkan Laravel, seperti `ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, dan `xml`

### Instalasi

Clone repository, kemudian masuk ke direktori project:

```bash
git clone <url-repository>
cd <nama-repository>
```

Pasang seluruh dependency backend dan frontend:

```bash
composer install
npm install
```

Buat file konfigurasi aplikasi dan generate application key:

```bash
cp .env.example .env
php artisan key:generate
```

Buat database MySQL baru bernama `alfago`, kemudian sesuaikan konfigurasi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alfago
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi, isi data awal, dan buat symbolic link untuk penyimpanan berkas:

```bash
php artisan migrate --seed
php artisan storage:link
```

Build asset frontend:

```bash
npm run build
```

Terakhir, jalankan development server:

```bash
php artisan serve
```

Setelah server berjalan, aplikasi dapat diakses melalui:

| Layanan | Alamat |
|---|---|
| Aplikasi ALFAGO | [http://127.0.0.1:8000](http://127.0.0.1:8000) |
| Panel Admin | [http://127.0.0.1:8000/admin/dashboard](http://127.0.0.1:8000/admin/dashboard) |

Untuk mengembangkan tampilan dengan hot reload, jalankan Vite pada terminal terpisah:

```bash
npm run dev
```

## 🔑 Akun Demo

Seeder menyediakan akun berikut:

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@alfago.test` | `password` |
| Pengguna | `user@alfago.test` | `password` |

Data akun admin dapat disesuaikan melalui variabel berikut pada file `.env` sebelum menjalankan seeder:

```env
ADMIN_NAME="Admin ALFAGO"
ADMIN_EMAIL=admin@alfago.test
ADMIN_PASSWORD=password
```

> [!IMPORTANT]
> Ganti kredensial bawaan sebelum aplikasi digunakan di lingkungan produksi.

## 🧪 Pengujian

Jalankan test suite:

```bash
php artisan test
```

Periksa juga proses build frontend:

```bash
npm run build
```

Pengujian mencakup katalog publik, autentikasi, otorisasi admin, checkout, pengurangan stok, snapshot nilai pesanan, perubahan status, dan pengelompokan teks WhatsApp.

## 🗂️ Struktur Direktori

```text
.
├── app/
│   ├── Enums/          # Status, peran, dan metode pembayaran
│   ├── Http/           # Controller dan middleware
│   ├── Livewire/       # Komponen halaman pengguna dan admin
│   ├── Models/         # Model Eloquent
│   └── Services/       # Logika keranjang dan checkout
├── database/
│   ├── migrations/     # Struktur tabel
│   └── seeders/        # Data awal dan akun demo
├── resources/
│   ├── css/            # Styling aplikasi
│   ├── js/             # JavaScript dan grafik
│   └── views/          # Blade dan tampilan Livewire
├── routes/             # Rute web aplikasi
└── tests/              # Pengujian otomatis
```

## ⚙️ Konfigurasi Penting

- **Biaya pengantaran** dapat diubah melalui menu **Admin → Ongkir**.
- **Link grup WhatsApp** dapat diubah melalui menu **Admin → Vendor → Edit**.
- **Stok kosong** pada data produk berarti stok tidak dibatasi.
- Nilai harga, nama produk, dan ongkir disimpan sebagai snapshot ketika checkout sehingga riwayat pesanan tetap konsisten.

## 🤝 Kontribusi

Kontribusi dapat dilakukan dengan membuat branch baru, mengembangkan perubahan, menjalankan pengujian, lalu mengajukan pull request.

```bash
git checkout -b feature/nama-fitur
```

Pastikan perubahan tetap mengikuti gaya penulisan Laravel dan tidak merusak alur checkout maupun pengelolaan stok.

---

<div align="center">

Dibuat untuk membantu proses jual-beli di lingkungan sekolah menjadi lebih mudah, cepat, dan transparan. ❤️

**ALFAGO — Jajan favorit, diantar ke kelas.**

</div>

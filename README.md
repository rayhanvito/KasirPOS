# Aplikasi Kasir

Aplikasi Kasir ini adalah sistem Point of Sale (POS) yang komprehensif, dirancang untuk membantu bisnis mengelola penjualan, inventaris, dan pelanggan dengan efisien. Dibangun dengan Laravel untuk backend dan Vue.js untuk frontend, aplikasi ini menawarkan antarmuka yang intuitif dan fitur-fitur penting untuk operasional harian.

## Fitur Utama

*   **Manajemen Penjualan (POS)**: Antarmuka kasir yang cepat dan mudah digunakan untuk transaksi penjualan.
*   **Manajemen Produk**: Tambah, edit, dan kelola produk dengan detail lengkap termasuk harga, stok, dan kategori.
*   **Manajemen Stok/Inventaris**: Lacak jumlah stok secara real-time, peringatan stok rendah, dan penyesuaian stok.
*   **Manajemen Pelanggan & Pemasok**: Kelola data pelanggan dan pemasok, termasuk riwayat transaksi.
*   **Laporan Komprehensif**: Laporan penjualan, pembelian, stok, dan keuangan untuk analisis bisnis.
*   **Manajemen Pengeluaran**: Catat dan kelola pengeluaran bisnis.
*   **Responsif**: Tampilan yang dioptimalkan untuk berbagai perangkat (desktop, tablet, mobile).
*   **Sistem Pengguna & Peran**: Kelola pengguna dengan peran dan izin yang berbeda.

## Persyaratan Sistem

Pastikan server Anda memenuhi persyaratan berikut:

*   PHP >= 8.1
*   Composer
*   Node.js (disarankan versi LTS)
*   NPM atau Yarn
*   MySQL (atau database lain yang didukung Laravel)

## Instalasi

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan aplikasi Kasir di lingkungan lokal Anda:

1.  **Kloning Repositori:**
    ```bash
    git clone https://github.com/rayhanvito/KasirPOS.git
    cd KasirPOS
    ```

2.  **Instal Dependensi Composer:**
    ```bash
    composer install
    ```

3.  **Konfigurasi Environment:**
    *   Buat salinan file `.env.example` dan ganti namanya menjadi `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Buka file `.env` dan konfigurasikan pengaturan database Anda (DB_DATABASE, DB_USERNAME, DB_PASSWORD).
    *   Juga, pastikan `APP_URL` diatur dengan benar.

4.  **Buat Kunci Aplikasi:**
    ```bash
    php artisan key:generate
    ```

5.  **Migrasi Database dan Seeder (Opsional, untuk data awal):**
    ```bash
    php artisan migrate --seed
    ```
    Jika Anda ingin memulai dengan database kosong, cukup jalankan `php artisan migrate`.

6.  **Instal Dependensi Node.js:**
    ```bash
    npm install
    # atau jika Anda menggunakan yarn
    # yarn install
    ```

7.  **Kompilasi Aset Frontend:**
    ```bash
    npm run build
    ```
    Untuk pengembangan, Anda bisa menggunakan:
    ```bash
    npm run dev
    ```

8.  **Jalankan Server Pengembangan Laravel:**
    ```bash
    php artisan serve
    ```

9.  **Akses Aplikasi:**
    Buka browser Anda dan kunjungi `http://localhost:8000` (atau URL yang dikonfigurasi di `APP_URL` Anda).

    *   **Login Default (jika menggunakan `--seed`):**
        *   Email: `admin@example.com`
        *   Password: `password`

---
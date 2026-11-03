# LangkapuraLokal — Direktori Digital UMKM Langkapura

Platform direktori digital berbasis web responsif untuk menemukan usaha mikro, kecil, dan menengah (UMKM) di wilayah **Kecamatan Langkapura, Kota Bandar Lampung**.

Dibuat berdasarkan spesifikasi **[PRD_LangkapuraLokal.md](PRD_LangkapuraLokal.md)** dan rancangan UI/UX **[desaind.md](desaind.md)**.

---

## 🌟 Fitur Utama

### 1. Area Publik (Pengunjung & Warga)
- **Beranda (`index.php`)**:
  - Banner disclaimers prototipe data simulasi.
  - Hero pencarian UMKM *"Temukan Usaha Lokal di Sekitarmu"*.
  - Filter kelurahan (Langkapura, Langkapura Baru, Gunung Terang, Bilabong Jaya).
  - Kata kunci populer (*Keripik Pisang*, *Kopi Robusta*, *Bengkel*, *Sembako*, *Jasa Jahit*, dll.).
  - Grid kategori UMKM dengan ikon dan jumlah usaha aktif.
  - Kartu UMKM pilihan terverifikasi.
  - Bagian alur cara kerja direktori (Cari -> Lihat Detail -> Hubungi WA).
  - CTA Daftarkan Usaha untuk pemilik UMKM.
- **Katalog / Jelajahi Usaha (`katalog.php`)**:
  - Kolom pencarian dinamis & multi-filter (kategori, kelurahan, kata kunci).
  - Pengurutan data (terbaru, nama A-Z, nama Z-A).
  - Empty state ramah jika hasil pencarian tidak ditemukan dengan opsi reset filter.
- **Detail Usaha (`detail.php`)**:
  - Foto utama dan informasi profil lengkap.
  - Status buka dan jam operasional.
  - Alamat lengkap dan petunjuk Google Maps.
  - Daftar produk/jasa beserta estimasi harga.
  - **Tombol Utama "Hubungi via WhatsApp"** dengan format pesan pembuka sopan otomatis.

### 2. Area Pemilik Usaha (`owner/`)
- **Autentikasi (`login.php` & `register.php`)**:
  - Pendaftaran akun pemilik dan login dengan hashing kata sandi BCRYPT.
  - Tombol demo login 1-klik untuk pengujian cepat.
- **Dashboard Pemilik (`owner/index.php`)**:
  - Kartu metrik: Total usaha, disetujui, menunggu moderasi, dan perlu perbaikan.
  - Daftar usaha milik pengguna beserta status moderasi dan catatan admin.
- **Pengajuan & Edit Usaha (`owner/tambah.php` & `owner/edit.php`)**:
  - Form pengajuan usaha baru lengkap dengan validasi kolom wajib.
  - Status otomatis masuk antrean `Menunggu Moderasi` saat disimpan atau diedit.

### 3. Area Administrator (`admin/`)
- **Dashboard Admin (`admin/index.php`)**:
  - Ringkasan statistik direktori real-time.
  - **Antrean Cepat Moderasi**: aksi 1-klik setujui atau tolak dengan modal catatan perbaikan.
- **Manajemen Usaha (`admin/usaha.php` & `admin/usaha_detail.php`)**:
  - Tabel seluruh data UMKM dengan filter status dan pencarian.
  - Pemeriksaan kelayakan data secara mendalam.
  - Aksi setujui, minta perbaikan, nonaktifkan, dan hapus entri.
- **Manajemen Kategori (`admin/kategori.php`)**:
  - Tambah kategori baru, pemilihan ikon Bootstrap Icons, dan toggle aktif/nonaktif.

---

## 🎨 Sistem Desain (Sesuai `desaind.md`)
- **Warna Utama**: `#176B45` (Forest Green)
- **Warna Aksen**: `#2E8B57` (Sea Green) & WhatsApp `#25D366`
- **Latar Belakang**: Soft Green-Gray `#F4F7F5` & `#FFFFFF`
- **Tipografi**:
  - Headings: `Plus Jakarta Sans`
  - Body Text: `Inter`
- **Desain**: Mobile-first, kartu sudut membulat (*rounded corners*), bayangan lembut, dan kontras teks yang ramah pengguna.

---

## 🚀 Cara Menjalankan Aplikasi

Aplikasi telah dirancang **Zero-Config** menggunakan PHP PDO dengan dukungan otomatis **SQLite** bawaan (otomatis dibuat dan diisi data contoh realistik saat pertama kali dijalankan). Anda juga dapat menggunakan **MySQL/MariaDB** di XAMPP jika diinginkan.

### Menjalankan dengan PHP Built-in Server (Sangat Cepat):
Buka terminal/PowerShell di direktori proyek ini:
```bash
php -S 127.0.0.1:8000
```
Lalu buka browser di alamat:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Akun Demo untuk Pengujian

Tersedia tombol **Mode Demo Cepat (1-Klik)** pada halaman login (`login.php`), atau gunakan kredensial berikut:

| Peran (Role) | Email | Kata Sandi | Halaman Akses |
|---|---|---|---|
| **Admin** | `admin@langkapuralokal.id` | `admin123` | `/admin/index.php` |
| **Pemilik UMKM (Demo 1)** | `budi@umkm.id` | `pemilik123` | `/owner/index.php` |
| **Pemilik UMKM (Demo 2)** | `siti@umkm.id` | `pemilik123` | `/owner/index.php` |

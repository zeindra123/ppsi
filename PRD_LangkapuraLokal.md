# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## LangkapuraLokal — Direktori Digital UMKM Langkapura

**Versi:** 1.0  
**Status:** Draft  
**Platform:** Website responsif  
**Wilayah awal:** Kelurahan Langkapura, Bandar Lampung, Lampung  
**Bahasa utama:** Bahasa Indonesia

---

## 1. Ringkasan Produk

**LangkapuraLokal** adalah platform direktori digital yang membantu masyarakat menemukan usaha mikro, kecil, dan menengah (UMKM) di wilayah Langkapura, Bandar Lampung. Pengunjung dapat mencari usaha berdasarkan nama, kategori, atau kata kunci, melihat informasi usaha, lalu menghubungi pemilik usaha secara langsung melalui WhatsApp.

Platform ini juga menyediakan dashboard bagi pemilik usaha untuk mengelola profil dan informasi usahanya serta dashboard admin untuk memoderasi data agar informasi yang ditampilkan tetap relevan dan layak.

LangkapuraLokal berfokus pada **penemuan usaha dan penghubungan pelanggan dengan penjual**, bukan transaksi di dalam platform.

## 2. Latar Belakang dan Masalah

Sebagian UMKM lokal belum memiliki kanal digital yang mudah ditemukan masyarakat. Informasi mengenai nama usaha, produk, lokasi, jam operasional, dan kontak dapat tersebar di media sosial atau hanya diketahui melalui rekomendasi dari mulut ke mulut.

Akibatnya:
- Masyarakat kesulitan menemukan UMKM di sekitar Langkapura.
- Informasi usaha sulit dibandingkan karena tersebar di banyak tempat.
- Pemilik UMKM memiliki keterbatasan dalam mempromosikan usaha secara digital.
- Informasi usaha yang tidak diperbarui dapat membingungkan calon pelanggan.

LangkapuraLokal menyediakan satu direktori terpusat yang mudah diakses melalui browser.

## 3. Tujuan Produk

### 3.1 Tujuan Utama
1. Memudahkan masyarakat menemukan UMKM di Langkapura.
2. Membantu UMKM memperoleh visibilitas digital.
3. Mempercepat calon pelanggan menghubungi pemilik usaha.
4. Menyediakan pengelolaan data usaha yang terstruktur dan dapat dimoderasi.

### 3.2 Bukan Tujuan (Non-Goals)
Untuk versi MVP, produk **tidak** ditujukan untuk:
- Memproses checkout atau pembayaran di dalam website.
- Menyediakan layanan kurir atau pelacakan pengiriman.
- Menyediakan fitur chat internal.
- Menyediakan rating dan ulasan pelanggan.
- Menyediakan aplikasi native Android atau iOS.
- Menjual paket langganan kepada pemilik usaha.
- Mengintegrasikan WhatsApp Business API.
- Menjamin kualitas, stok, harga, atau ketersediaan produk yang ditawarkan oleh setiap usaha.

## 4. Target Pengguna dan Persona

### 4.1 Pengunjung / Calon Pelanggan
**Kebutuhan:** Menemukan usaha lokal dan memperoleh informasi yang cukup sebelum menghubungi penjual.

**Karakteristik:**
- Mengakses website melalui ponsel atau komputer.
- Mencari berdasarkan nama usaha, kategori, atau jenis produk.
- Membutuhkan kontak dan informasi lokasi yang jelas.

**Keberhasilan pengguna:** Pengunjung menemukan usaha yang sesuai dan berhasil membuka tautan WhatsApp atau kontak yang tersedia.

### 4.2 Pemilik UMKM
**Kebutuhan:** Menampilkan usaha secara digital dan memperbarui informasi tanpa harus membuat website sendiri.

**Karakteristik:**
- Memiliki usaha di wilayah layanan Langkapura.
- Membutuhkan formulir sederhana untuk memasukkan profil usaha, kategori, foto, lokasi, dan kontak.
- Memerlukan status pengajuan yang jelas.

**Keberhasilan pengguna:** Pemilik usaha mengirim data yang lengkap, mengetahui status moderasi, dan dapat memperbarui informasi usahanya.

### 4.3 Admin
**Kebutuhan:** Mengelola pengguna, kategori, dan daftar usaha serta memastikan konten yang ditampilkan sesuai.

**Karakteristik:**
- Memiliki akses khusus ke dashboard admin.
- Memeriksa pengajuan baru dan perubahan data.
- Dapat menyetujui, menolak, menyunting, menonaktifkan, atau menghapus data sesuai kewenangan.

**Keberhasilan pengguna:** Data usaha dikelola secara konsisten dan konten yang tidak sesuai tidak ditampilkan secara publik.

## 5. Ruang Lingkup Produk

### 5.1 Fitur MVP
- Halaman beranda publik.
- Katalog atau direktori UMKM.
- Pencarian usaha.
- Filter kategori.
- Halaman detail usaha.
- Tombol kontak penjual melalui WhatsApp jika nomor tersedia.
- Informasi alamat atau lokasi usaha jika tersedia.
- Registrasi dan login pemilik usaha.
- Dashboard pemilik usaha.
- Formulir pengajuan dan pengelolaan profil usaha.
- Status pengajuan usaha.
- Dashboard admin.
- Moderasi pengajuan usaha.
- Pengelolaan kategori.
- Pengelolaan akun dan status usaha.
- Desain responsif untuk desktop dan perangkat seluler.
- Validasi formulir dan penanganan kondisi kosong atau gagal.

### 5.2 Fitur Setelah MVP
Fitur berikut dapat dipertimbangkan setelah kebutuhan MVP tervalidasi:
- Peta interaktif.
- Statistik kunjungan dan klik kontak bagi pemilik usaha.
- Fitur penandaan usaha unggulan oleh admin.
- Galeri produk yang lebih lengkap.
- Notifikasi status pengajuan melalui email.
- Ekspor data usaha untuk kebutuhan laporan.
- Dukungan wilayah lain setelah penerapan awal dievaluasi.

Fitur lanjutan tidak dianggap bagian dari MVP kecuali disetujui secara eksplisit.

## 6. Alur Utama Pengguna

### 6.1 Pengunjung Mencari Usaha
1. Pengunjung membuka halaman beranda.
2. Pengunjung memasukkan kata kunci atau memilih kategori.
3. Sistem menampilkan daftar usaha yang sesuai dan berstatus publik.
4. Pengunjung membuka halaman detail usaha.
5. Pengunjung melihat profil, deskripsi, kategori, lokasi, jam operasional, dan kontak yang tersedia.
6. Pengunjung menekan tombol WhatsApp untuk menghubungi penjual.

### 6.2 Pemilik Mengajukan Usaha
1. Pemilik membuat akun atau login.
2. Pemilik membuka dashboard.
3. Pemilik mengisi formulir data usaha.
4. Sistem memvalidasi kolom wajib dan format data.
5. Pemilik mengirim pengajuan.
6. Sistem memberi status **Menunggu Moderasi**.
7. Admin memeriksa pengajuan.
8. Sistem memperbarui status menjadi disetujui atau ditolak.
9. Jika disetujui, usaha dapat ditampilkan pada katalog publik.

### 6.3 Admin Memoderasi Usaha
1. Admin login ke dashboard admin.
2. Admin membuka daftar pengajuan.
3. Admin memeriksa kelengkapan dan kesesuaian informasi.
4. Admin menyetujui atau menolak pengajuan, disertai catatan bila diperlukan.
5. Sistem menyimpan keputusan dan memperbarui status usaha.
6. Usaha yang disetujui dan aktif dapat tampil di halaman publik.

## 7. Persyaratan Fungsional

### FR-01 — Beranda Publik
- Sistem menampilkan identitas LangkapuraLokal dan navigasi utama.
- Sistem menampilkan pengantar singkat, kolom pencarian, kategori, dan pilihan usaha.
- Sistem menyediakan akses menuju katalog serta alur pendaftaran usaha.
- Sistem menampilkan kondisi yang informatif jika belum ada usaha yang dipublikasikan.

### FR-02 — Katalog UMKM
- Sistem menampilkan usaha dengan status disetujui dan aktif.
- Setiap kartu usaha menampilkan informasi ringkas, seperti nama, kategori, gambar jika tersedia, dan lokasi ringkas jika tersedia.
- Pengunjung dapat membuka halaman detail usaha.
- Sistem menyediakan pagination atau mekanisme pemuatan bertahap jika jumlah data banyak.

### FR-03 — Pencarian dan Filter
- Pengunjung dapat mencari berdasarkan nama usaha atau kata kunci yang relevan.
- Pengunjung dapat memfilter daftar berdasarkan kategori.
- Pencarian dan filter dapat digunakan bersamaan.
- Jika tidak ada hasil, sistem menampilkan pesan yang jelas dan opsi menghapus filter.

### FR-04 — Detail Usaha
- Halaman menampilkan nama usaha, kategori, deskripsi, gambar, alamat, jam operasional, dan kontak sesuai data yang tersedia.
- Data opsional yang kosong tidak ditampilkan sebagai informasi palsu; sistem dapat menampilkan label bahwa informasi belum tersedia jika relevan.
- Tombol WhatsApp hanya ditampilkan jika nomor kontak valid tersedia.
- Tautan kontak menggunakan format yang aman dan sesuai.
- Usaha nonaktif, ditolak, atau belum disetujui tidak dapat diakses sebagai halaman publik.

### FR-05 — Akun Pemilik Usaha
- Pemilik dapat mendaftar, login, dan logout.
- Sistem memvalidasi input dan menjaga kata sandi dengan mekanisme hashing yang aman.
- Pesan kesalahan tidak membocorkan informasi sensitif.
- Pemilik hanya dapat mengakses dan mengelola data usaha miliknya sendiri.

### FR-06 — Dashboard Pemilik
- Dashboard menampilkan ringkasan usaha milik pengguna dan status pengajuan.
- Pemilik dapat mengajukan usaha baru jika memenuhi ketentuan.
- Pemilik dapat mengedit informasi usaha miliknya.
- Perubahan data yang memerlukan pemeriksaan ulang dapat masuk ke status menunggu moderasi.
- Pemilik dapat melihat alasan penolakan jika admin memberikan catatan.

### FR-07 — Formulir Usaha
- Kolom wajib minimal mencakup nama usaha, kategori, deskripsi singkat, dan kontak.
- Kolom alamat/lokasi, jam operasional, dan gambar disediakan sesuai desain dan kebutuhan implementasi.
- Sistem memvalidasi panjang teks, format nomor kontak, serta tipe dan ukuran file gambar.
- Sistem menampilkan pesan kesalahan yang dekat dengan kolom terkait.
- Sistem mencegah pengiriman formulir berulang yang tidak disengaja.

### FR-08 — Dashboard Admin
- Admin dapat melihat ringkasan jumlah usaha menurut status.
- Admin dapat melihat daftar usaha dan melakukan pencarian atau filter.
- Admin dapat membuka detail data usaha untuk diperiksa.
- Admin dapat menyetujui, menolak, mengaktifkan, menonaktifkan, atau menghapus data sesuai kewenangan.
- Admin dapat mengelola kategori.
- Tindakan penting harus dikonfirmasi sebelum dijalankan.

### FR-09 — Moderasi
- Status minimum usaha: **Draft**, **Menunggu Moderasi**, **Disetujui**, **Ditolak**, dan **Nonaktif**.
- Hanya usaha yang disetujui dan aktif yang ditampilkan kepada publik.
- Keputusan penolakan sebaiknya menyertakan alasan.
- Sistem mencatat waktu perubahan status dan admin yang melakukan tindakan jika struktur data memungkinkan.

### FR-10 — Keamanan dan Hak Akses
- Halaman dashboard memerlukan autentikasi.
- Hak akses pemilik dan admin dibedakan.
- Validasi dilakukan di sisi server, bukan hanya di browser.
- Input ditangani untuk mengurangi risiko SQL injection dan cross-site scripting.
- Aksi yang mengubah data dilindungi dari permintaan tidak sah.
- Kredensial dan data sensitif tidak ditampilkan dalam log atau pesan kesalahan publik.

## 8. Persyaratan Nonfungsional

### NFR-01 — Responsivitas
Antarmuka harus dapat digunakan pada ponsel, tablet, dan desktop tanpa kehilangan fungsi utama.

### NFR-02 — Kinerja
Halaman utama dan katalog harus dirancang agar ringan, mengoptimalkan ukuran gambar, serta menggunakan pagination atau query yang efisien ketika data bertambah.

### NFR-03 — Kemudahan Penggunaan
Navigasi, tombol aksi, label formulir, dan pesan status harus menggunakan Bahasa Indonesia yang jelas dan konsisten.

### NFR-04 — Keamanan
Autentikasi, otorisasi, validasi server, hashing kata sandi, serta perlindungan terhadap input berbahaya harus diterapkan.

### NFR-05 — Pemeliharaan
Kode harus dipisahkan secara terstruktur, misalnya antara tampilan, logika aplikasi, aset CSS/JavaScript, dan akses basis data.

### NFR-06 — Aksesibilitas Dasar
Teks harus mudah dibaca, kontras warna memadai, gambar memiliki teks alternatif jika relevan, dan kontrol formulir memiliki label.

### NFR-07 — Ketersediaan Informasi
Sistem harus menampilkan keadaan loading, data kosong, kesalahan, dan keberhasilan dengan jelas.

## 9. Struktur Halaman

### Area Publik
1. **Beranda** — pengenalan layanan, pencarian, kategori, dan pilihan UMKM.
2. **Katalog UMKM** — daftar, pencarian, dan filter.
3. **Detail UMKM** — informasi usaha dan kontak.
4. **Login** — masuk ke akun.
5. **Registrasi Pemilik** — pembuatan akun.
6. **Ajukan Usaha** — formulir pengajuan untuk pengguna yang sudah login.

### Area Pemilik Usaha
1. **Dashboard** — ringkasan usaha dan status.
2. **Data Usaha** — daftar usaha milik pemilik.
3. **Tambah/Edit Usaha** — formulir profil.
4. **Detail Pengajuan** — status dan catatan moderasi.
5. **Profil Akun** — informasi akun dasar.

### Area Admin
1. **Dashboard Admin** — ringkasan dan pengajuan terbaru.
2. **Manajemen Usaha** — daftar dan detail usaha.
3. **Moderasi Pengajuan** — persetujuan atau penolakan.
4. **Manajemen Kategori** — tambah, edit, dan nonaktifkan kategori.
5. **Manajemen Pengguna** — melihat akun dan mengelola status sesuai kebutuhan.

## 10. Model Data Awal

Model berikut merupakan rancangan awal dan dapat disesuaikan ketika desain basis data final dibuat.

### 10.1 `users`
- `id` — primary key.
- `name` — nama pengguna.
- `email` — email unik.
- `password_hash` — kata sandi yang sudah di-hash.
- `role` — pemilik atau admin.
- `status` — status akun.
- `created_at`, `updated_at`.

### 10.2 `categories`
- `id` — primary key.
- `name` — nama kategori.
- `slug` — identitas URL unik.
- `description` — deskripsi opsional.
- `status` — aktif atau nonaktif.
- `created_at`, `updated_at`.

### 10.3 `businesses`
- `id` — primary key.
- `owner_id` — foreign key ke `users.id`.
- `category_id` — foreign key ke `categories.id`.
- `name` — nama usaha.
- `slug` — identitas URL.
- `description` — deskripsi usaha.
- `whatsapp_number` — nomor WhatsApp jika tersedia.
- `address` — alamat usaha.
- `operating_hours` — jam operasional, dapat disimpan sebagai teks terstruktur atau tabel terpisah.
- `image_path` — lokasi file gambar utama jika tersedia.
- `status` — status moderasi dan publikasi.
- `moderation_note` — catatan admin jika ada.
- `reviewed_by` — admin yang meninjau jika ada.
- `reviewed_at` — waktu peninjauan jika ada.
- `created_at`, `updated_at`.

### 10.4 Relasi
- Satu pengguna pemilik dapat memiliki satu atau lebih usaha, sesuai kebijakan produk.
- Satu kategori dapat digunakan oleh banyak usaha.
- Satu admin dapat meninjau banyak pengajuan.
- Data usaha menyimpan pemilik dan kategori melalui foreign key.

## 11. Aturan Bisnis

1. Usaha hanya boleh dipublikasikan setelah disetujui admin.
2. Usaha yang dinonaktifkan tidak muncul pada katalog publik.
3. Pemilik hanya boleh mengubah usaha yang menjadi miliknya.
4. Admin memiliki kewenangan moderasi sesuai role.
5. Nomor WhatsApp harus divalidasi sebelum tombol kontak ditampilkan.
6. Data yang tidak tersedia tidak boleh digantikan dengan informasi rekaan.
7. Kategori nonaktif tidak dapat dipilih untuk pengajuan baru, tetapi data usaha lama perlu ditangani secara konsisten.
8. Penghapusan data perlu mempertimbangkan keterkaitan dengan akun, catatan moderasi, dan kebutuhan audit.

## 12. Kondisi Tepi dan Penanganan Kegagalan

- **Katalog kosong:** tampilkan penjelasan dan ajakan mendaftarkan usaha.
- **Pencarian tidak menemukan hasil:** tampilkan pesan “Usaha tidak ditemukan” dan opsi reset filter.
- **Gambar gagal dimuat:** tampilkan gambar pengganti yang konsisten.
- **Nomor WhatsApp tidak tersedia:** sembunyikan tombol WhatsApp dan tampilkan informasi kontak lain jika ada.
- **Formulir tidak lengkap:** tandai kolom yang perlu diperbaiki tanpa menghapus input yang sudah diisi.
- **Unggahan file tidak valid:** tolak file dan jelaskan format atau ukuran yang diperbolehkan.
- **Sesi login berakhir:** minta pengguna login kembali dan hindari kehilangan data formulir jika memungkinkan.
- **Akses tanpa izin:** tampilkan halaman akses ditolak atau arahkan ke login.
- **Pengajuan ditolak:** tampilkan status dan catatan alasan penolakan.
- **Usaha dinonaktifkan setelah dibuka:** halaman publik harus menampilkan status tidak tersedia atau mengarahkan kembali ke katalog.
- **Gangguan basis data:** tampilkan pesan yang ramah tanpa membocorkan detail teknis.
- **Klik tombol kontak:** buka tautan WhatsApp melalui aplikasi atau browser yang sesuai jika tersedia.

## 13. Metrik Keberhasilan

Metrik awal yang dapat dipantau:
- Jumlah UMKM yang terdaftar.
- Jumlah UMKM yang disetujui dan aktif.
- Persentase pengajuan yang lengkap.
- Waktu rata-rata dari pengajuan hingga keputusan moderasi.
- Jumlah kunjungan ke halaman detail usaha.
- Jumlah klik tombol WhatsApp atau kontak.
- Persentase pengunjung katalog yang membuka detail usaha.
- Jumlah pemilik yang kembali memperbarui data.
- Tingkat kesalahan formulir dan kegagalan sistem.

Target numerik belum ditetapkan dan perlu ditentukan setelah baseline penggunaan tersedia.

## 14. Prinsip UI/UX dan Desain

- Gunakan tampilan modern, rapi, ramah, dan mudah dipahami oleh masyarakat umum.
- Prioritaskan pengalaman mobile-first karena pengunjung dapat mengakses melalui ponsel.
- Gunakan navigasi yang konsisten pada halaman publik, dashboard pemilik, dan dashboard admin.
- Tampilkan pencarian dengan jelas pada beranda dan katalog.
- Gunakan kartu usaha dengan hierarki informasi yang mudah dipindai.
- Bedakan tombol aksi utama dan sekunder secara visual.
- Gunakan status yang mudah dipahami: Menunggu Moderasi, Disetujui, Ditolak, dan Nonaktif.
- Sediakan tampilan empty state, loading state, success state, dan error state.
- Pastikan desain dapat diimplementasikan menggunakan HTML, CSS, Bootstrap, JavaScript, PHP, dan MySQL bila stack tersebut dipilih.

## 15. Teknologi dan Implementasi

Pilihan implementasi awal yang sesuai untuk MVP:
- **Frontend:** HTML, CSS, Bootstrap, dan JavaScript.
- **Backend:** PHP.
- **Database:** MySQL/MariaDB.
- **Lingkungan pengembangan lokal:** XAMPP.
- **Kontak penjual:** tautan WhatsApp biasa, tanpa WhatsApp Business API.

Pilihan teknologi ini merupakan rekomendasi implementasi awal, bukan persyaratan mutlak. Struktur kode perlu menjaga pemisahan antara tampilan, logika bisnis, dan akses basis data.

Desain dari Google Stitch adalah acuan UI/UX; hasil desain tetap perlu diimplementasikan menjadi halaman web fungsional dan dihubungkan ke backend serta basis data.

## 16. Keamanan, Privasi, dan Moderasi Konten

- Batasi akses berdasarkan role pengguna.
- Terapkan validasi input di sisi server.
- Simpan kata sandi dalam bentuk hash, bukan teks biasa.
- Batasi jenis, ukuran, dan nama file yang dapat diunggah.
- Hindari menampilkan email atau data pribadi pemilik kecuali memang diperlukan.
- Berikan informasi kepada pemilik mengenai data usaha yang ditampilkan secara publik.
- Sediakan mekanisme pelaporan atau penonaktifan usaha bermasalah sebagai pertimbangan pengembangan berikutnya.
- Cadangkan basis data secara berkala sesuai kemampuan operasional.
- Catat tindakan admin yang penting jika memungkinkan.

## 17. Kriteria Penerimaan MVP

MVP dapat dianggap siap diuji apabila:
1. Pengunjung dapat membuka beranda dan katalog tanpa login.
2. Pengunjung dapat mencari dan memfilter usaha.
3. Pengunjung dapat membuka detail usaha yang telah disetujui.
4. Tombol WhatsApp bekerja ketika nomor tersedia dan valid.
5. Pemilik dapat mendaftar, login, dan logout.
6. Pemilik dapat mengajukan usaha dan melihat statusnya.
7. Pemilik tidak dapat mengubah data milik pengguna lain.
8. Admin dapat melihat pengajuan serta menyetujui atau menolaknya.
9. Usaha yang belum disetujui atau nonaktif tidak muncul di katalog publik.
10. Formulir memvalidasi kolom wajib dan file gambar.
11. Halaman utama dan dashboard dapat digunakan pada ukuran layar ponsel maupun desktop.
12. Kondisi kosong dan kesalahan umum ditampilkan dengan pesan yang dapat dipahami.
13. Akses halaman privat dan tindakan sensitif dilindungi autentikasi serta otorisasi.

## 18. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Data UMKM tidak lengkap | Informasi sulit digunakan | Jadikan kolom penting wajib dan berikan panduan pengisian |
| Data usaha kedaluwarsa | Pengunjung mendapat informasi keliru | Sediakan fitur edit dan tanggal pembaruan |
| Pengajuan berisi spam | Kualitas direktori menurun | Terapkan moderasi admin dan validasi formulir |
| Pemilik lupa memperbarui informasi | Kontak atau jam operasional tidak akurat | Pertimbangkan pengingat dan peninjauan berkala |
| Penyalahgunaan akun | Perubahan data tanpa izin | Terapkan autentikasi, otorisasi, dan pengamanan sesi |
| Jumlah data meningkat | Katalog melambat | Gunakan pagination, indeks basis data, dan optimasi gambar |

## 19. Pertanyaan Terbuka

Hal-hal berikut perlu dikonfirmasi sebelum implementasi final:
1. Apakah wilayah cakupan MVP hanya Kelurahan Langkapura atau juga area sekitar?
2. Apakah setiap pemilik boleh mendaftarkan lebih dari satu usaha?
3. Apakah pendaftaran pemilik langsung aktif atau perlu verifikasi admin?
4. Apakah lokasi cukup berupa alamat teks atau harus memakai peta?
5. Apakah pemilik dapat langsung memublikasikan perubahan kecil, atau semua perubahan harus dimoderasi?
6. Berapa batas ukuran dan format gambar yang diperbolehkan?
7. Apakah admin membutuhkan fitur ekspor laporan pada MVP?
8. Siapa yang bertanggung jawab memperbarui data dan menangani laporan usaha bermasalah?

## 20. Rencana Rilis

### Tahap 1 — Fondasi
- Menyiapkan struktur proyek, database, autentikasi, role, dan layout dasar.
- Membuat navigasi publik dan dashboard.

### Tahap 2 — Direktori Publik
- Membuat katalog, pencarian, filter, detail usaha, dan tautan WhatsApp.
- Menyiapkan kondisi kosong dan responsivitas.

### Tahap 3 — Dashboard Pemilik
- Membuat formulir pengajuan, edit data, unggah gambar, dan status pengajuan.

### Tahap 4 — Dashboard Admin
- Membuat moderasi usaha, manajemen kategori, serta pengelolaan status.

### Tahap 5 — Pengujian dan Peluncuran
- Menguji hak akses, validasi, alur pengajuan, pencarian, tampilan mobile, dan penanganan error.
- Memperbaiki masalah sebelum dipublikasikan.

## 21. Ringkasan

LangkapuraLokal adalah direktori digital UMKM untuk wilayah Langkapura yang berfokus pada pencarian usaha, informasi usaha, dan kontak langsung ke penjual. MVP mencakup area publik, dashboard pemilik, dan dashboard admin dengan moderasi sebelum publikasi. Checkout, pembayaran, chat internal, kurir, rating/ulasan, aplikasi native, langganan, dan integrasi WhatsApp Business API berada di luar ruang lingkup MVP.

Keputusan yang belum pasti dicantumkan sebagai pertanyaan terbuka agar tidak dianggap sebagai kebutuhan final sebelum dikonfirmasi.

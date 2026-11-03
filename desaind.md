# Prompt Google Stitch — LangkapuraLokal

Dokumen ini berisi prompt untuk merancang UI/UX **LangkapuraLokal**, platform direktori UMKM berbasis web untuk Kecamatan Langkapura, Bandar Lampung.

> **Catatan:** Google Stitch menghasilkan rancangan UI. Implementasi fungsi seperti login, database, pencarian dinamis, moderasi, dan tautan WhatsApp tetap perlu dikembangkan di PHP, HTML, CSS/Bootstrap, dan MySQL. Gunakan data simulasi selama tahap prototipe.

## 1. Prompt Utama — Desain Seluruh Platform

Salin prompt berikut ke Google Stitch:

```text
Bertindaklah sebagai Senior UI/UX Designer dan Product Designer. Rancang UI/UX web responsif bernama “LangkapuraLokal”, yaitu direktori digital UMKM untuk Kecamatan Langkapura, Kota Bandar Lampung, Indonesia.

TUJUAN PRODUK
Membantu warga menemukan usaha lokal dan membantu pemilik UMKM mempromosikan usahanya secara digital. Pengunjung dapat mencari usaha, melihat detail, lalu menghubungi penjual langsung melalui WhatsApp. Tidak ada checkout atau pembayaran di dalam aplikasi.

TARGET PENGGUNA
1. Pengunjung/warga yang mencari kuliner, toko, dan jasa lokal.
2. Pemilik UMKM yang ingin mendaftarkan dan mengelola profil usaha.
3. Admin yang memoderasi data usaha dan mengelola kategori.

GAYA VISUAL
- Modern, ramah, terpercaya, sederhana, dan terasa lokal.
- Mobile-first dan responsif untuk ponsel, tablet, serta desktop.
- Warna utama hijau #176B45; hijau aksen #2E8B57; latar putih dan abu-abu kehijauan yang lembut.
- Gunakan Inter untuk teks isi dan Plus Jakarta Sans untuk judul.
- Kartu dengan sudut membulat, jarak antarelemen yang konsisten, ikon jelas, bayangan tipis, dan kontras teks yang baik.
- Gunakan Bahasa Indonesia pada seluruh antarmuka.
- Hindari tampilan yang terlalu ramai atau terlalu mirip marketplace nasional yang kompleks.

HALAMAN YANG HARUS DIRANCANG
A. Beranda publik:
- Header berisi logo/nama LangkapuraLokal, Beranda, Jelajahi Usaha, Kategori, Masuk Pemilik Usaha, dan tombol Daftarkan Usaha.
- Hero dengan judul “Temukan Usaha Lokal di Sekitarmu”.
- Deskripsi singkat tentang direktori UMKM Langkapura.
- Kolom pencarian usaha dan filter kelurahan.
- Kata kunci populer.
- Grid kategori usaha.
- Kartu usaha pilihan yang menampilkan foto, nama, kategori, lokasi, deskripsi singkat, dan tombol Lihat Detail.
- Bagian cara kerja: cari usaha, lihat detail, hubungi via WhatsApp.
- Ajakan untuk pemilik UMKM agar mendaftarkan usaha.
- Footer dengan navigasi dan informasi bantuan.
- Tampilkan banner bahwa data prototipe adalah data simulasi/dummy.

B. Katalog/Jelajahi Usaha:
- Judul dan deskripsi halaman.
- Kolom pencarian.
- Filter kategori dan kelurahan.
- Pengurutan yang sederhana, misalnya terbaru atau nama usaha.
- Grid kartu usaha yang konsisten dengan beranda.
- Status kosong ketika tidak ada hasil.
- Navigasi halaman atau tombol muat lebih banyak jika diperlukan.

C. Detail Usaha:
- Foto utama dan galeri foto.
- Nama usaha, kategori, alamat/kelurahan, dan status buka jika tersedia.
- Deskripsi usaha.
- Daftar produk/jasa beserta harga bila tersedia.
- Jam operasional dan informasi kontak.
- Tombol utama “Hubungi via WhatsApp”.
- Tautan lokasi/peta jika tersedia.
- Tampilkan informasi yang belum tersedia secara wajar, jangan membuat data palsu terlihat sebagai fakta.

D. Dashboard Pemilik Usaha:
- Sidebar atau navigasi dashboard yang mudah digunakan.
- Ringkasan status profil usaha.
- Form untuk menambah dan mengubah nama usaha, kategori, deskripsi, alamat, kelurahan, nomor WhatsApp, jam operasional, foto, serta produk/jasa.
- Daftar usaha milik pengguna dan status moderasi: Menunggu, Disetujui, atau Perlu Perbaikan.
- Pesan konfirmasi, validasi form, loading, dan error state.
- Berikan petunjuk yang jelas bagi pengguna nonteknis.

E. Dashboard Admin:
- Ringkasan jumlah usaha, usaha menunggu moderasi, usaha disetujui, dan kategori.
- Tabel daftar usaha dengan pencarian dan filter status.
- Detail untuk memeriksa informasi usaha.
- Aksi setujui, tolak/perlu perbaikan, dan nonaktifkan usaha.
- Pengelolaan kategori.
- Konfirmasi sebelum tindakan penting dan notifikasi hasil tindakan.
- Tampilan tabel harus tetap mudah digunakan pada layar kecil.

PRINSIP PENGALAMAN PENGGUNA
- Prioritaskan alur utama: pengunjung menemukan usaha → membuka detail → menghubungi penjual lewat WhatsApp.
- Buat tombol aksi utama terlihat jelas.
- Sediakan state untuk loading, kosong, gagal, sukses, dan validasi form.
- Gunakan data contoh yang jelas sebagai data dummy.
- Jangan menambahkan fitur di luar cakupan MVP.

BATASAN MVP
Jangan merancang checkout internal, pembayaran QRIS/e-wallet, chat internal, layanan kurir, sistem rating/review, aplikasi native, langganan berbayar, atau integrasi WhatsApp Business API. Kontak dilakukan melalui tautan WhatsApp biasa.

HASIL YANG DIHARAPKAN
Buat desain visual yang konsisten untuk semua halaman di atas, lengkap dengan komponen berulang, tata letak responsif, hierarki visual yang jelas, dan navigasi yang masuk akal. Pastikan desain realistis untuk diimplementasikan oleh satu pengembang dengan kemampuan dasar PHP dan MySQL dalam waktu sekitar tiga bulan.
```

## 2. Prompt Beranda

Gunakan setelah membuat desain utama, jika ingin menyempurnakan halaman beranda:

```text
Sempurnakan halaman Beranda LangkapuraLokal. Pertahankan sistem desain hijau, putih, dan abu-abu kehijauan; font Inter dan Plus Jakarta Sans; kartu membulat; dan tampilan responsif.

Fokuskan halaman pada pencarian usaha lokal. Tampilkan header yang ringkas, hero “Temukan Usaha Lokal di Sekitarmu”, pencarian usaha, filter kelurahan, kata kunci populer, kategori usaha, usaha pilihan, cara kerja platform, ajakan mendaftarkan usaha, dan footer.

Pastikan CTA “Cari Usaha” dan “Daftarkan Usaha” mudah ditemukan. Sertakan banner bahwa data yang ditampilkan merupakan data simulasi untuk prototipe. Jangan tampilkan statistik atau klaim dukungan lembaga sebagai fakta nyata tanpa data yang telah diverifikasi.
```

## 3. Prompt Katalog Usaha

```text
Buat halaman “Jelajahi Usaha” untuk LangkapuraLokal dengan gaya visual yang sama dengan halaman Beranda.

Tampilkan judul halaman, deskripsi singkat, kolom pencarian, filter kategori, filter kelurahan, opsi pengurutan, dan grid kartu usaha. Setiap kartu berisi foto, nama usaha, kategori, lokasi, ringkasan, dan tombol “Lihat Detail”.

Rancang state untuk hasil ditemukan, tidak ada hasil, loading, dan kesalahan pemuatan. Pada layar ponsel, filter harus tetap mudah dibuka dan digunakan. Gunakan data dummy yang ditandai sebagai contoh dan jangan menampilkan rating/review sebagai fitur inti.
```

## 4. Prompt Detail Usaha

```text
Buat halaman Detail Usaha LangkapuraLokal yang konsisten dengan sistem desain sebelumnya.

Tampilkan foto utama, galeri foto, nama usaha, kategori, alamat/kelurahan, deskripsi, daftar produk atau jasa dan harga jika tersedia, jam operasional, serta informasi kontak. Tempatkan tombol utama “Hubungi via WhatsApp” secara menonjol. Sediakan tautan lokasi/peta hanya jika data lokasinya tersedia.

Pastikan halaman nyaman dibaca di ponsel, informasi tersusun dengan jelas, dan tidak ada data yang tidak diketahui ditampilkan sebagai fakta. Berikan tampilan fallback untuk foto atau informasi yang belum tersedia.
```

## 5. Prompt Dashboard Pemilik Usaha

```text
Buat dashboard pemilik UMKM untuk LangkapuraLokal dengan antarmuka sederhana bagi pengguna nonteknis.

Tampilkan ringkasan profil, status moderasi, dan formulir untuk membuat atau mengubah profil usaha: nama, kategori, deskripsi, alamat, kelurahan, nomor WhatsApp, jam operasional, foto, serta produk/jasa. Sertakan daftar usaha yang dimiliki pengguna dengan status Menunggu, Disetujui, atau Perlu Perbaikan.

Sediakan validasi form, pesan sukses/gagal, state loading, konfirmasi sebelum meninggalkan formulir yang belum disimpan, dan petunjuk singkat. Gunakan navigasi dashboard yang konsisten dengan desain LangkapuraLokal dan responsif untuk ponsel.
```

## 6. Prompt Dashboard Admin

```text
Buat dashboard Admin LangkapuraLokal untuk mengelola direktori UMKM.

Tampilkan kartu ringkasan jumlah usaha, usaha menunggu moderasi, usaha disetujui, dan jumlah kategori. Buat tabel daftar usaha dengan pencarian, filter kategori/status, dan informasi penting seperti nama usaha, pemilik, kelurahan, tanggal pengajuan, dan status.

Sediakan alur untuk melihat detail, menyetujui, meminta perbaikan/menolak, serta menonaktifkan usaha. Tambahkan pengelolaan kategori, dialog konfirmasi untuk tindakan penting, dan notifikasi setelah tindakan. Rancang tampilan tabel yang tetap mudah digunakan pada layar kecil. Gunakan hanya data simulasi dan hindari klaim statistik nyata.
```

## 7. Prompt Penyelarasan Desain

```text
Tinjau semua halaman LangkapuraLokal yang telah dibuat dan selaraskan menjadi satu sistem desain yang konsisten.

Samakan warna, tipografi, ukuran judul, radius kartu, bayangan, jarak, ikon, tombol, field formulir, navigasi, dan gaya pesan notifikasi. Pastikan label dan istilah berbahasa Indonesia konsisten, header/footer publik seragam, serta dashboard pemilik dan admin menggunakan pola komponen yang sama.

Periksa tampilan desktop dan mobile. Perbaiki masalah keterbacaan, kontras, hierarki visual, overflow, dan tombol yang terlalu kecil. Jangan menambah fitur baru di luar cakupan MVP.
```

## 8. Urutan Penggunaan di Google Stitch

1. Masukkan **Prompt Utama** untuk membuat gambaran keseluruhan.
2. Sempurnakan **Beranda**.
3. Buat/sesuaikan **Katalog Usaha**.
4. Buat **Detail Usaha**.
5. Buat **Dashboard Pemilik Usaha**.
6. Buat **Dashboard Admin**.
7. Jalankan **Prompt Penyelarasan Desain** agar seluruh halaman konsisten.

## 9. Catatan Implementasi

Desain dari Stitch merupakan referensi UI/UX, bukan aplikasi backend yang langsung siap produksi. Untuk MVP, implementasikan secara bertahap:

- Frontend: HTML, CSS/Bootstrap, dan JavaScript seperlunya.
- Backend: PHP.
- Database: MySQL.
- Modul awal: katalog publik, pencarian/filter, detail usaha, tautan WhatsApp, login pemilik, formulir pengelolaan profil, dan moderasi admin.
- Keamanan minimum: validasi input di server, pengamanan sesi/login, pembatasan akses berdasarkan peran, validasi unggahan foto, serta query database yang aman.
- Uji dengan beberapa pemilik UMKM terlebih dahulu sebelum meluncurkan direktori secara lebih luas.

**Alur utama produk:** Pengunjung mencari usaha → melihat detail → menghubungi pemilik melalui WhatsApp.

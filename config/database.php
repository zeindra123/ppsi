<?php
/**
 * LangkapuraLokal - Konfigurasi Database
 * Mendukung SQLite (otomatis tanpa konfigurasi) dan MySQL / MariaDB (XAMPP).
 */

// Konfigurasi MySQL default (jika ingin digunakan di XAMPP)
define('DB_DRIVER', 'sqlite'); // Ubah ke 'mysql' jika MySQL XAMPP sudah berjalan
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'langkapura_lokal');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $driver = DB_DRIVER;
    $dataDir = __DIR__ . '/../data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0777, true);
    }
    $sqliteFile = $dataDir . '/langkapura.sqlite';

    // Coba koneksi MySQL jika diatur ke mysql, atau fallback ke SQLite jika gagal/default sqlite
    if ($driver === 'mysql') {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (Exception $e) {
            // Fallback ke SQLite jika koneksi MySQL gagal agar web tetap dapat dijalankan live
            $driver = 'sqlite';
        }
    }

    // Driver SQLite (Portable & Zero-Config)
    $dsn = "sqlite:" . $sqliteFile;
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Aktifkan foreign key di SQLite
    $pdo->exec("PRAGMA foreign_keys = ON;");

    // Inisialisasi tabel jika belum ada
    initDatabase($pdo);

    return $pdo;
}

function initDatabase($pdo) {
    // 1. Tabel users
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        phone TEXT,
        password_hash TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'owner', -- 'admin' atau 'owner'
        status TEXT NOT NULL DEFAULT 'active', -- 'active' atau 'inactive'
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // 2. Tabel categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        icon TEXT DEFAULT 'bi-tag',
        description TEXT,
        status TEXT NOT NULL DEFAULT 'active', -- 'active' atau 'inactive'
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    // 3. Tabel businesses
    $pdo->exec("CREATE TABLE IF NOT EXISTS businesses (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        owner_id INTEGER NOT NULL,
        category_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        slug TEXT NOT NULL,
        description TEXT NOT NULL,
        whatsapp_number TEXT NOT NULL,
        kelurahan TEXT NOT NULL DEFAULT 'Langkapura',
        address TEXT NOT NULL,
        operating_hours TEXT,
        image_path TEXT,
        maps_url TEXT,
        products_list TEXT,
        status TEXT NOT NULL DEFAULT 'pending', -- 'draft', 'pending', 'approved', 'rejected', 'inactive'
        moderation_note TEXT,
        reviewed_by INTEGER,
        reviewed_at DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );");

    // Cek apakah data awal (seed) sudah ada
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM users");
    $userCount = $stmt->fetch()['cnt'];

    if ($userCount == 0) {
        seedInitialData($pdo);
    }
}

function seedInitialData($pdo) {
    // Password default hash: 'admin123' dan 'pemilik123'
    $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
    $ownerPass = password_hash('pemilik123', PASSWORD_BCRYPT);

    // Seed Users
    $stmtUser = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtUser->execute(['Admin LangkapuraLokal', 'admin@langkapuralokal.id', '081234567890', $adminPass, 'admin', 'active']);
    $stmtUser->execute(['Budi Santoso (Pemilik UMKM)', 'budi@umkm.id', '082188997711', $ownerPass, 'owner', 'active']);
    $stmtUser->execute(['Siti Aminah (Pemilik UMKM)', 'siti@umkm.id', '081377665544', $ownerPass, 'owner', 'active']);

    // Seed Categories
    $categories = [
        ['Kuliner & Jajanan', 'kuliner-jajanan', 'bi-cup-hot', 'Makanan berat, olahan khas, jajanan tradisional, dan aneka minuman.'],
        ['Toko Kelontong & Sembako', 'kelontong-sembako', 'bi-cart4', 'Kebutuhan pokok sehari-hari, beras, minyak, bumbu dapur, dan toko serba ada.'],
        ['Jasa & Servis', 'jasa-servis', 'bi-tools', 'Bengkel kendaraan, jasa perbaikan elektronik, laundry, dan servis rumah tangga.'],
        ['Fashion & Konveksi', 'fashion-konveksi', 'bi-scissors', 'Jasa jahit pakaian, bordir, busana muslim, seragam, dan tailor lokal.'],
        ['Kerajinan & Tanaman', 'kerajinan-tanaman', 'bi-flower1', 'Tanaman hias, bibit tanaman, kerajinan tangan, dan suvenir lokal.'],
        ['Kesehatan & Kecantikan', 'kesehatan-kecantikan', 'bi-heart-pulse', 'Pangkas rambut, barbershop, salon rumahan, dan produk herbal alami.'],
    ];

    $stmtCat = $pdo->prepare("INSERT INTO categories (name, slug, icon, description, status) VALUES (?, ?, ?, ?, 'active')");
    foreach ($categories as $cat) {
        $stmtCat->execute($cat);
    }

    // Seed Businesses
    // Kelurahan di Kecamatan Langkapura: Langkapura, Langkapura Baru, Gunung Terang, Bilabong Jaya
    $businesses = [
        [
            'owner_id' => 2,
            'category_id' => 1,
            'name' => 'Keripik Pisang Berkah Langkapura',
            'slug' => 'keripik-pisang-berkah-langkapura',
            'description' => 'Produsen keripik pisang kepok khas Lampung dengan berbagai varian rasa premium seperti coklat leleh, keju manis, mocca, balado, dan original gurih. Diproduksi higienis tanpa bahan pengawet.',
            'whatsapp_number' => '6282188997711',
            'kelurahan' => 'Langkapura',
            'address' => 'Jl. Imam Bonjol No. 45, RT 02/LK I, Kel. Langkapura',
            'operating_hours' => 'Senin - Sabtu: 08.00 - 18.00 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bd087?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Langkapura+Bandar+Lampung',
            'products_list' => "Keripik Pisang Coklat Lumer (250gr) - Rp 18.000\nKeripik Pisang Keju Manis (250gr) - Rp 18.000\nKeripik Pisang Original Renyah (250gr) - Rp 15.000\nPaket Oleh-oleh 4 Rasa - Rp 65.000",
            'status' => 'approved',
            'moderation_note' => 'Data usaha telah lengkap dan sesuai verifikasi wilayah Langkapura.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'owner_id' => 2,
            'category_id' => 1,
            'name' => 'Kopi Robusta Bukit Langkapura',
            'slug' => 'kopi-robusta-bukit-langkapura',
            'description' => 'Roastery rumahan yang mengolah biji kopi robusta pilihan dari dataran tinggi Lampung. Tersedia bubuk halus, kasar, dan whole beans roast profile medium-dark.',
            'whatsapp_number' => '6282188997711',
            'kelurahan' => 'Langkapura Baru',
            'address' => 'Jl. Pangeran Tirtayasa Gg. Mawar No. 12, Kel. Langkapura Baru',
            'operating_hours' => 'Setiap Hari: 09.00 - 21.00 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Langkapura+Baru+Bandar+Lampung',
            'products_list' => "Kopi Robusta Bubuk Murni 200gr - Rp 25.000\nKopi Robusta Premium Roast Bean 250gr - Rp 35.000\nCold Brew Robusta Botol 250ml - Rp 15.000",
            'status' => 'approved',
            'moderation_note' => 'Disetujui. Kontak WhatsApp dan alamat terverifikasi.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-1 days'))
        ],
        [
            'owner_id' => 3,
            'category_id' => 2,
            'name' => 'Toko Kelontong Berkah Siti',
            'slug' => 'toko-kelontong-berkah-siti',
            'description' => 'Menyediakan sembako lengkap (beras lokal kualitas super, telur, minyak goreng, gula pasir, bumbu dapur segar, tabung gas LPG 3kg, dan air galon isi ulang). Melayani pesan antar untuk warga sekitar perumahan.',
            'whatsapp_number' => '6281377665544',
            'kelurahan' => 'Bilabong Jaya',
            'address' => 'Perumahan Bilabong Jaya Blok C No. 18, Kel. Bilabong Jaya',
            'operating_hours' => 'Senin - Minggu: 06.30 - 21.30 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Bilabong+Jaya+Langkapura',
            'products_list' => "Beras Raja Udang 5kg - Rp 72.000\nMinyak Goreng Kita 2L - Rp 34.000\nTelur Ayam Negeri 1kg - Rp 28.000\nIsi Ulang Gas LPG 3kg - Rp 20.000",
            'status' => 'approved',
            'moderation_note' => 'Disetujui untuk direktori publik.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
        ],
        [
            'owner_id' => 3,
            'category_id' => 3,
            'name' => 'Bengkel Motor Langkapura Presisi',
            'slug' => 'bengkel-motor-langkapura-presisi',
            'description' => 'Melayani servis rutin motor matic dan manual, tune up injeksi, ganti oli original, tambal ban tubeless modern, kelistrikan, serta suku cadang original dengan harga bersahabat.',
            'whatsapp_number' => '6281377665544',
            'kelurahan' => 'Gunung Terang',
            'address' => 'Jl. Kulit No. 7, Dekat Lapangan Gunung Terang',
            'operating_hours' => 'Senin - Sabtu: 08.30 - 17.00 WIB (Minggu Libur)',
            'image_path' => 'https://images.unsplash.com/photo-1486006920555-c77dce18193b?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Gunung+Terang+Langkapura',
            'products_list' => "Servis Ringan + Tune Up Matic - Rp 45.000\nPaket Ganti Oli MPX2 + Servis - Rp 85.000\nTambal Ban Tubeless Tip-Top - Rp 15.000\nPembersihan Injektor Ultrasonik - Rp 35.000",
            'status' => 'approved',
            'moderation_note' => 'Disetujui.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ],
        [
            'owner_id' => 2,
            'category_id' => 4,
            'name' => 'Rumah Jahit & Bordir Anggun',
            'slug' => 'rumah-jahit-dan-bordir-anggun',
            'description' => 'Menerima pesanan jahit baju wanita, gamis, kebaya modern, kemeja pria, seragam sekolah, seragam pengajian, permak celana levis, dan bordir nama digital cepat.',
            'whatsapp_number' => '6282188997711',
            'kelurahan' => 'Langkapura',
            'address' => 'Jl. Danau Ranau Gg. Kencana No. 04, Kel. Langkapura',
            'operating_hours' => 'Senin - Sabtu: 09.00 - 17.00 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1528459801416-a9e53bbf4e17?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Langkapura+Bandar+Lampung',
            'products_list' => "Jahit Gamis Polos / Kombinasi - Mulai Rp 85.000\nJahit Kemeja Batik Berfuring - Mulai Rp 110.000\nPermak Potong / Kecilkan Celana - Rp 15.000\nBordir Komputer Nama / Logo - Mulai Rp 10.000",
            'status' => 'approved',
            'moderation_note' => 'Disetujui untuk direktori publik.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ],
        [
            'owner_id' => 3,
            'category_id' => 5,
            'name' => 'Kebun Bibit & Tanaman Hias Bilabong',
            'slug' => 'kebun-bibit-dan-tanaman-hias-bilabong',
            'description' => 'Menjual aneka tanaman hias indoor & outdoor (Aglonema, Monstera, Sansevieria), bibit buah unggul (Alpukat Siger Lampung, Mangga Kiojay, Jeruk Santang), pupuk kompos organik, dan pot terakota.',
            'whatsapp_number' => '6281377665544',
            'kelurahan' => 'Bilabong Jaya',
            'address' => 'Jl. Teratai Raya Dekat Gerbang Bilabong Jaya',
            'operating_hours' => 'Setiap Hari: 07.30 - 17.30 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1463936575829-25148e1db1b8?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Bilabong+Jaya+Bandar+Lampung',
            'products_list' => "Bibit Alpukat Siger 1 Meter - Rp 45.000\nTanaman Hias Aglonema Suksom - Rp 60.000\nPupuk Kompos Fermentasi 10kg - Rp 20.000\nMedia Tanam Siap Pakai - Rp 12.000",
            'status' => 'approved',
            'moderation_note' => 'Disetujui.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-1 days'))
        ],
        // 1 Usaha status PENDING untuk testing moderasi admin
        [
            'owner_id' => 2,
            'category_id' => 1,
            'name' => 'Dapur Pempek Cek Mey Langkapura',
            'slug' => 'dapur-pempek-cek-mey-langkapura',
            'description' => 'Pempek asli ikan tenggiri segar dengan kuah cuko kental pedas manis asam khas Palembang-Lampung. Tersedia pempek kapal selam, lenjer, adaan, dan kulit krispi.',
            'whatsapp_number' => '6282188997711',
            'kelurahan' => 'Langkapura Baru',
            'address' => 'Jl. Cut Nyak Dien No. 22, Kel. Langkapura Baru',
            'operating_hours' => 'Senin - Minggu: 10.00 - 20.00 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Langkapura+Baru',
            'products_list' => "Paket Pempek Campur (10 pcs) - Rp 35.000\nPempek Kapal Selam Besar - Rp 15.000\nCuko Kental Botol 250ml - Rp 12.000",
            'status' => 'pending',
            'moderation_note' => null,
            'reviewed_by' => null,
            'reviewed_at' => null
        ],
        // 1 Usaha status REJECTED (Perlu Perbaikan) untuk testing pemilik
        [
            'owner_id' => 3,
            'category_id' => 6,
            'name' => 'Barbershop Mas Bro Langkapura',
            'slug' => 'barbershop-mas-bro-langkapura',
            'description' => 'Tempat cukur rambut pria modern dengan ruangan ber-AC nyaman, kapster berpengalaman, fasilitas cuci rambut, pijat relaksasi kepala, dan pomade wangi.',
            'whatsapp_number' => '6281377665544',
            'kelurahan' => 'Gunung Terang',
            'address' => 'Jl. Purnawirawan Raya No. 8, Kel. Gunung Terang',
            'operating_hours' => 'Setiap Hari: 10.00 - 21.00 WIB',
            'image_path' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=800&auto=format&fit=crop&q=80',
            'maps_url' => 'https://maps.google.com/?q=Gunung+Terang+Langkapura',
            'products_list' => "Potong Rambut Dewasa + Cuci - Rp 30.000\nPotong Rambut Anak - Rp 25.000\nCukur Kumis / Jenggot Hot Towel - Rp 15.000",
            'status' => 'rejected',
            'moderation_note' => 'Mohon tambahkan nomor RT/RW yang jelas pada bagian alamat agar pengunjung mudah menemukan lokasi ruko.',
            'reviewed_by' => 1,
            'reviewed_at' => date('Y-m-d H:i:s', strtotime('-1 days'))
        ]
    ];

    $stmtBiz = $pdo->prepare("INSERT INTO businesses (
        owner_id, category_id, name, slug, description, whatsapp_number,
        kelurahan, address, operating_hours, image_path, maps_url, products_list,
        status, moderation_note, reviewed_by, reviewed_at
    ) VALUES (
        :owner_id, :category_id, :name, :slug, :description, :whatsapp_number,
        :kelurahan, :address, :operating_hours, :image_path, :maps_url, :products_list,
        :status, :moderation_note, :reviewed_by, :reviewed_at
    )");

    foreach ($businesses as $biz) {
        $stmtBiz->execute($biz);
    }
}

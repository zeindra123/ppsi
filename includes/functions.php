<?php
/**
 * LangkapuraLokal - Helper Functions & Formatting
 */

function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

function getKelurahanList() {
    return [
        'Langkapura',
        'Langkapura Baru',
        'Gunung Terang',
        'Bilabong Jaya'
    ];
}

function formatWhatsAppNumber($number) {
    // Hapus karakter non-digit
    $cleaned = preg_replace('/[^0-9]/', '', $number ?? '');
    // Jika diawali 08, ganti menjadi 628
    if (strpos($cleaned, '08') === 0) {
        $cleaned = '62' . substr($cleaned, 1);
    }
    // Jika belum ada kode negara dan mulai dengan 8
    elseif (strpos($cleaned, '8') === 0) {
        $cleaned = '62' . $cleaned;
    }
    return $cleaned;
}

function buildWhatsAppUrl($number, $businessName) {
    $cleanNumber = formatWhatsAppNumber($number);
    if (empty($cleanNumber)) {
        return '#';
    }
    $message = "Halo *" . rawurlencode($businessName) . "*, saya menemukan usaha Anda di direktori digital LangkapuraLokal. Saya ingin bertanya mengenai produk/jasa Anda.";
    return "https://wa.me/" . $cleanNumber . "?text=" . $message;
}

function getStatusBadge($status) {
    switch ($status) {
        case 'approved':
            return '<span class="badge badge-status badge-approved"><i class="bi bi-check-circle-fill me-1"></i>Disetujui</span>';
        case 'pending':
            return '<span class="badge badge-status badge-pending"><i class="bi bi-clock-history me-1"></i>Menunggu Moderasi</span>';
        case 'rejected':
            return '<span class="badge badge-status badge-rejected"><i class="bi bi-exclamation-triangle-fill me-1"></i>Perlu Perbaikan</span>';
        case 'inactive':
            return '<span class="badge badge-status badge-inactive"><i class="bi bi-slash-circle me-1"></i>Nonaktif</span>';
        case 'draft':
            return '<span class="badge badge-status badge-draft"><i class="bi bi-pencil me-1"></i>Draft</span>';
        default:
            return '<span class="badge badge-status badge-secondary">' . sanitize(ucfirst($status)) . '</span>';
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'approved': return 'Disetujui';
        case 'pending': return 'Menunggu Moderasi';
        case 'rejected': return 'Perlu Perbaikan';
        case 'inactive': return 'Nonaktif';
        case 'draft': return 'Draft';
        default: return ucfirst($status);
    }
}

function truncateText($text, $length = 120) {
    $text = strip_tags($text ?? '');
    if (mb_strlen($text) > $length) {
        return mb_substr($text, 0, $length) . '...';
    }
    return $text;
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a-' . time();
    }
    return $text;
}

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Hitung path subfolder jika dijalankan di subdirektori (misal XAMPP htdocs/kuis)
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = dirname($scriptName);
    
    // Normalisasi jika di root
    if ($dir === '/' || $dir === '\\') {
        $dir = '';
    }
    
    // Jika saat ini di dalam folder owner/ atau admin/, naik 1 tingkat
    if (basename($dir) === 'owner' || basename($dir) === 'admin') {
        $dir = dirname($dir);
        if ($dir === '/' || $dir === '\\') {
            $dir = '';
        }
    }
    
    return $dir;
}

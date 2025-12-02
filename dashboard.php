<?php
// Pastikan sesi dimulai di awal
session_start();

// --- Konfigurasi dan Fungsi Pembantu ---

/**
 * Format angka menjadi Rupiah (Rp 100.000)
 * @param int $angka
 * @return string
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Menghitung persentase diskon berdasarkan total belanja.
 * @param int $total
 * @return int Persentase diskon (misalnya 10)
 */
function hitungDiskonPersen($total) {
    if ($total <= 0) {
        return 0;
    } elseif ($total < 50000) {
        return 5;
    } elseif ($total <= 100000) {
        return 10;
    } else { // $total > 100000
        return 15;
    }
}

// --- Inisialisasi Input dan Status ---

// Ambil input dan bersihkan/format sesuai kebutuhan
$inputKode = isset($_POST['kode']) ? strtoupper(trim($_POST['kode'])) : '';
$inputNama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
// Hilangkan titik ribuan (jika ada) dari input harga untuk menjaga value field
$inputHarga = isset($_POST['harga']) ? str_replace('.', '', $_POST['harga']) : '';
$inputJumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : '';
$statusMessage = '';

// --- Logika Aksi POST (Tambah/Kosongkan) ---

// Tombol Kosongkan Keranjang
if (isset($_POST['clear'])) {
    unset($_SESSION['cart']);
    $statusMessage = '<span class="success-text">🗑️ Keranjang berhasil dikosongkan.</span>';
}

// Proses Tambah Barang
if (isset($_POST['tambah'])) {
    $kode = strtoupper(trim($_POST['kode']));
    $nama = trim($_POST['nama']);
    // Pastikan harga adalah integer setelah menghapus semua karakter non-digit kecuali tanda minus (jika ada, tapi di sini diasumsikan positif)
    $harga = intval(preg_replace('/[^0-9]/', '', $_POST['harga'])); 
    $jumlah = intval($_POST['jumlah']);

    // Validasi dasar
    if (empty($kode) || empty($nama) || $harga <= 0 || $jumlah <= 0) {
        $statusMessage = '<span class="error-text">⚠️ Error: Harap isi semua kolom dengan nilai yang valid.</span>';
    } else {
        // Data valid, proses penambahan
        $subtotal = $harga * $jumlah;
        $found = false;

        // Cek jika barang sudah ada di keranjang
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['kode'] === $kode) {
                    // Update jumlah dan subtotal
                    $_SESSION['cart'][$key]['jumlah'] += $jumlah;
                    $_SESSION['cart'][$key]['subtotal'] += $subtotal;
                    $found = true;
                    break;
                }
            }
        }
        
        // Jika barang belum ada, tambahkan sebagai item baru
        if (!$found) {
            $_SESSION['cart'][] = [
                "kode" => $kode,
                "nama" => $nama,
                "harga" => $harga,
                "jumlah" => $jumlah,
                "subtotal" => $subtotal
            ];
        }
        
        // Bersihkan input setelah berhasil
        $inputKode = '';
        $inputNama = '';
        $inputHarga = '';
        $inputJumlah = '';
        $statusMessage = '<span class="success-text">✅ Sukses: Barang **' . htmlspecialchars($nama) . '** berhasil ditambahkan.</span>';
    }
}

// --- Kalkulasi Total & Diskon ---
$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['subtotal'];
    }
}

$persenDiskon = hitungDiskonPersen($total);
$diskon = ($persenDiskon / 100) * $total;
$grandTotal = $total - $diskon;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Dashboard Penjualan</title>
    <style>
        /* --- General Setup --- */
        :root {
            --primary-color: #ffc300; /* Kuning Cerah */
            --secondary-color: #333;
            --background-light: #f4f7f6; /* Abu-abu Sangat Terang */
            --background-dark: #ffffff; /* Putih bersih */
            --accent-color: #007bff; /* Biru untuk aksi/informasi */
            --success-color: #28a745;
            --error-color: #dc3545;
            --shadow-subtle: 0 4px 12px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        body { 
            font-family: 'Poppins', sans-serif; /* Font modern */
            background-color: var(--background-light); 
            padding: 0; 
            margin: 0; 
            display: flex; 
            justify-content: center; 
            min-height: 100vh;
        }

        .dashboard-container { 
            width: 95%; 
            max-width: 1300px; /* Lebar ditingkatkan */
            background: var(--background-dark); 
            margin: 40px auto; 
            border-radius: 20px; 
            box-shadow: var(--shadow-medium); 
            display: flex;
            min-height: 85vh;
            overflow: hidden;
        }

        /* --- Sidebar / Input Section --- */
        .sidebar {
            width: 400px; /* Lebar ditingkatkan */
            background: #ffffff;
            padding: 40px 30px;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }

        .header-sidebar {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--primary-color);
        }

        .header-sidebar h1 {
            margin: 0;
            color: var(--secondary-color);
            font-size: 28px;
            font-weight: 800;
        }
        
        .header-sidebar p {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }

        .input-group { 
            margin-bottom: 25px; 
        }

        label { 
            display: block; 
            font-size: 15px; 
            font-weight: 600; 
            color: var(--secondary-color); 
            margin-bottom: 8px; 
        }

        .input-field { 
            width: 100%; 
            padding: 14px; 
            border: 1px solid #ddd; 
            border-radius: 10px; 
            box-sizing: border-box; 
            font-size: 15px; 
            background-color: #fcfcfc;
            transition: border-color 0.3s, box-shadow 0.3s; 
        }

        .input-field:focus { 
            border-color: var(--primary-color); 
            box-shadow: 0 0 0 3px rgba(255, 195, 0, 0.2); 
            outline: none;
        }

        .form-row { 
            display: flex; 
            gap: 20px; 
        }

        .form-row .input-group { 
            flex: 1; 
        }

        .button-row { 
            display: flex; 
            gap: 15px; 
            margin-top: 30px; 
        }
        
        .btn-action { 
            padding: 14px 25px; 
            border-radius: 10px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 16px; 
            transition: background-color 0.3s, transform 0.2s; 
            border: none;
        }
        
        .btn-tambahkan { 
            background: var(--primary-color); 
            color: var(--secondary-color); 
            flex-grow: 1;
        }

        .btn-tambahkan:hover {
            background-color: #e6b300;
            transform: translateY(-2px);
        }
        
        .btn-batal { 
            background: #e9ecef; 
            color: #6c757d; 
            width: 120px;
        }
        
        .btn-batal:hover {
            background-color: #dee2e6;
        }
        
        .status-message {
            margin: 10px 0 20px 0;
            padding: 15px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            background-color: #fff8e1; 
            border-left: 5px solid var(--primary-color);
        }

        .success-text { color: var(--success-color); }
        .error-text { color: var(--error-color); }
        
        /* --- Main Content / Keranjang Section --- */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            background-color: var(--background-dark);
        }

        .header-main {
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            padding-bottom: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .header-main h2 {
            font-size: 22px;
            margin: 0;
            color: var(--secondary-color);
        }

        .user-info {
            text-align: right; 
            font-size: 15px;
            line-height: 1.4;
        }

        .user-info .role { 
            font-size: 12px; 
            color: var(--accent-color); 
            margin-top: 2px;
            font-weight: 600;
        }

        .logout-btn { 
            background: none; 
            border: none; 
            color: var(--error-color); 
            cursor: pointer; 
            padding: 0; 
            margin-top: 5px; 
            font-size: 13px; 
            text-decoration: underline; 
            font-weight: 500;
            display: block;
        }

        .purchase-list-container {
            flex-grow: 1;
            overflow-y: auto; 
            padding-right: 10px;
        }

        .table-list { 
            width: 100%; 
            border-collapse: separate; 
            border-spacing: 0;
            font-size: 15px; 
            box-shadow: var(--shadow-subtle);
            border-radius: 10px;
            overflow: hidden;
        }

        .table-list th { 
            background-color: var(--accent-color); /* Warna yang lebih tegas */
            color: white;
            padding: 15px; 
            text-align: left; 
            position: sticky;
            top: 0;
            font-weight: 700;
        }

        .table-list td { 
            padding: 15px; 
            border-bottom: 1px solid #f0f0f0; 
        }
        
        .table-list tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table-list tr:hover {
            background-color: #fff3cd; /* Kuning lembut saat hover */
        }

        .text-right {
            text-align: right;
        }

        /* --- Summary Section --- */
        .summary-area { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 2px solid #e0e0e0;
        }

        .summary-row { 
            display: flex; 
            justify-content: flex-end; 
            padding: 10px 0; 
            font-size: 16px; 
        }

        .summary-row .label { 
            width: 200px; 
            font-weight: 500; 
            color: #555; 
            text-align: right; 
            padding-right: 20px; 
        }

        .summary-row .value { 
            width: 150px; 
            text-align: right; 
            font-weight: 600; 
        }

        .summary-total-bayar { 
            border-top: 3px solid var(--secondary-color); 
            margin-top: 15px; 
            padding-top: 15px; 
            font-size: 24px; 
        }
        
        .summary-total-bayar .label {
            font-weight: 800;
            color: var(--secondary-color);
        }

        .summary-total-bayar .value {
            color: var(--primary-color);
            font-weight: 800;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .summary-diskon .value { 
            color: var(--success-color); 
        }
        
        .footer-action {
            margin-top: 20px;
            text-align: right;
        }
        
        .btn-kosongkan { 
            background: var(--error-color); 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 14px; 
            width: auto; 
            margin: 0; 
            transition: background-color 0.3s;
        }
        
        .btn-kosongkan:hover {
            background-color: #a71d2a;
        }
        
        .empty-cart-message {
            text-align: center; 
            padding: 80px 30px; 
            color: #adb5bd; 
            font-style: italic;
            font-size: 1.1em;
            background-color: #f8f9fa;
            border: 2px dashed #ced4da;
            border-radius: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <div class="sidebar">
        <div class="header-sidebar">
            <h1>FAZU BANANA MELT </h1>
            <p>Sistem Kasir Sederhana</p>
        </div>
        
        <form method="post" id="purchaseForm">
            
            <?php if ($statusMessage): ?>
                <div class="status-message">
                    <?= $statusMessage ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label for="kode">Kode Barang</label>
                <input type="text" id="kode" name="kode" class="input-field" placeholder="Contoh: BNT01" 
                        required value="<?= htmlspecialchars($inputKode) ?>">
            </div>
            
            <div class="input-group">
                <label for="nama">Nama Barang</label>
                <input type="text" id="nama" name="nama" class="input-field" placeholder="Masukkan Nama Barang" 
                        required value="<?= htmlspecialchars($inputNama) ?>">
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="harga">Harga Satuan (Angka)</label>
                    <input type="text" id="harga" name="harga" class="input-field" placeholder="Contoh: 15000" 
                            required value="<?= htmlspecialchars($inputHarga) ?>">
                </div>
                <div class="input-group">
                    <label for="jumlah">Jumlah Beli (Qty)</label>
                    <input type="number" id="jumlah" name="jumlah" class="input-field" min="1" placeholder="Min. 1" 
                            required value="<?= htmlspecialchars($inputJumlah) ?>">
                </div>
            </div>

            <div class="button-row">
                <button class="btn-action btn-tambahkan" name="tambah">➕ Tambahkan ke Keranjang</button>
                <button type="button" class="btn-action btn-batal" onclick="window.location.href=window.location.href">Reset</button>
            </div>
        </form>
    </div>
    
    <div class="main-content">
        
        <div class="header-main">
            <h2>🛒 Keranjang Belanja Anda</h2>
            <div class="user-info">
                <span>Selamat datang, FAHRI FRAYUDA</span>
                <div class="role">Role: ADMIN KASIR</div>
                <a href="logout.php" class="logout-btn">Logout &rarr;</a>
            </div>
        </div>
        
        <div class="purchase-list-container">
            <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table-list">
                <thead>
                    <tr>
                        <th style="width: 10%;">Kode</th>
                        <th style="width: 35%;">Nama Barang</th>
                        <th style="width: 20%;" class="text-right">Harga Satuan</th>
                        <th style="width: 15%;" class="text-right">Qty</th>
                        <th style="width: 20%;" class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['kode']) ?></td>
                        <td><?= htmlspecialchars($item['nama']) ?></td>
                        <td class="text-right"><?= formatRupiah($item['harga']) ?></td>
                        <td class="text-right"><?= $item['jumlah'] ?></td>
                        <td class="text-right">**<?= formatRupiah($item['subtotal']) ?>**</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php else: ?>
            <div class="empty-cart-message">
                <p>🛍️ Keranjang pembelian Anda saat ini kosong.</p> 
                <p>Silakan input dan tambahkan item dari panel di samping.</p>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($_SESSION['cart'])): ?>
        <div class="summary-area">
            <div class="summary-row">
                <div class="label">Total Belanja Awal</div>
                <div class="value"><?= formatRupiah($total) ?></div>
            </div>
            <div class="summary-row summary-diskon">
                <div class="label">Diskon Spesial (<?= $persenDiskon ?>%)</div>
                <div class="value">- <?= formatRupiah($diskon) ?></div>
            </div>
            <div class="summary-row summary-total-bayar">
                <div class="label">TOTAL AKHIR BAYAR</div>
                <div class="value">**<?= formatRupiah($grandTotal) ?>**</div>
            </div>
        </div>

        <div class="footer-action">
            <form method="post" style="display:inline-block;">
                <button class="btn-kosongkan" name="clear" onclick="return confirm('Anda yakin ingin MENGOSONGKAN SELURUH keranjang? Tindakan ini tidak dapat dibatalkan.');">❌ Kosongkan Keranjang</button>
            </form>
        </div>
        <?php endif; ?>

    </div> 
</div>

<script>
    // FUNGSI JAVASCRIPT UNTUK MEMFORMAT INPUT HARGA SAAT DIKETIK
    document.addEventListener('DOMContentLoaded', function() {
        const hargaInput = document.getElementById('harga');

        hargaInput.addEventListener('input', function(e) {
            let value = e.target.value;
            
            // Hapus semua karakter non-digit (termasuk titik/koma)
            value = value.replace(/[^0
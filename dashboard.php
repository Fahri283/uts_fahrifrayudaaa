<?php
session_start();

$inputKode = isset($_POST['kode']) ? strtoupper($_POST['kode']) : '';
$inputNama = isset($_POST['nama']) ? $_POST['nama'] : '';
// Hilangkan titik ribuan agar bisa dihitung (untuk input harga)
$inputHarga = isset($_POST['harga']) ? str_replace('.', '', $_POST['harga']) : ''; 
$inputJumlah = isset($_POST['jumlah']) ? intval($_POST['jumlah']) : '';
$statusMessage = '';

// Tombol Kosongkan Keranjang
if (isset($_POST['clear'])) {
    unset($_SESSION['cart']);
    $statusMessage = 'Keranjang berhasil dikosongkan.';
}

// Proses Tambah Barang
if (isset($_POST['tambah'])) {
    $kode = strtoupper($_POST['kode']);
    $nama = $_POST['nama'];
    // Hapus format Rupiah atau titik ribuan untuk perhitungan
    $harga = intval(str_replace(['.', 'Rp '], '', $_POST['harga'])); 
    $jumlah = intval($_POST['jumlah']);

    // Validasi dasar
    if (empty($kode) || empty($nama) || empty($harga) || empty($jumlah) || $harga <= 0 || $jumlah <= 0) {
        $statusMessage = '<span style="color:red;">⚠️ Error: Harap isi semua kolom dengan nilai yang valid.</span>';
    } else {
        // Data valid, proses penambahan
        $subtotal = $harga * $jumlah;
        $found = false;

        // Cek jika barang sudah ada di keranjang (didasarkan pada kode)
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['kode'] === $kode) {
                    // Jika kode sama, hanya update jumlah dan subtotal
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
        $statusMessage = '<span style="color:green;">✅ Sukses: Barang ' . htmlspecialchars($nama) . ' berhasil ditambahkan.</span>';
    }
}

// --- Kalkulasi Total ---
$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['subtotal'];
    }
}

// --- Logika Diskon ---
$persenDiskon = 0;
if ($total > 0 && $total < 50000) {
    $persenDiskon = 5;
} elseif ($total >= 50000 && $total <= 100000) {
    $persenDiskon = 10;
} elseif ($total > 100000) {
    $persenDiskon = 15;
}

$diskon = ($persenDiskon / 100) * $total;
$grandTotal = $total - $diskon;

// Fungsi untuk format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard POS FAZU BANANA MELT</title>
    <style>
        /* --- General Setup --- */
        :root {
            --primary-color: #f7d300; /* Kuning Pisang */
            --secondary-color: #333;
            --background-light: #f9f9f9;
            --background-dark: #eee;
            --shadow-light: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-medium: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--background-light); 
            padding: 0; 
            margin: 0; 
            display: flex; 
            justify-content: center; 
            min-height: 100vh;
        }

        .dashboard-container { 
            width: 95%; 
            max-width: 1200px; 
            background: white; 
            margin: 30px auto; 
            border-radius: 15px; 
            box-shadow: var(--shadow-medium); 
            display: flex;
            min-height: 80vh;
            overflow: hidden;
        }

        /* --- Sidebar / Input Section --- */
        .sidebar {
            width: 350px;
            background: var(--background-dark);
            padding: 30px 20px;
            border-right: 1px solid #ddd;
            display: flex;
            flex-direction: column;
        }

        .header-sidebar {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
        }

        .header-sidebar h1 {
            margin: 0;
            color: var(--secondary-color);
            font-size: 24px;
            font-weight: 700;
        }
        
        .header-sidebar p {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .input-group { 
            margin-bottom: 20px; 
        }

        label { 
            display: block; 
            font-size: 14px; 
            font-weight: 600; 
            color: var(--secondary-color); 
            margin-bottom: 8px; 
        }

        .input-field { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            box-sizing: border-box; 
            font-size: 14px; 
            transition: border-color 0.3s, box-shadow 0.3s; 
        }

        .input-field:focus { 
            border-color: var(--primary-color); 
            box-shadow: 0 0 0 3px rgba(247, 211, 0, 0.3); 
            outline: none;
        }

        .form-row { 
            display: flex; 
            gap: 15px; 
        }

        .form-row .input-group { 
            flex: 1; 
        }

        .button-row { 
            display: flex; 
            gap: 10px; 
            margin-top: 20px; 
        }
        
        .btn-action { 
            padding: 12px 20px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 15px; 
            transition: background-color 0.3s, opacity 0.3s; 
        }
        
        .btn-tambahkan { 
            background: var(--primary-color); 
            color: var(--secondary-color); 
            border: none; 
            flex-grow: 1;
        }

        .btn-tambahkan:hover {
            background-color: #e0be00;
        }
        
        .btn-batal { 
            background: #ffffff; 
            color: #666; 
            border: 1px solid #ccc; 
            width: 100px;
        }
        
        .btn-batal:hover {
            background-color: #f0f0f0;
        }
        
        .status-message {
            margin: 10px 0 15px 0;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            background-color: #fff8e1; /* Latar belakang untuk pesan status */
            border-left: 5px solid var(--primary-color);
        }
        
        /* --- Main Content / Keranjang Section --- */
        .main-content {
            flex-grow: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
        }

        .header-main {
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding-bottom: 15px;
        }
        
        .header-main h2 {
            font-size: 20px;
            margin: 0;
            color: var(--secondary-color);
        }

        .user-info {
            text-align: right; 
            font-size: 14px;
        }

        .user-info .role { 
            font-size: 12px; 
            color: #888; 
            margin-top: 2px;
        }

        .logout-btn { 
            background: none; 
            border: none; 
            color: #dc3545; 
            cursor: pointer; 
            padding: 0; 
            margin-top: 4px; 
            font-size: 12px; 
            text-decoration: none; 
            font-weight: 500;
        }

        .purchase-list-container {
            flex-grow: 1;
            overflow-y: auto; /* Scroll jika keranjang penuh */
            padding-right: 5px;
        }

        .table-list { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 14px; 
        }

        .table-list th { 
            background-color: var(--background-dark);
            color: var(--secondary-color);
            padding: 12px 15px; 
            text-align: left; 
            border-bottom: 2px solid var(--primary-color);
            position: sticky;
            top: 0;
        }

        .table-list td { 
            padding: 12px 15px; 
            border-bottom: 1px solid #f0f0f0; 
        }
        
        .table-list tr:hover {
            background-color: #fffbe6;
        }

        .text-right {
            text-align: right;
        }

        /* --- Summary Section --- */
        .summary-area { 
            margin-top: 20px; 
            padding-top: 15px; 
            border-top: 1px solid #ddd;
        }

        .summary-row { 
            display: flex; 
            justify-content: flex-end; 
            padding: 8px 0; 
            font-size: 15px; 
        }

        .summary-row .label { 
            width: 150px; 
            font-weight: 500; 
            color: #555; 
            text-align: right; 
            padding-right: 15px; 
        }

        .summary-row .value { 
            width: 120px; 
            text-align: right; 
            font-weight: 600; 
        }

        .summary-total-bayar { 
            border-top: 2px solid var(--secondary-color); 
            margin-top: 10px; 
            padding-top: 10px; 
            font-size: 18px; 
        }
        
        .summary-total-bayar .label {
            font-weight: 700;
            color: var(--secondary-color);
        }

        .summary-total-bayar .value {
            color: var(--primary-color);
            font-weight: 700;
        }

        .summary-diskon .value { 
            color: #28a745; /* Hijau untuk diskon */
        }
        
        .footer-action {
            margin-top: 15px;
            text-align: right;
        }
        
        .btn-kosongkan { 
            background: #dc3545; 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 13px; 
            width: auto; 
            margin: 0; 
            transition: background-color 0.3s;
        }
        
        .btn-kosongkan:hover {
            background-color: #c82333;
        }
        
        .empty-cart-message {
            text-align: center; 
            padding: 50px 20px; 
            color: #888; 
            font-style: italic;
            background-color: #fcfcfc;
            border: 1px dashed #ddd;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    
    <div class="sidebar">
        <div class="header-sidebar">
            <h1>FAZU BANANA POS</h1>
            <p>Input Manual Penjualan</p>
        </div>
        
        <form method="post">
            
            <?php if ($statusMessage): ?>
                <div class="status-message">
                    <?= $statusMessage ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <label for="kode">Kode Barang</label>
                <input type="text" id="kode" name="kode" class="input-field" placeholder="Contoh: A01" 
                        required value="<?= htmlspecialchars($inputKode) ?>">
            </div>
            
            <div class="input-group">
                <label for="nama">Nama Barang</label>
                <input type="text" id="nama" name="nama" class="input-field" placeholder="Masukkan Nama Barang" 
                        required value="<?= htmlspecialchars($inputNama) ?>">
            </div>

            <div class="form-row">
                <div class="input-group">
                    <label for="harga">Harga (Rp)</label>
                    <input type="text" id="harga" name="harga" class="input-field" placeholder="Contoh: 5000" 
                            required value="<?= htmlspecialchars($inputHarga) ?>">
                </div>
                <div class="input-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" class="input-field" min="1" placeholder="Masukkan Jumlah" 
                            required value="<?= htmlspecialchars($inputJumlah) ?>">
                </div>
            </div>

            <div class="button-row">
                <button class="btn-action btn-tambahkan" name="tambah">➕ Tambahkan ke Keranjang</button>
                <button type="button" class="btn-action btn-batal" onclick="window.location.href=window.location.href">Batal</button>
            </div>
        </form>
    </div>
    
    <div class="main-content">
        
        <div class="header-main">
            <h2>🛒 Daftar Pembelian (Keranjang)</h2>
            <div class="user-info">
                <span>Halo, **FAHRI**!</span>
                <div class="role">Role: ADMIN</div>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <div class="purchase-list-container">
            <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table-list">
                <thead>
                    <tr>
                        <th style="width: 10%;">Kode</th>
                        <th style="width: 40%;">Nama Barang</th>
                        <th style="width: 15%;" class="text-right">Harga Satuan</th>
                        <th style="width: 10%;" class="text-right">Qty</th>
                        <th style="width: 25%;" class="text-right">Subtotal</th>
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
                Keranjang pembelian Anda saat ini kosong. Silakan tambahkan item dari formulir di samping.
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
                <div class="label">Diskon (<?= $persenDiskon ?>%)</div>
                <div class="value">- <?= formatRupiah($diskon) ?></div>
            </div>
            <div class="summary-row summary-total-bayar">
                <div class="label">GRAND TOTAL BAYAR</div>
                <div class="value">**<?= formatRupiah($grandTotal) ?>**</div>
            </div>
        </div>

        <div class="footer-action">
            <form method="post" style="display:inline-block;">
                <button class="btn-kosongkan" name="clear" onclick="return confirm('Anda yakin ingin mengosongkan seluruh keranjang?');">❌ Kosongkan Keranjang</button>
            </form>
        </div>
        <?php endif; ?>

    </div> </div> </body>
</html>
<?php
// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inisialisasi Session
session_start();

// Data Awal (Mock Database)
if(!isset($_SESSION['produk'])) {
    $_SESSION['produk'] = [
        ['id' => 1, 'nama' => 'Beras', 'harga' => 12000, 'stok' => 50, 'satuan' => 'kg'],
        ['id' => 2, 'nama' => 'Minyak Goreng', 'harga' => 25000, 'stok' => 30, 'satuan' => 'liter'],
        ['id' => 3, 'nama' => 'Gula Pasir', 'harga' => 15000, 'stok' => 40, 'satuan' => 'kg']
    ];
}

if(!isset($_SESSION['pelanggan'])) {
    $_SESSION['pelanggan'] = [
        ['id' => 1, 'nama' => 'Budi Santoso', 'alamat' => 'Jl. Merdeka No. 10', 'telp' => '08123456789'],
        ['id' => 2, 'nama' => 'Siti Aminah', 'alamat' => 'Jl. Sudirman No. 5', 'telp' => '08234567890']
    ];
}

if(!isset($_SESSION['transaksi'])) {
    $_SESSION['transaksi'] = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Toko Kelontong</title>
    <style>
        /* CSS Variables */
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --secondary: #ff6600;
            --secondary-dark: #cc5500;
            --light: #f8f8f8;
            --dark: #333;
            --white: #ffffff;
            --gray: #f4f4f4;
            --shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Reset & Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--gray);
            color: var(--dark);
            line-height: 1.6;
            padding-top: 80px;
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        header {
            background-color: var(--primary);
            color: var(--white);
            padding: 12px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Navigation */
        nav ul {
            display: flex;
            gap: 15px;
            list-style: none;
        }

        nav a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        nav a:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* Main Content */
        .page-title {
            color: var(--primary);
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--secondary);
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .menu-card {
            background-color: var(--white);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-decoration: none;
            color: var(--dark);
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .menu-card-header {
            background-color: var(--primary);
            color: var(--white);
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .menu-card-body {
            padding: 20px;
            background-color: var(--white);
            text-align: center;
            color: var(--dark);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .header-container {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .menu-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <span>🛒</span>
                <span>Toko Kelontong</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="produk.php">Produk</a></li>
                    <li><a href="transaksi.php">Transaksi</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Sistem Pengelolaan Toko Kelontong</h1>
        
        <div class="menu-grid">
            <a href="produk.php" class="menu-card">
                <div class="menu-card-header">Manajemen Produk</div>
                <div class="menu-card-body">Kelola stok, harga, dan data produk</div>
            </a>
            
            <a href="transaksi.php" class="menu-card">
                <div class="menu-card-header">Transaksi Penjualan</div>
                <div class="menu-card-body">Proses pembelian pelanggan</div>
            </a>
            
            <a href="laporan_stock.php" class="menu-card">
                <div class="menu-card-header">Laporan Stok</div>
                <div class="menu-card-body">Pantau persediaan barang</div>
            </a>
            
            <a href="rekap_penjualan.php" class="menu-card">
                <div class="menu-card-header">Rekap Penjualan</div>
                <div class="menu-card-body">Rekap Penjualan per Hari/Bulan/Tahun</div>
            </a>
            
            <a href="cari_produk.php" class="menu-card">
                <div class="menu-card-header">Cari Produk</div>
                <div class="menu-card-body">Temukan produk cepat</div>
            </a>
            
            <a href="faktur.php" class="menu-card">
                <div class="menu-card-header">Faktur Penjualan</div>
                <div class="menu-card-body">Cetak bukti transaksi</div>
            </a>
        </div>
    </div>
</body>
</html>
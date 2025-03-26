<?php
session_start();

// Fungsi untuk mencari produk
function cariProduk($keyword = '', $hanya_tersedia = true) {
    $hasil = [];
    
    if(isset($_SESSION['produk'])) {
        foreach($_SESSION['produk'] as $produk) {
            if($hanya_tersedia && $produk['stok'] <= 0) continue;
            if(!empty($keyword) && stripos($produk['nama'], $keyword) === false) continue;
            $hasil[] = $produk;
        }
    }
    
    return $hasil;
}

// Proses pencarian
$keyword = '';
$hanya_tersedia = true;
$hasil_pencarian = [];
$pesan = '';

if(isset($_GET['cari'])) {
    $keyword = trim($_GET['keyword']);
    $hanya_tersedia = isset($_GET['hanya_tersedia']) ? true : false;
    $hasil_pencarian = cariProduk($keyword, $hanya_tersedia);
    
    $pesan = empty($hasil_pencarian) 
        ? (empty($keyword) ? "Tidak ada produk yang tersedia" : "Produk tidak ditemukan dengan kata kunci '$keyword'")
        : "Menampilkan hasil pencarian";
} else {
    $hasil_pencarian = cariProduk('', true);
    $pesan = "Menampilkan semua produk yang tersedia";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Produk - Toko Kelontong</title>
    <style>
        /* Gunakan variabel CSS yang sama dengan index.php */
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
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
        }

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


        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --secondary: #ff6600;
            --light: #f8f8f8;
            --dark: #333;
            --white: #ffffff;
            --gray: #f4f4f4;
            --shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            --danger: #dc3545;
            --warning: #ffc107;
            --success: #28a745;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: var(--gray);
            color: var(--dark);
            line-height: 1.6;
            padding-top: 80px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
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

        .search-box {
            background-color: var(--white);
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
        }

        .search-form input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        .search-form input[type="text"]:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .filter-options {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-form button {
            padding: 12px 25px;
            background-color: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-form button:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
            font-weight: 500;
        }

        .info {
            background-color: rgba(0, 123, 255, 0.1);
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }

        .warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #856404;
            border-left: 4px solid var(--warning);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            box-shadow: var(--shadow);
            background-color: var(--white);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .highlight {
            background-color: #fffacd;
            padding: 2px 4px;
            border-radius: 2px;
        }

        .stok-habis {
            color: var(--danger);
            font-weight: bold;
        }

        .stok-sedikit {
            color: var(--warning);
            font-weight: bold;
        }

        .stok-cukup {
            color: var(--success);
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 25px;
            background-color: var(--primary);
            color: var(--white);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .result-count {
            margin: 15px 0;
            font-style: italic;
            color: #666;
            text-align: center;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .checkbox-label input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
            
            table {
                display: block;
                overflow-x: auto;
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
        <h1 class="page-title">Cari Produk</h1>
        
        <div class="search-box">
            <form method="get" class="search-form">
                <div class="search-input">
                    <input type="text" name="keyword" placeholder="Masukkan nama produk..." value="<?= htmlspecialchars($keyword) ?>">
                </div>
                
                <div class="filter-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="hanya_tersedia" <?= $hanya_tersedia ? 'checked' : '' ?>>
                        Hanya tampilkan produk tersedia
                    </label>
                    
                    <button type="submit" name="cari">Cari</button>
                </div>
            </form>
        </div>
        
        <div class="message <?= empty($hasil_pencarian) ? 'warning' : 'info' ?>">
            <?= $pesan ?>
        </div>
        
        <?php if(!empty($hasil_pencarian)): ?>
            <div class="result-count">Ditemukan <?= count($hasil_pencarian) ?> produk</div>
            
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($hasil_pencarian as $i => $produk): ?>
                    <?php
                        $nama_produk = !empty($keyword) 
                            ? str_ireplace($keyword, '<span class="highlight">'.$keyword.'</span>', $produk['nama'])
                            : $produk['nama'];
                        
                        if($produk['stok'] == 0) {
                            $stok_class = 'stok-habis';
                            $status_stok = 'Habis';
                        } elseif($produk['stok'] <= 5) {
                            $stok_class = 'stok-sedikit';
                            $status_stok = 'Hampir Habis';
                        } else {
                            $stok_class = 'stok-cukup';
                            $status_stok = 'Tersedia';
                        }
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $nama_produk ?></td>
                        <td>Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                        <td class="<?= $stok_class ?>"><?= $produk['stok'] ?></td>
                        <td class="<?= $stok_class ?>"><?= $status_stok ?></td>
                        <td><?= $produk['satuan'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <a href="index.php" class="back-link">Kembali ke Menu Utama</a>
    </div>
</body>
</html>
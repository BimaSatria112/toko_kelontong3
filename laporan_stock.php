<?php
session_start();

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Produk | Toko Kelontong</title>
    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --secondary: #ff6600;
            --secondary-dark: #cc5500;
            --success: #4CAF50;
            --success-dark: #45a049;
            --danger: #dc3545;
            --danger-dark: #c82333;
            --light: #f8f8f8;
            --dark: #333;
            --white: #ffffff;
            --gray: #f4f4f4;
            --shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
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

        .card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .table-responsive {
            overflow-x: auto;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary);
            color: var(--white);
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 16px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: var(--success);
            color: var(--white);
        }

        .btn-success:hover {
            background-color: var(--success-dark);
            transform: translateY(-2px);
        }

        .stock-low {
            color: var(--danger);
            font-weight: bold;
        }

        .stock-medium {
            color: var(--warning);
        }

        .stock-high {
            color: var(--success);
        }

        .summary-card {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            flex: 1;
            min-width: 200px;
            background-color: var(--white);
            border-radius: 8px;
            padding: 15px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 10px 0;
            color: var(--primary);
        }

        @media print {
            body {
                padding-top: 0;
                background-color: white;
            }
            
            header, .action-buttons {
                display: none;
            }
            
            .container {
                box-shadow: none;
                padding: 0;
            }
            
            table {
                width: 100%;
            }
        }

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
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px 10px;
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
        <h1 class="page-title">Laporan Stok Produk</h1>
        
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-success">
                Cetak Laporan
            </button>
            <a href="index.php" class="btn btn-primary">
                Kembali ke Menu Utama
            </a>
        </div>
        
        <?php if(!empty($_SESSION['produk'])): ?>
            <?php
            $total_nilai = 0;
            $total_produk = 0;
            $produk_habis = 0;
            $produk_sedikit = 0;
            
            foreach($_SESSION['produk'] as $produk) {
                $nilai_stok = $produk['harga'] * $produk['stok'];
                $total_nilai += $nilai_stok;
                $total_produk++;
                
                if($produk['stok'] == 0) {
                    $produk_habis++;
                } elseif($produk['stok'] < 5) {
                    $produk_sedikit++;
                }
            }
            ?>
            
            <div class="summary-card">
                <div class="summary-item">
                    <div>Total Produk</div>
                    <div class="summary-value"><?= $total_produk ?></div>
                </div>
                <div class="summary-item">
                    <div>Total Nilai Stok</div>
                    <div class="summary-value">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
                </div>
                <div class="summary-item">
                    <div>Produk Habis</div>
                    <div class="summary-value" style="color: var(--danger);"><?= $produk_habis ?></div>
                </div>
                <div class="summary-item">
                    <div>Stok Sedikit (<5)</div>
                    <div class="summary-value" style="color: var(--warning);"><?= $produk_sedikit ?></div>
                </div>
            </div>
            
            <div class="card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-center">Stok</th>
                                <th>Satuan</th>
                                <th class="text-right">Nilai Stok</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($_SESSION['produk'] as $index => $produk): ?>
                                <?php
                                $nilai_stok = $produk['harga'] * $produk['stok'];
                                $status_class = '';
                                $status_text = '';
                                
                                if($produk['stok'] == 0) {
                                    $status_class = 'stock-low';
                                    $status_text = 'Habis';
                                } elseif($produk['stok'] < 5) {
                                    $status_class = 'stock-medium';
                                    $status_text = 'Sedikit';
                                } else {
                                    $status_class = 'stock-high';
                                    $status_text = 'Aman';
                                }
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($produk['nama']) ?></td>
                                    <td class="text-right">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                    <td class="text-center"><?= $produk['stok'] ?></td>
                                    <td><?= htmlspecialchars($produk['satuan']) ?></td>
                                    <td class="text-right">Rp <?= number_format($nilai_stok, 0, ',', '.') ?></td>
                                    <td class="text-center <?= $status_class ?>"><?= $status_text ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-right"><strong>TOTAL NILAI STOK:</strong></td>
                                <td class="text-right"><strong>Rp <?= number_format($total_nilai, 0, ',', '.') ?></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <p style="text-align: center;">Tidak ada data produk</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Tambahkan efek saat mencetak
        window.onafterprint = function() {
            alert("Cetakan berhasil. Silahkan ambil dokumen dari printer Anda.");
        };
    </script>
</body>
</html>
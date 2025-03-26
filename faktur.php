<?php
session_start();

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_GET['id'])){
    header("Location: transaksi.php");
    exit();
}

$id_transaksi = (int)$_GET['id'];
$transaksi = null;

// Find transaction by ID
foreach($_SESSION['transaksi'] as $t){
    if($t['id'] == $id_transaksi){
        $transaksi = $t;
        break;
    }
}

if(!$transaksi){
    header("Location: transaksi.php");
    exit();
}

// Process saving to sales recap
if(isset($_POST['simpan'])){
    if(!isset($_SESSION['rekap_penjualan'])){
        $_SESSION['rekap_penjualan'] = [];
    }
    
    // Check if transaction already exists in recap
    $sudah_ada = false;
    foreach($_SESSION['rekap_penjualan'] as $rekap){
        if($rekap['id'] == $transaksi['id']){
            $sudah_ada = true;
            break;
        }
    }
    
    if(!$sudah_ada){
        $_SESSION['rekap_penjualan'][] = $transaksi;
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Transaksi berhasil disimpan ke rekap penjualan!'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Transaksi ini sudah tersimpan di rekap penjualan.'
        ];
    }
    
    header("Location: faktur.php?id=".$transaksi['id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan | Toko Kelontong</title>
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
            --warning: #ffc107;
            --warning-dark: #e0a800;
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
            padding-top: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--white);
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--primary);
        }

        .invoice-title {
            color: var(--primary);
            margin-bottom: 5px;
        }

        .store-name {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .store-address {
            color: #666;
            font-size: 0.9rem;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .info-box {
            flex: 1;
            min-width: 250px;
            margin: 10px;
        }

        .info-label {
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
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

        .invoice-total {
            text-align: right;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 20px 0;
            padding: 15px;
            background-color: var(--light);
            border-radius: 4px;
            border-left: 4px solid var(--primary);
        }

        .invoice-footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-size: 0.9rem;
            padding-top: 20px;
            border-top: 1px dashed #ddd;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 30px;
            gap: 10px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
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

        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: var(--danger-dark);
            transform: translateY(-2px);
        }

        .btn-warning {
            background-color: var(--secondary);
            color: var(--white);
        }

        .btn-warning:hover {
            background-color: var(--secondary-dark);
            transform: translateY(-2px);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .close-alert {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: inherit;
        }

        @media print {
            body {
                padding: 0;
                background-color: white;
                font-size: 12pt;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            
            .action-buttons {
                display: none;
            }
            
            .no-print {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .invoice-container {
                padding: 15px;
            }
            
            .invoice-info {
                flex-direction: column;
            }
            
            .info-box {
                margin: 5px 0;
            }
            
            th, td {
                padding: 8px 10px;
                font-size: 14px;
            }
            
            .btn {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h1 class="invoice-title">FAKTUR PENJUALAN</h1>
            <div class="store-name">Toko Kelontong Bima Satria</div>
            <div class="store-address">
                Jl. Parkit X, Kota Padang | Telp: (+62) 81536525598 | Email: bimasatria020704@gmail.com
            </div>
        </div>
        
        <?php if(isset($_SESSION['alert'])): ?>
            <div class="alert alert-<?= $_SESSION['alert']['type'] ?>">
                <span><?= $_SESSION['alert']['message'] ?></span>
                <button class="close-alert">&times;</button>
            </div>
            <?php unset($_SESSION['alert']); ?>
        <?php endif; ?>
        
        <div class="invoice-info">
            <div class="info-box">
                <div class="info-label">No. Faktur</div>
                <div>INV-<?= str_pad($transaksi['id'], 5, '0', STR_PAD_LEFT) ?></div>
                
                <div class="info-label" style="margin-top: 15px;">Tanggal</div>
                <div><?= date('d/m/Y H:i', strtotime($transaksi['tanggal'])) ?></div>
            </div>
            
            <div class="info-box">
                <div class="info-label">Pelanggan</div>
                <div><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></div>
                
                <div class="info-label" style="margin-top: 15px;">Kasir</div>
                <div><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Admin' ?></div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th class="text-right">Harga Satuan</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach($transaksi['items'] as $id_produk => $qty): ?>
                        <?php if($qty > 0): ?>
                            <?php foreach($_SESSION['produk'] as $produk): ?>
                                <?php if($produk['id'] == $id_produk): ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= htmlspecialchars($produk['nama']) ?></td>
                                        <td class="text-right">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                        <td class="text-center"><?= $qty ?> <?= htmlspecialchars($produk['satuan']) ?></td>
                                        <td class="text-right">Rp <?= number_format($produk['harga'] * $qty, 0, ',', '.') ?></td>
                                    </tr>
                                    <?php $no++; ?>
                                    <?php break; ?>
<?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="invoice-total">
            TOTAL PEMBAYARAN: Rp <?= number_format($transaksi['total'], 0, ',', '.') ?>
        </div>
        
        <div class="invoice-footer">
            <p>Terima kasih telah berbelanja di toko kami</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</p>
            <p class="no-print">Faktur ini sah tanpa tanda tangan</p>
        </div>
        
        <form method="post" class="action-buttons no-print">
            <div class="btn-group">
                <button type="submit" name="simpan" class="btn btn-success">
                    Simpan ke Rekap
                </button>
                <a href="transaksi.php" class="btn btn-primary">
                    Transaksi Baru
                </a>
            </div>
            
            <div class="btn-group">
                <a href="#" class="btn btn-warning" onclick="window.print()">
                    Cetak Faktur
                </a>
                <a href="index.php" class="btn btn-danger">
                    Menu Utama
                </a>
            </div>
        </form>
    </div>

    <script>
        // Close alert
        document.querySelectorAll('.close-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });
        
        // Auto close alert after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
        
        // Print feedback
        window.onafterprint = function() {
            alert("Faktur berhasil dicetak. Silahkan ambil dokumen dari printer Anda.");
        };
    </script>
</body>
</html>
<?php
session_start();

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Proses transaksi
if(isset($_POST['proses'])){
    $nama_pelanggan = htmlspecialchars($_POST['nama_pelanggan']);
    $items = $_POST['items'];
    $total = 0;
    $error = false;
    
    // Validasi stok
    foreach($items as $item_id => $qty){
        if($qty > 0){
            foreach($_SESSION['produk'] as $produk){
                if($produk['id'] == $item_id && $qty > $produk['stok']){
                    $error = true;
                    $_SESSION['error'] = "Stok ".$produk['nama']." tidak mencukupi (Stok tersedia: ".$produk['stok'].")";
                    break 2;
                }
            }
        }
    }
    
    if(!$error){
        // Hitung total dan kurangi stok
        foreach($items as $item_id => $qty){
            if($qty > 0){
                foreach($_SESSION['produk'] as &$produk){
                    if($produk['id'] == $item_id){
                        $produk['stok'] -= $qty;
                        $total += $produk['harga'] * $qty;
                        break;
                    }
                }
            }
        }
        
        // Simpan transaksi
        $transaksi = [
            'id' => count($_SESSION['transaksi']) + 1,
            'nama_pelanggan' => $nama_pelanggan,
            'tanggal' => date('Y-m-d H:i:s'),
            'items' => array_filter($items, function($qty) { return $qty > 0; }),
            'total' => $total
        ];
        
        array_push($_SESSION['transaksi'], $transaksi);
        $_SESSION['last_transaction'] = $transaksi['id'];
        
        header("Location: faktur.php?id=".$transaksi['id']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Penjualan | Toko Kelontong</title>
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }

        input:focus, select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
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

        input[type="number"] {
            width: 80px;
            text-align: center;
            padding: 8px;
        }

        .note {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-danger {
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

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
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
            
            .card {
                padding: 15px;
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
                    <li><a href="transaksi.php" class="active">Transaksi</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Transaksi Penjualan</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <span><?= $_SESSION['error'] ?></span>
                <button class="close-alert">&times;</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <form method="post">
                <div class="form-group">
                    <label for="nama_pelanggan">Nama Pelanggan</label>
                    <input type="text" id="nama_pelanggan" name="nama_pelanggan" required 
                           placeholder="Contoh: Budi Santoso">
                </div>
                
                <h3>Daftar Produk</h3>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Stok Tersedia</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($_SESSION['produk'])): ?>
                                <?php foreach($_SESSION['produk'] as $produk): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($produk['nama']) ?></td>
                                        <td>Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                        <td><?= $produk['stok'] ?> <?= htmlspecialchars($produk['satuan']) ?></td>
                                        <td class="text-center">
                                            <input type="number" name="items[<?= $produk['id'] ?>]" 
                                                   min="0" max="<?= $produk['stok'] ?>" value="0" 
                                                   class="qty-input" data-price="<?= $produk['harga'] ?>"
                                                   onchange="calculateTotal()">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada produk tersedia</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total Pembayaran:</strong></td>
                                <td class="text-center"><strong id="total-amount">Rp 0</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="action-buttons">
                    <button type="submit" name="proses" class="btn btn-success">
                        Proses Transaksi
                    </button>
                    <a href="index.php" class="btn btn-primary">
                        Kembali ke Menu Utama
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Hitung total secara real-time
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('.qty-input').forEach(input => {
                const qty = parseInt(input.value) || 0;
                const price = parseInt(input.dataset.price) || 0;
                total += qty * price;
            });
            document.getElementById('total-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
        
        // Tutup alert
        document.querySelectorAll('.close-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });
        
        // Inisialisasi perhitungan pertama kali
        calculateTotal();
    </script>
</body>
</html>
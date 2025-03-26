<?php
session_start();

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tambah Produk
if(isset($_POST['tambah'])){
    $id = count($_SESSION['produk']) + 1;
    $produk_baru = [
        'id' => $id,
        'nama' => htmlspecialchars($_POST['nama']),
        'harga' => (int)$_POST['harga'],
        'stok' => (int)$_POST['stok'],
        'satuan' => htmlspecialchars($_POST['satuan'])
    ];
    array_push($_SESSION['produk'], $produk_baru);
    header("Location: produk.php");
    exit();
}

// Hapus Produk
if(isset($_GET['hapus'])){
    $id = (int)$_GET['hapus'];
    foreach($_SESSION['produk'] as $key => $produk){
        if($produk['id'] == $id){
            unset($_SESSION['produk'][$key]);
            break;
        }
    }
    header("Location: produk.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk | Toko Kelontong</title>
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .action-links a {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            margin-right: 5px;
            font-size: 14px;
        }

        .edit-link {
            background-color: var(--primary);
            color: white;
        }

        .delete-link {
            background-color: var(--danger);
            color: white;
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
                    <li><a href="produk.php" class="active">Produk</a></li>
                    <li><a href="transaksi.php">Transaksi</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Manajemen Produk</h1>
        
        <?php if(isset($_GET['status']) && $_GET['status'] == 'tambah'): ?>
            <div class="alert alert-success">
                <span>Produk berhasil ditambahkan!</span>
                <button class="close-alert">&times;</button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['status']) && $_GET['status'] == 'hapus'): ?>
            <div class="alert alert-danger">
                <span>Produk berhasil dihapus!</span>
                <button class="close-alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Tambah Produk Baru</h2>
            <form method="post">
                <div class="form-group">
                    <label for="nama">Nama Produk</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                
                <div class="form-group">
                    <label for="harga">Harga (Rp)</label>
                    <input type="number" id="harga" name="harga" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" id="stok" name="stok" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="satuan">Satuan</label>
                    <input type="text" id="satuan" name="satuan" required>
                </div>
                
                <button type="submit" name="tambah" class="btn btn-success">Tambah Produk</button>
            </form>
        </div>

        <div class="card">
            <h2>Daftar Produk</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($_SESSION['produk'])): ?>
                            <?php foreach($_SESSION['produk'] as $produk): ?>
                                <tr>
                                    <td><?= $produk['id'] ?></td>
                                    <td><?= htmlspecialchars($produk['nama']) ?></td>
                                    <td>Rp <?= number_format($produk['harga'], 0, ',', '.') ?></td>
                                    <td><?= $produk['stok'] ?></td>
                                    <td><?= htmlspecialchars($produk['satuan']) ?></td>
                                    <td class="text-center action-links">
                                        <a href="produk_edit.php?id=<?= $produk['id'] ?>" class="btn btn-primary edit-link">Edit</a>
                                        <a href="produk.php?hapus=<?= $produk['id'] ?>" class="btn btn-danger delete-link" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data produk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Tutup alert otomatis
        document.querySelectorAll('.close-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.parentElement.style.display = 'none';
            });
        });

        // Tutup alert setelah 5 detik
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>
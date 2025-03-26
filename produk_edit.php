<?php
session_start();

// Redirect jika tidak ada parameter ID
if(!isset($_GET['id'])) {
    header("Location: produk.php");
    exit();
}

$id_produk = $_GET['id'];
$produk = null;

// Cari produk berdasarkan ID
foreach($_SESSION['produk'] as &$item) {
    if($item['id'] == $id_produk) {
        $produk = &$item;
        break;
    }
}

// Redirect jika produk tidak ditemukan
if($produk === null) {
    header("Location: produk.php");
    exit();
}

// Proses update produk
if(isset($_POST['update'])) {
    $produk['nama'] = $_POST['nama'];
    $produk['harga'] = $_POST['harga'];
    $produk['stok'] = $_POST['stok'];
    $produk['satuan'] = $_POST['satuan'];
    
    $_SESSION['pesan'] = "Produk berhasil diperbarui!";
    header("Location: produk.php");
    exit();
}

// Proses hapus produk
if(isset($_POST['hapus'])) {
    foreach($_SESSION['produk'] as $key => $item) {
        if($item['id'] == $id_produk) {
            unset($_SESSION['produk'][$key]);
            break;
        }
    }
    
    $_SESSION['pesan'] = "Produk berhasil dihapus!";
    header("Location: produk.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Toko Kelontong</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-update {
            background-color: #4CAF50;
            color: white;
        }
        .btn-update:hover {
            background-color: #45a049;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        .btn-back {
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 20px;
        }
        .btn-back:hover {
            background-color: #0b7dda;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>
        
        <?php if(isset($_SESSION['pesan'])): ?>
            <div class="message success"><?= $_SESSION['pesan'] ?></div>
            <?php unset($_SESSION['pesan']); ?>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="nama">Nama Produk:</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($produk['nama']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="harga">Harga:</label>
                <input type="number" id="harga" name="harga" value="<?= $produk['harga'] ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="stok">Stok:</label>
                <input type="number" id="stok" name="stok" value="<?= $produk['stok'] ?>" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="satuan">Satuan:</label>
                <input type="text" id="satuan" name="satuan" value="<?= htmlspecialchars($produk['satuan']) ?>" required>
            </div>
            
            <div class="button-group">
                <button type="submit" name="update" class="btn-update">Update Produk</button>
                <button type="submit" name="hapus" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">Hapus Produk</button>
            </div>
        </form>
        
        <a href="produk.php" class="btn-back">Kembali ke Daftar Produk</a>
    </div>
</body>
</html>
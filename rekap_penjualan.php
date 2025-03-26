<?php
session_start();

// Fungsi untuk mendapatkan rekap harian
function getRekapHarian($tanggal) {
    $rekap = [];
    $total = 0;
    
    if(isset($_SESSION['transaksi'])) {
        foreach($_SESSION['transaksi'] as $transaksi) {
            if(date('Y-m-d', strtotime($transaksi['tanggal'])) == $tanggal) {
                $total += $transaksi['total'];
                
                foreach($transaksi['items'] as $id_produk => $qty) {
                    if($qty > 0) {
                        if(!isset($rekap[$id_produk])) {
                            $rekap[$id_produk] = [
                                'nama' => getNamaProduk($id_produk),
                                'jumlah' => 0,
                                'subtotal' => 0
                            ];
                        }
                        $harga = getHargaProduk($id_produk);
                        $rekap[$id_produk]['jumlah'] += $qty;
                        $rekap[$id_produk]['subtotal'] += $harga * $qty;
                    }
                }
            }
        }
    }
    
    return [
        'detail' => $rekap,
        'total' => $total
    ];
}

// Fungsi untuk mendapatkan rekap bulanan
function getRekapBulanan($bulan, $tahun) {
    $rekap = [];
    $total = 0;
    
    if(isset($_SESSION['transaksi'])) {
        foreach($_SESSION['transaksi'] as $transaksi) {
            $tgl = strtotime($transaksi['tanggal']);
            if(date('m', $tgl) == $bulan && date('Y', $tgl) == $tahun) {
                $total += $transaksi['total'];
                
                foreach($transaksi['items'] as $id_produk => $qty) {
                    if($qty > 0) {
                        if(!isset($rekap[$id_produk])) {
                            $rekap[$id_produk] = [
                                'nama' => getNamaProduk($id_produk),
                                'jumlah' => 0,
                                'subtotal' => 0
                            ];
                        }
                        $harga = getHargaProduk($id_produk);
                        $rekap[$id_produk]['jumlah'] += $qty;
                        $rekap[$id_produk]['subtotal'] += $harga * $qty;
                    }
                }
            }
        }
    }
    
    return [
        'detail' => $rekap,
        'total' => $total
    ];
}

// Fungsi untuk mendapatkan rekap tahunan
function getRekapTahunan($tahun) {
    $rekap = [];
    $total = 0;
    
    if(isset($_SESSION['transaksi'])) {
        foreach($_SESSION['transaksi'] as $transaksi) {
            $tgl = strtotime($transaksi['tanggal']);
            if(date('Y', $tgl) == $tahun) {
                $total += $transaksi['total'];
                
                foreach($transaksi['items'] as $id_produk => $qty) {
                    if($qty > 0) {
                        if(!isset($rekap[$id_produk])) {
                            $rekap[$id_produk] = [
                                'nama' => getNamaProduk($id_produk),
                                'jumlah' => 0,
                                'subtotal' => 0
                            ];
                        }
                        $harga = getHargaProduk($id_produk);
                        $rekap[$id_produk]['jumlah'] += $qty;
                        $rekap[$id_produk]['subtotal'] += $harga * $qty;
                    }
                }
            }
        }
    }
    
    return [
        'detail' => $rekap,
        'total' => $total
    ];
}

// Fungsi bantu untuk mendapatkan nama produk
function getNamaProduk($id_produk) {
    if(isset($_SESSION['produk'])) {
        foreach($_SESSION['produk'] as $produk) {
            if($produk['id'] == $id_produk) {
                return $produk['nama'];
            }
        }
    }
    return "Produk Tidak Ditemukan";
}

// Fungsi bantu untuk mendapatkan harga produk
function getHargaProduk($id_produk) {
    if(isset($_SESSION['produk'])) {
        foreach($_SESSION['produk'] as $produk) {
            if($produk['id'] == $id_produk) {
                return $produk['harga'];
            }
        }
    }
    return 0;
}

// Tanggal default untuk filter
$filter_tanggal = date('Y-m-d');
$filter_bulan = date('m');
$filter_tahun = date('Y');

// Proses filter
if(isset($_GET['filter'])) {
    $filter = $_GET['filter'];
    
    if($filter == 'harian' && isset($_GET['tanggal'])) {
        $filter_tanggal = $_GET['tanggal'];
    }
    elseif($filter == 'bulanan' && isset($_GET['bulan']) && isset($_GET['tahun'])) {
        $filter_bulan = $_GET['bulan'];
        $filter_tahun = $_GET['tahun'];
    }
    elseif($filter == 'tahunan' && isset($_GET['tahun'])) {
        $filter_tahun = $_GET['tahun'];
    }
}

// Ambil data rekap
$rekap_harian = getRekapHarian($filter_tanggal);
$rekap_bulanan = getRekapBulanan($filter_bulan, $filter_tahun);
$rekap_tahunan = getRekapTahunan($filter_tahun);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Penjualan | Toko Kelontong</title>
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

        .section-title {
            color: var(--primary);
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--secondary);
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

        .total-row {
            font-weight: bold;
            background-color: rgba(0, 123, 255, 0.1);
        }

        .total-row td {
            border-top: 2px solid var(--primary);
        }

        /* Tab Styles */
        .tab {
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #f1f1f1;
            border-radius: 5px 5px 0 0;
            margin-bottom: -1px;
        }

        .tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 16px;
        }

        .tab button:hover {
            background-color: #ddd;
        }

        .tab button.active {
            background-color: var(--white);
            font-weight: bold;
            border-bottom: 3px solid var(--primary);
        }

        .tabcontent {
            display: none;
            padding: 20px;
            border: 1px solid #ccc;
            border-top: none;
            border-radius: 0 0 5px 5px;
            background-color: var(--white);
            animation: fadeEffect 0.5s;
        }

        @keyframes fadeEffect {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        /* Filter Form Styles */
        .filter-form {
            margin: 20px 0;
            padding: 15px;
            background-color: var(--light);
            border-radius: 5px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .filter-form label {
            font-weight: 500;
        }

        .filter-form input[type="date"],
        .filter-form select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .filter-form button {
            padding: 8px 16px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: var(--primary-dark);
        }

        @media print {
            body {
                padding-top: 0;
                background-color: white;
            }
            
            header, .action-buttons, .tab, .filter-form {
                display: none;
            }
            
            .container {
                box-shadow: none;
                padding: 0;
            }
            
            table {
                width: 100%;
            }
            
            .tabcontent {
                display: block !important;
                border: none;
                padding: 0;
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
            
            .filter-form {
                flex-direction: column;
                align-items: flex-start;
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
        <h1 class="page-title">Rekapitulasi Penjualan</h1>
        
        <div class="action-buttons">
            <button onclick="window.print()" class="btn btn-success">
                Cetak Laporan
            </button>
            <a href="index.php" class="btn btn-primary">
                Kembali ke Menu Utama
            </a>
        </div>
        
        <div class="card">
            <div class="tab">
                <button class="tablinks active" onclick="openTab(event, 'harian')">Harian</button>
                <button class="tablinks" onclick="openTab(event, 'bulanan')">Bulanan</button>
                <button class="tablinks" onclick="openTab(event, 'tahunan')">Tahunan</button>
            </div>
            
            <!-- Tab Harian -->
            <div id="harian" class="tabcontent" style="display: block;">
                <form method="get" class="filter-form">
                    <input type="hidden" name="filter" value="harian">
                    <label for="tanggal">Pilih Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?= htmlspecialchars($filter_tanggal) ?>">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                
                <h2 class="section-title">Rekap Penjualan Harian - <?= date('d F Y', strtotime($filter_tanggal)) ?></h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Jumlah Terjual</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($rekap_harian['detail'])): ?>
                                <?php $no = 1; ?>
                                <?php foreach($rekap_harian['detail'] as $item): ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= htmlspecialchars($item['nama']) ?></td>
                                        <td class="text-center"><?= $item['jumlah'] ?></td>
                                        <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">Tidak ada data penjualan untuk tanggal ini</td></tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td colspan="3" class="text-right"><strong>TOTAL PENJUALAN:</strong></td>
                                <td class="text-right"><strong>Rp <?= number_format($rekap_harian['total'], 0, ',', '.') ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab Bulanan -->
            <div id="bulanan" class="tabcontent">
                <form method="get" class="filter-form">
                    <input type="hidden" name="filter" value="bulanan">
                    <label for="bulan">Pilih Bulan:</label>
                    <select id="bulan" name="bulan">
                        <?php
                        $bulan = [
                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                            '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                            '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                        ];
                        
                        foreach($bulan as $key => $value) {
                            $selected = ($key == $filter_bulan) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($key)."' $selected>".htmlspecialchars($value)."</option>";
                        }
                        ?>
                    </select>
                    <label for="tahun">Tahun:</label>
                    <select id="tahun" name="tahun">
                        <?php
                        $tahun_sekarang = date('Y');
                        for($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--) {
                            $selected = ($i == $filter_tahun) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($i)."' $selected>".htmlspecialchars($i)."</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                
                <h2 class="section-title">Rekap Penjualan Bulanan - <?= htmlspecialchars($bulan[$filter_bulan]) ?> <?= htmlspecialchars($filter_tahun) ?></h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Jumlah Terjual</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($rekap_bulanan['detail'])): ?>
                                <?php $no = 1; ?>
                                <?php foreach($rekap_bulanan['detail'] as $item): ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= htmlspecialchars($item['nama']) ?></td>
                                        <td class="text-center"><?= $item['jumlah'] ?></td>
                                        <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">Tidak ada data penjualan untuk bulan ini</td></tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td colspan="3" class="text-right"><strong>TOTAL PENJUALAN:</strong></td>
                                <td class="text-right"><strong>Rp <?= number_format($rekap_bulanan['total'], 0, ',', '.') ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Tab Tahunan -->
            <div id="tahunan" class="tabcontent">
                <form method="get" class="filter-form">
                    <input type="hidden" name="filter" value="tahunan">
                    <label for="tahun">Pilih Tahun:</label>
                    <select id="tahun" name="tahun">
                        <?php
                        $tahun_sekarang = date('Y');
                        for($i = $tahun_sekarang; $i >= $tahun_sekarang - 5; $i--) {
                            $selected = ($i == $filter_tahun) ? 'selected' : '';
                            echo "<option value='".htmlspecialchars($i)."' $selected>".htmlspecialchars($i)."</option>";
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                
                <h2 class="section-title">Rekap Penjualan Tahunan - <?= htmlspecialchars($filter_tahun) ?></h2>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th class="text-center">Jumlah Terjual</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($rekap_tahunan['detail'])): ?>
                                <?php $no = 1; ?>
                                <?php foreach($rekap_tahunan['detail'] as $item): ?>
                                    <tr>
                                        <td><?= $no ?></td>
                                        <td><?= htmlspecialchars($item['nama']) ?></td>
                                        <td class="text-center"><?= $item['jumlah'] ?></td>
                                        <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">Tidak ada data penjualan untuk tahun ini</td></tr>
                            <?php endif; ?>
                            <tr class="total-row">
                                <td colspan="3" class="text-right"><strong>TOTAL PENJUALAN:</strong></td>
                                <td class="text-right"><strong>Rp <?= number_format($rekap_tahunan['total'], 0, ',', '.') ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
        
        // Tambahkan efek saat mencetak
        window.onafterprint = function() {
            alert("Cetakan berhasil. Silahkan ambil dokumen dari printer Anda.");
        };
    </script>
</body>
</html>
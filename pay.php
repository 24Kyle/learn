<?php
$conn = new mysqli('localhost', 'root', '', 'kantin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['order']) || empty(trim($_GET['order']))) {
    die("Tidak ada pesanan.");
}

$orderRaw = $_GET['order'];
$items = explode(',', $orderRaw);

$total = 0;
$menus = [];

foreach ($items as $item) {
    $parts = explode(':', $item);
    if (count($parts) !== 2) {
        die("Format pesanan tidak valid: $item");
    }
    $name = urldecode($parts[0]);
    $qty = intval($parts[1]);

    if ($qty <= 0) {
        die("Jumlah pesanan untuk $name tidak valid.");
    }

    $stmt = $conn->prepare("SELECT id, harga, stok FROM menu WHERE nama = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Menu $name tidak ditemukan.");
    }

    $row = $result->fetch_assoc();

    if ($row['stok'] < $qty) {
        die("Stok $name tidak cukup. Tersisa: " . $row['stok']);
    }

    $total += $row['harga'] * $qty;
    $menus[] = [
        'menu_id' => $row['id'],
        'nama' => $name,
        'qty' => $qty,
        'harga' => $row['harga']
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->begin_transaction();
    try {
        $tanggal = date('Y-m-d H:i:s');
        foreach ($menus as $menu) {
            $stmt = $conn->prepare("UPDATE menu SET stok = stok - ? WHERE id = ?");
            $stmt->bind_param("ii", $menu['qty'], $menu['menu_id']);
            $stmt->execute();

            $total_harga = $menu['harga'] * $menu['qty'];
            $stmt = $conn->prepare("INSERT INTO transaksi (menu_id, quantity, total_harga, tanggal) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $menu['menu_id'], $menu['qty'], $total_harga, $tanggal);
            $stmt->execute();
        }

        $conn->commit();
        header("Location: sukses.php?total=$total");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Terjadi kesalahan saat memproses pembayaran: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bayar Pesanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      * {
        box-sizing: border-box;
        font-family: 'Segoe UI', sans-serif;
      }
      body {
        margin: 0;
        padding: 20px;
        background-color: #f5f9ff;
        color: #333;
      }
      .container {
        max-width: 600px;
        margin: auto;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      }
      h2 {
        color: #007BFF;
        text-align: center;
      }
      ul {
        padding: 0;
        list-style: none;
        margin-bottom: 20px;
      }
      li {
        padding: 10px;
        border-bottom: 1px solid #eee;
      }
      strong {
        font-size: 18px;
      }
      form {
        text-align: center;
      }
      button {
        background-color: #007BFF;
        color: white;
        border: none;
        padding: 12px 24px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
      }
      button:hover {
        background-color: #0056b3;
      }

      @media (max-width: 600px) {
        body {
          padding: 10px;
        }
        .container {
          padding: 15px;
        }
        button {
          width: 100%;
        }
      }
    </style>
</head>
<body>
  <div class="container">
    <h2>Detail Pesanan</h2>
    <ul>
      <?php foreach ($menus as $menu): ?>
        <li><?= htmlspecialchars($menu['nama']) ?> x <?= $menu['qty'] ?> = Rp <?= number_format($menu['harga'] * $menu['qty'], 0, ',', '.') ?></li>
      <?php endforeach; ?>
    </ul>
    <p><strong>Total: Rp <?= number_format($total, 0, ',', '.') ?></strong></p>
    <form method="POST">
      <button type="submit">Bayar Sekarang</button>
    </form>
  </div>
</body>
</html>

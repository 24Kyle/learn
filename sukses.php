<?php
$total = isset($_GET['total']) ? intval($_GET['total']) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran Berhasil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #e8f0ff;
      padding: 30px;
      text-align: center;
    }
    .card {
      background: white;
      padding: 30px;
      border-radius: 12px;
      display: inline-block;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #28a745;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: white;
      background-color: #007BFF;
      padding: 10px 20px;
      border-radius: 8px;
    }
    a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Terima kasih sudah membayar!</h2>
    <p>Total yang dibayar: <strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></p>
    <a href="index.php">Kembali ke halaman utama</a>
  </div>
</body>
</html>

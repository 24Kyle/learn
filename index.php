<?php
$msg = '';
$err = '';
$conn = new mysqli('localhost', 'root', '', 'kantin');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, nama, harga, stok FROM menu";
$result = $conn->query($sql);

$menus = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menus[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website Kantin Sekolah</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Kantin Sekolah</h1>
    <nav>
      <a href="#about">About Kantin</a>
      <a href="#cafetaria">Cafetaria List</a>
      <a href="#buy">How to Buy</a>
      <a href="#contact">Contact Us</a>
    </nav>
  </header>

  <section id="about">
    <h2>About Kantin</h2>
    <img src="image/kantin.jpg" alt="Logo Kantin">
    <video src="image/vidkantin.mp4" controls></video>
    <p>Kantin sekolah kami menyediakan berbagai makanan dan minuman sehat dengan harga terjangkau. Makanan yang dijual berasal dari kantin resmi sekolah dan selalu terjaga kebersihannya.</p>
  </section>

  <section id="cafetaria">
    <h2>Cafetaria List</h2>
    <div class="kantin">
      <h3>Kantin Ibu Rika</h3>
      <img src="image/kantin1.jpeg" alt="Kantin Ibu Rika">
      <div class="menu-item">Batagor - Rp12.000 <img src="image/batagor.jpg"></div>
      <div class="menu-item">Es Teh - Rp5.000 <img src="image/es-teh.jpg"></div>
    </div>
    <div class="kantin">
      <h3>Kantin Batagor Mas Riki</h3>
      <img src="image/kantin2.jpeg" alt="Kantin Mas Riki">
      <div class="menu-item">Mie - Rp6.000 <img src="image/indomie.jpg"></div>
      <div class="menu-item">Mineral - Rp3.000 <img src="image/mineral.jpg"></div>
    </div>
  </section>

  <section id="buy">
    <h2>How to Buy</h2>
    <form id="buyForm">
      <div class="menu-container">
        <?php foreach ($menus as $menu): ?>
        <div class="menu-item">
          <label>
            <input type="checkbox" class="menu-checkbox" 
                   data-price="<?= $menu['harga']; ?>" 
                   data-stock="<?= $menu['stok']; ?>" 
                   name="menu[]" 
                   value="<?= htmlspecialchars($menu['nama']); ?>" 
            >
            <?php
              $gambar = strtolower(str_replace(' ', '-', $menu['nama'])) . '.jpg';
            ?>
            <img src="image/<?= $gambar; ?>" alt="<?= htmlspecialchars($menu['nama']); ?>">
            <div>
              <div><strong><?= htmlspecialchars($menu['nama']); ?></strong></div>
              <div>Rp <?= number_format($menu['harga']); ?></div>
              <div><small>Stok: <span class="stock-count"><?= $menu['stok']; ?></span></small></div>
            </div>
          </label>
          <input type="number" class="qty-input" 
                 name="qty-<?= strtolower(str_replace(' ', '-', $menu['nama'])); ?>" 
                 value="1" min="1" max="<?= $menu['stok']; ?>" 
          >
        </div>
        <?php endforeach; ?>
      </div>
      <p>
        <strong>Total Harga: <span id="total">Rp0</span></strong>
      </p>
      <div>
        <button type="button" onclick="generateQR()">Bayar (QR Dummy)</button>
      </div>
      <div id="qr"></div>
    </form>
  </section>

  <section id="contact">
    <h2>Contact Me</h2>
    <form method="POST" action="simpan_pesan.php" id="contactForm">
      <input type="text" name="nama" placeholder="Nama Anda" required>
      <input type="email" name="email" placeholder="Email Anda" required>
      <textarea name="pesan" placeholder="Pesan Anda" rows="5" required></textarea>
      <button type="submit" name="submit_pesan">Kirim Pesan</button>
    </form>

    <?php if($msg) echo '<p style="color:green; margin-top:10px;">'.$msg.'</p>'; ?>
    <?php if($err) echo '<p style="color:red; margin-top:10px;">'.$err.'</p>'; ?>
  </section>

  <footer>
    <p>&copy; 2025 Website Kantin oleh Ariiq Riyadi</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <script>
    const checkboxes = document.querySelectorAll('.menu-checkbox');
    const qtyInputs = document.querySelectorAll('.qty-input');

    checkboxes.forEach(cb => {
      cb.addEventListener('change', () => {
        const qtyInput = cb.closest('.menu-item').querySelector('.qty-input');
        qtyInput.disabled = !cb.checked;
        if (!cb.checked) qtyInput.value = 1;
        updateTotal();
      });
    });

    function updateTotal() {
      let total = 0;
      checkboxes.forEach(cb => {
        if (cb.checked) {
          const price = parseInt(cb.getAttribute('data-price'));
          const stock = parseInt(cb.getAttribute('data-stock'));
          const qtyInput = cb.closest('.menu-item').querySelector('.qty-input');
          let qty = parseInt(qtyInput.value);
          if (qty > stock) {
            qty = stock;
            qtyInput.value = qty;
          }
          total += price * qty;
        }
      });
      document.getElementById('total').innerText = 'Rp' + total.toLocaleString('id-ID');
    }

    qtyInputs.forEach(input => {
      input.addEventListener('input', updateTotal);
    });

    function generateQR() {
      const qrDiv = document.getElementById('qr');
      let orderParts = [];
      checkboxes.forEach(cb => {
        if (cb.checked) {
          const nama = cb.value;
          const qtyInput = cb.closest('.menu-item').querySelector('.qty-input');
          const qty = parseInt(qtyInput.value);
          orderParts.push(`${encodeURIComponent(nama)}:${qty}`);
        }
      });

      if (orderParts.length === 0) {
        alert('Pilih minimal 1 menu yang ingin dibeli!');
        return;
      }

      const orderString = orderParts.join(',');
      const totalRaw = document.getElementById('total').innerText.replace('Rp', '').replace(/\./g, '');
      const payUrl = `http://192.168.174.53/web_kantin_sederhana/pay.php?order=${orderString}&total=${totalRaw}`;

      qrDiv.innerHTML = `
        <p>Scan untuk bayar sebesar <strong>Rp${parseInt(totalRaw).toLocaleString('id-ID')}</strong></p>
        <div id="qrcode" style="margin-top:10px;"></div>
      `;

      setTimeout(() => {
        new QRCode(document.getElementById("qrcode"), {
          text: payUrl,
          width: 200,
          height: 200,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
      }, 100);
    }
  </script>
</body>
</html>

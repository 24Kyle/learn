<?php
$conn = new mysqli('localhost', 'root', '', 'kantin');
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_POST['submit_pesan'])) {
    $nama = $conn->real_escape_string(trim($_POST['nama']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $pesan = $conn->real_escape_string(trim($_POST['pesan']));
    $tanggal = date('Y-m-d H:i:s');

    $sql = "INSERT INTO pesan (nama, email, pesan, tanggal) VALUES ('$nama', '$email', '$pesan', '$tanggal')";
    if ($conn->query($sql) === TRUE) {
        // Bisa redirect kembali ke halaman utama dengan pesan sukses
        header("Location: index.php?msg=success");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Form tidak dikirim dengan benar.";
}
?>

-- Buat database
CREATE DATABASE IF NOT EXISTS kantin;
USE kantin;

-- Tabel: menu
CREATE TABLE `menu` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `harga` INT(11) NOT NULL,
  `stok` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: pesan
CREATE TABLE `pesan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `pesan` TEXT NOT NULL,
  `tanggal` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel: transaksi
CREATE TABLE `transaksi` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `menu_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `total_harga` INT(11) NOT NULL,
  `tanggal` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO menu (id, nama, harga, stok) VALUES
(1, 'Mie', 7000, 10),
(2, 'Batagor', 10000, 10),
(3, 'Es Teh', 5000, 10),
(4, 'Mineral', 3000, 10);
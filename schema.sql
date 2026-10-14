CREATE DATABASE IF NOT EXISTS inventaris_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS suppliers;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT NOT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id)
        REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id)
        REFERENCES suppliers(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
('Aksesoris'), ('Komponen PC'), ('Penyimpanan'), ('Periferal'), ('Jaringan');

INSERT INTO suppliers (name, phone) VALUES
('PT Sumber Teknologi', '081234567801'),
('CV Digital Jaya', '081234567802'),
('Medan Computer Store', '081234567803'),
('PT Nusantara Hardware', '081234567804'),
('Global Network Supply', '081234567805');

INSERT INTO products (name, category_id, supplier_id, price, stock) VALUES
('Mouse Wireless', 1, 1, 125000, 25),
('Keyboard Mechanical', 4, 2, 450000, 12),
('SSD NVMe 1TB', 3, 3, 950000, 8),
('RAM DDR4 16GB', 2, 4, 520000, 15),
('Router Wi-Fi', 5, 5, 375000, 10);

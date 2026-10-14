<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config/database.php';
function e(mixed $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$errors = [];
$name = ''; $categoryId = ''; $supplierId = ''; $price = ''; $stock = '';
try {
    $pdo = Database::getInstance()->getConnection();
    $categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();
    $suppliers = $pdo->query('SELECT id, name FROM suppliers ORDER BY name')->fetchAll();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim((string)($_POST['name'] ?? ''));
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT) ?: '';
        $supplierId = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT) ?: '';
        $priceInput = trim((string)($_POST['price'] ?? ''));
        $stockInput = trim((string)($_POST['stock'] ?? ''));
        $price = is_numeric($priceInput) ? (float)$priceInput : -1;
        $stock = filter_var($stockInput, FILTER_VALIDATE_INT);
        if ($name === '' || mb_strlen($name) > 150) $errors[] = 'Nama wajib diisi (maksimal 150 karakter).';
        if (!$categoryId) $errors[] = 'Pilih kategori yang valid.';
        if (!$supplierId) $errors[] = 'Pilih supplier yang valid.';
        if (!is_numeric($priceInput) || $price < 0) $errors[] = 'Harga harus berupa angka nol atau lebih.';
        if ($stock === false || $stock < 0) $errors[] = 'Stok harus berupa bilangan bulat nol atau lebih.';
        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO products (name, category_id, supplier_id, price, stock) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, (int)$categoryId, (int)$supplierId, $price, (int)$stock]);
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Produk berhasil ditambahkan.'];
            header('Location: index.php'); exit;
        }
    }
} catch (Throwable $ex) { error_log($ex->getMessage()); $errors[] = 'Terjadi kesalahan. Periksa koneksi database dan data pilihan.'; }
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Tambah Produk</title><link rel="stylesheet" href="assets/style.css"></head>
<body><header class="topbar"><a class="brand brand-link" href="index.php"><span class="brand-icon">▦</span><div><strong>Inventaris</strong><small>Kembali ke daftar</small></div></a><a class="button light" href="index.php">← Kembali</a></header>
<main class="container narrow"><section class="hero"><p class="eyebrow">DATA BARU</p><h1>Tambah Produk</h1><p class="muted">Lengkapi informasi produk di bawah ini.</p></section>
<section class="panel form-panel">
<?php if ($errors): ?><div class="alert error"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="form-grid">
<label class="full">Nama produk<input name="name" maxlength="150" required value="<?= e($name) ?>" placeholder="Contoh: Mouse Wireless"></label>
<label>Kategori<select name="category_id" required><option value="">Pilih kategori</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$categoryId === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></label>
<label>Supplier<select name="supplier_id" required><option value="">Pilih supplier</option><?php foreach ($suppliers as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (string)$supplierId === (string)$s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select></label>
<label>Harga (Rp)<input type="number" name="price" min="0" step="0.01" required value="<?= e($price === -1 ? '' : $price) ?>" placeholder="125000"></label>
<label>Stok<input type="number" name="stock" min="0" step="1" required value="<?= e($stock === false ? '' : $stock) ?>" placeholder="10"></label>
<div class="form-actions full"><a class="button light" href="index.php">Batal</a><button class="button primary" type="submit">Simpan Produk</button></div>
</form></section></main></body></html>

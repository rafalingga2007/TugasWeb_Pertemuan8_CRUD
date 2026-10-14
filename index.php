<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/config/database.php';

function e(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
try {
    $pdo = Database::getInstance()->getConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            flash('error', 'ID produk tidak valid.');
        } else {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
                $stmt->execute([$id]);
                $pdo->commit();
                flash($stmt->rowCount() ? 'success' : 'error',
                    $stmt->rowCount() ? 'Produk berhasil dihapus.' : 'Produk tidak ditemukan.');
            } catch (Throwable $ex) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                throw $ex;
            }
        }
        header('Location: index.php'); exit;
    }

    $search = trim((string)($_GET['q'] ?? ''));
    $sql = 'SELECT p.id, p.name, p.price, p.stock, c.name AS category_name, s.name AS supplier_name
            FROM products p
            INNER JOIN categories c ON p.category_id = c.id
            INNER JOIN suppliers s ON p.supplier_id = s.id';
    if ($search !== '') {
        $sql .= ' WHERE p.name LIKE ? OR c.name LIKE ? OR s.name LIKE ?';
        $stmt = $pdo->prepare($sql . ' ORDER BY p.id DESC');
        $term = '%' . $search . '%';
        $stmt->execute([$term, $term, $term]);
    } else {
        $stmt = $pdo->query($sql . ' ORDER BY p.id DESC');
    }
    $products = $stmt->fetchAll();
    $flashMessage = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
} catch (Throwable $ex) {
    error_log($ex->getMessage());
    http_response_code(500);
    $pageError = 'Tidak dapat memuat data. Pastikan MySQL aktif dan database sudah diimpor.';
    $products = [];
    $flashMessage = null;
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventaris Barang</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand"><span class="brand-icon">▦</span><div><strong>Inventaris</strong><small>Panel pengelolaan barang</small></div></div>
  <a class="button primary" href="create.php">＋ Tambah Produk</a>
</header>
<main class="container">
  <section class="hero">
    <p class="eyebrow">MANAJEMEN PERSEDIAAN</p>
    <h1>Daftar Produk</h1>
    <p class="muted">Kelola produk, kategori, supplier, harga, dan stok dalam satu tempat.</p>
  </section>
  <?php if (!empty($pageError)): ?><div class="alert error"><?= e($pageError) ?></div><?php endif; ?>
  <?php if ($flashMessage): ?><div class="alert <?= e($flashMessage['type']) ?>"><?= e($flashMessage['message']) ?></div><?php endif; ?>
  <section class="panel">
    <div class="panel-head">
      <div><h2>Semua barang</h2><p class="muted"><?= count($products) ?> produk ditampilkan</p></div>
      <form class="search" method="get">
        <input type="search" name="q" value="<?= e($search ?? '') ?>" placeholder="Cari produk, kategori, supplier...">
        <button class="button dark" type="submit">Cari</button>
        <?php if (!empty($search)): ?><a class="button light" href="index.php">Reset</a><?php endif; ?>
      </form>
    </div>
    <div class="table-wrap"><table>
      <thead><tr><th>Produk</th><th>Kategori</th><th>Supplier</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php if (!$products): ?><tr><td colspan="6" class="empty">Belum ada data yang cocok.</td></tr><?php endif; ?>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><strong><?= e($p['name']) ?></strong><small class="subline">ID #<?= (int)$p['id'] ?></small></td>
          <td><span class="tag"><?= e($p['category_name']) ?></span></td>
          <td><?= e($p['supplier_name']) ?></td>
          <td class="price">Rp <?= number_format((float)$p['price'], 0, ',', '.') ?></td>
          <td><span class="stock <?= (int)$p['stock'] < 5 ? 'low' : '' ?>"><?= (int)$p['stock'] ?></span></td>
          <td><div class="actions"><a class="button small light" href="edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
            <form method="post" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
              <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <button class="button small danger" type="submit">Hapus</button>
            </form></div></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
  <footer>CRUD Inventaris · PHP Native + PDO + MySQL</footer>
</main>
</body>
</html>

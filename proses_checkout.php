<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
include 'koneksi.php';
header('Content-Type: application/json');

$raw = file_get_contents('php://input');
file_put_contents('debug_log.txt', date('H:i:s') . " | " . $raw . "\n", FILE_APPEND);

$data = json_decode($raw, true);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Koneksi DB gagal']);
    exit;
}
if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
    exit;
}

$items         = $data['items'];
$total_price   = intval($data['total']);
$shipping_cost = intval($data['ongkir']);
$pembayaran    = $data['pembayaran'] ?? '';
$users_id      = $_SESSION['user_id'] ?? NULL;

$kode_order = 'BTF-' . strtoupper(substr(md5(uniqid()), 0, 8));

// ── INSERT orders — status langsung 'Dibayar' ─────────────────
$stmtOrder = $conn->prepare("
    INSERT INTO orders (users_id, total_price, shipping_cost, status, kode_order)
    VALUES (?, ?, ?, 'Dibayar', ?)
");
if (!$stmtOrder) {
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $conn->error]);
    exit;
}
$stmtOrder->bind_param("idds", $users_id, $total_price, $shipping_cost, $kode_order);
if (!$stmtOrder->execute()) {
    echo json_encode(['success' => false, 'message' => 'Gagal simpan order: ' . $stmtOrder->error]);
    exit;
}
$order_id = $conn->insert_id;
$stmtOrder->close();

// ── INSERT order_items ────────────────────────────────────────
$stmtItem = $conn->prepare("
    INSERT INTO order_items (order_id, product_id, quantity, price)
    VALUES (?, ?, ?, ?)
");
if (!$stmtItem) {
    echo json_encode(['success' => false, 'message' => 'Query item error: ' . $conn->error]);
    exit;
}

// Cek apakah sold_count ada
$cekKolom     = $conn->query("SHOW COLUMNS FROM products LIKE 'sold_count'");
$hasSoldCount = ($cekKolom && $cekKolom->num_rows > 0);
if ($hasSoldCount) {
    $stmtUpdate = $conn->prepare("UPDATE products SET sold_count = sold_count + ? WHERE id_product = ?");
}

foreach ($items as $item) {
    $product_id = intval($item['id'] ?? $item['id_product'] ?? 0);
    $qty        = intval($item['qty']);
    $price      = intval($item['price']);
    if ($product_id === 0) continue;

    $stmtItem->bind_param("iiii", $order_id, $product_id, $qty, $price);
    $stmtItem->execute();

    if ($hasSoldCount && isset($stmtUpdate)) {
        $stmtUpdate->bind_param("ii", $qty, $product_id);
        $stmtUpdate->execute();
    }
}

$stmtItem->close();
if (isset($stmtUpdate)) $stmtUpdate->close();

echo json_encode(['success' => true, 'kode' => $kode_order]);
?>
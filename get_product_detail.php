<?php
// get_product_detail.php — ambil stok & deskripsi terbaru
include 'koneksi.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT stock, description FROM products WHERE id_product = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    echo json_encode([
        'success'     => true,
        'stock'       => intval($row['stock']),
        'description' => $row['description'] ?? ''
    ]);
} else {
    echo json_encode(['success' => false]);
}
?>
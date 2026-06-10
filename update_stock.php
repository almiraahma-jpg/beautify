<?php
// update_stock.php — API update stok + deskripsi produk
session_start();
include 'koneksi.php';
header('Content-Type: application/json');

// Hanya admin yang boleh update
if (($_SESSION['user_role'] ?? '') !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$product_id = intval($data['id']          ?? 0);
$new_stock  = intval($data['stock']       ?? -1);
$new_desc   = trim($data['description']   ?? '');

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID produk tidak valid']);
    exit;
}

// Update stok saja, atau stok + deskripsi
if ($new_stock >= 0 && $new_desc !== '') {
    $stmt = $conn->prepare("UPDATE products SET stock = ?, description = ? WHERE id_product = ?");
    $stmt->bind_param("isi", $new_stock, $new_desc, $product_id);
} elseif ($new_stock >= 0) {
    $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id_product = ?");
    $stmt->bind_param("ii", $new_stock, $product_id);
} elseif ($new_desc !== '') {
    $stmt = $conn->prepare("UPDATE products SET description = ? WHERE id_product = ?");
    $stmt->bind_param("si", $new_desc, $product_id);
} else {
    echo json_encode(['success' => false, 'message' => 'Tidak ada data yang diubah']);
    exit;
}

if ($stmt->execute()) {
    // Ambil data terbaru
    $res  = $conn->query("SELECT stock, description FROM products WHERE id_product = $product_id");
    $row  = $res->fetch_assoc();
    echo json_encode([
        'success'     => true,
        'stock'       => intval($row['stock']),
        'description' => $row['description'] ?? ''
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update: ' . $stmt->error]);
}
?>